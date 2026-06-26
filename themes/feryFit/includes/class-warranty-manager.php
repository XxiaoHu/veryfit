<?php

class FeryFit_Warranty_Manager {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'feryfit_warranty_applications';

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		register_activation_hook( __FILE__, array( $this, 'create_table' ) );
	}

	public function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			order_number VARCHAR(100) NOT NULL,
			email VARCHAR(255) NOT NULL,
			name VARCHAR(255) DEFAULT '',
			country VARCHAR(100) DEFAULT '',
			language VARCHAR(10) DEFAULT '',
			rating TINYINT(1) DEFAULT 0,
			receive_updates TINYINT(1) DEFAULT 0,
			future_tests TINYINT(1) DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function register_rest_routes() {
		register_rest_route( 'feryfit/v1', '/submit-warranty', array(
			'methods' => 'POST',
			'callback' => array( $this, 'handle_form_submission' ),
			'permission_callback' => array( $this, 'verify_warranty_nonce' ),
		) );

		register_rest_route( 'feryfit/v1', '/warranty-nonce', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_warranty_nonce' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( 'feryfit/v1', '/warranty-applications', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_applications' ),
			'permission_callback' => array( $this, 'check_permission' ),
		) );

		register_rest_route( 'feryfit/v1', '/warranty-applications/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_application' ),
			'permission_callback' => array( $this, 'check_permission' ),
		) );

		register_rest_route( 'feryfit/v1', '/warranty-applications/export', array(
			'methods' => 'GET',
			'callback' => array( $this, 'export_applications' ),
			'permission_callback' => array( $this, 'check_permission' ),
		) );

		register_rest_route( 'feryfit/v1', '/warranty-languages', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_languages' ),
			'permission_callback' => array( $this, 'check_permission' ),
		) );
	}

	public function verify_warranty_nonce( $request ) {
		$nonce = $request->get_header( 'x_feryfit_nonce' );
		if ( ! $nonce ) {
			$nonce = $request->get_header( 'x-feryfit-nonce' );
		}
		if ( ! $nonce ) {
			$nonce = $request->get_param( '_wpnonce' );
		}
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'feryfit_warranty_nonce' ) ) {
			return new WP_Error( 'invalid_nonce', 'Invalid nonce', array( 'status' => 403 ) );
		}
		return true;
	}

	public function get_warranty_nonce() {
		return array( 'nonce' => wp_create_nonce( 'feryfit_warranty_nonce' ) );
	}

	public function handle_form_submission( $request ) {
		global $wpdb;

		$user_ip = feryfit_get_client_ip();
		if ( ! feryfit_rate_limit_check( 'warranty:' . $user_ip, 3, MINUTE_IN_SECONDS ) ) {
			return new WP_Error( 'rate_limit', 'Too many requests. Please try again later.', array( 'status' => 429 ) );
		}

		$honeypot = $request->get_param( 'website' );
		if ( ! empty( $honeypot ) ) {
			return new WP_Error( 'spam_detected', 'Submission rejected.', array( 'status' => 403 ) );
		}

		$order_number = substr( sanitize_text_field( $request->get_param( 'order_number' ) ), 0, 100 );
		$email = substr( sanitize_email( $request->get_param( 'email' ) ), 0, 255 );
		$name = substr( sanitize_text_field( $request->get_param( 'name' ) ), 0, 255 );
		$country = substr( sanitize_text_field( $request->get_param( 'country' ) ), 0, 100 );
		$language = substr( sanitize_text_field( $request->get_param( 'language' ) ), 0, 10 );
		$rating = max( 0, min( 5, intval( $request->get_param( 'rating' ) ) ) );
		$receive_updates = $request->get_param( 'receive_updates' ) ? 1 : 0;
		$future_tests = $request->get_param( 'future_tests' ) ? 1 : 0;

		if ( empty( $order_number ) || empty( $email ) ) {
			return new WP_Error( 'missing_fields', 'Order Number and Email are required', array( 'status' => 400 ) );
		}

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', 'Invalid email address', array( 'status' => 400 ) );
		}

		$result = $wpdb->insert(
			$this->table_name,
			array(
				'order_number' => $order_number,
				'email' => $email,
				'name' => $name,
				'country' => $country,
				'language' => $language,
				'rating' => $rating,
				'receive_updates' => $receive_updates,
				'future_tests' => $future_tests,
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
			)
		);

		if ( $result ) {
			return array( 'success' => true, 'message' => 'Application submitted successfully!' );
		} else {
			return new WP_Error( 'db_error', 'Failed to save application', array( 'status' => 500 ) );
		}
	}

	public function get_applications( $request ) {
		global $wpdb;

		$page = intval( $request->get_param( 'page' ) ) ?: 1;
		$per_page = intval( $request->get_param( 'per_page' ) ) ?: 10;
		$search = sanitize_text_field( $request->get_param( 'search' ) );
		$language = sanitize_text_field( $request->get_param( 'language' ) );
		$date_from = sanitize_text_field( $request->get_param( 'date_from' ) );
		$date_to = sanitize_text_field( $request->get_param( 'date_to' ) );
		$offset = ( $page - 1 ) * $per_page;

		$where_clauses = array();
		$where_values = array();

		if ( ! empty( $search ) ) {
			$search_like = '%' . $wpdb->esc_like( $search ) . '%';
			$where_clauses[] = '(order_number LIKE %s OR email LIKE %s OR name LIKE %s OR country LIKE %s)';
			$where_values[] = $search_like;
			$where_values[] = $search_like;
			$where_values[] = $search_like;
			$where_values[] = $search_like;
		}

		if ( ! empty( $language ) ) {
			$where_clauses[] = 'language = %s';
			$where_values[] = $language;
		}

		if ( ! empty( $date_from ) ) {
			$where_clauses[] = 'created_at >= %s';
			$where_values[] = $date_from . ' 00:00:00';
		}

		if ( ! empty( $date_to ) ) {
			$where_clauses[] = 'created_at <= %s';
			$where_values[] = $date_to . ' 23:59:59';
		}

		$where_clause = '';
		if ( ! empty( $where_clauses ) ) {
			$where_clause = ' WHERE ' . implode( ' AND ', $where_clauses );
			if ( ! empty( $where_values ) ) {
				$where_clause = $wpdb->prepare( $where_clause, $where_values );
			}
		}

		$total = $wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name $where_clause" );

		$query = "SELECT * FROM $this->table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$applications = $wpdb->get_results(
			$wpdb->prepare( $query, $per_page, $offset ),
			ARRAY_A
		);

		return array(
			'total' => (int) $total,
			'pages' => ceil( $total / $per_page ),
			'current_page' => $page,
			'data' => $applications,
		);
	}

	public function delete_application( $request ) {
		global $wpdb;

		$id = intval( $request->get_param( 'id' ) );

		if ( ! $id ) {
			return new WP_Error( 'invalid_id', 'Invalid application ID', array( 'status' => 400 ) );
		}

		$result = $wpdb->delete( $this->table_name, array( 'id' => $id ), array( '%d' ) );

		if ( $result ) {
			return array( 'success' => true, 'message' => 'Application deleted successfully' );
		} else {
			return new WP_Error( 'delete_error', 'Failed to delete application', array( 'status' => 500 ) );
		}
	}

	public function export_applications( $request ) {
		global $wpdb;

		$search = sanitize_text_field( $request->get_param( 'search' ) );
		$language = sanitize_text_field( $request->get_param( 'language' ) );
		$date_from = sanitize_text_field( $request->get_param( 'date_from' ) );
		$date_to = sanitize_text_field( $request->get_param( 'date_to' ) );

		$where_clauses = array();
		$where_values = array();

		if ( ! empty( $search ) ) {
			$search_like = '%' . $wpdb->esc_like( $search ) . '%';
			$where_clauses[] = '(order_number LIKE %s OR email LIKE %s OR name LIKE %s OR country LIKE %s)';
			$where_values[] = $search_like;
			$where_values[] = $search_like;
			$where_values[] = $search_like;
			$where_values[] = $search_like;
		}

		if ( ! empty( $language ) ) {
			$where_clauses[] = 'language = %s';
			$where_values[] = $language;
		}

		if ( ! empty( $date_from ) ) {
			$where_clauses[] = 'created_at >= %s';
			$where_values[] = $date_from . ' 00:00:00';
		}

		if ( ! empty( $date_to ) ) {
			$where_clauses[] = 'created_at <= %s';
			$where_values[] = $date_to . ' 23:59:59';
		}

		$where_clause = '';
		if ( ! empty( $where_clauses ) ) {
			$where_clause = ' WHERE ' . implode( ' AND ', $where_clauses );
			if ( ! empty( $where_values ) ) {
				$where_clause = $wpdb->prepare( $where_clause, $where_values );
			}
		}

		$applications = $wpdb->get_results(
			"SELECT * FROM $this->table_name $where_clause ORDER BY created_at DESC",
			ARRAY_A
		);

		// 设置 CSV 头部
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=warranty-applications-' . date( 'Y-m-d' ) . '.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// 输出 UTF-8 BOM (让 Excel 正确识别 UTF-8)
		echo "\xEF\xBB\xBF";

		// 打开输出流
		$output = fopen( 'php://output', 'w' );

		// CSV 表头
		$headers = array( 'ID', '订单号', '邮箱', '姓名', '国家', '语言', '评分', '接收更新', '创建时间' );
		fputcsv( $output, $headers );

		// 输出数据
		foreach ( $applications as $app ) {
			$row = array_map( 'feryfit_sanitize_csv_cell', array(
				$app['id'],
				$app['order_number'],
				$app['email'],
				$app['name'],
				$app['country'],
				$app['language'],
				$app['rating'],
				$app['receive_updates'] ? '是' : '否',
				$app['created_at'],
			) );
			fputcsv( $output, $row );
		}

		fclose( $output );
		exit;
	}

	public function get_languages( $request ) {
		global $wpdb;

		$languages = $wpdb->get_col( "SELECT DISTINCT language FROM $this->table_name WHERE language != '' ORDER BY language ASC" );

		return array(
			'languages' => $languages,
		);
	}

	public function check_permission() {
		return current_user_can( 'manage_options' );
	}

	public function add_admin_menu() {
		add_menu_page(
			'保修申请管理',
			'保修申请',
			'manage_options',
			'feryfit-warranty',
			array( $this, 'render_admin_page' ),
			'dashicons-email',
			6
		);
	}

	public function enqueue_admin_scripts( $hook_suffix ) {
		if ( $hook_suffix !== 'toplevel_page_feryfit-warranty' ) {
			return;
		}

		// 加载 jQuery UI Datepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css' );

		wp_enqueue_script(
			'feryfit-warranty-admin',
			get_template_directory_uri() . '/assets/js/warranty-admin.js',
			array( 'jquery', 'jquery-ui-datepicker' ),
			'1.0.1',
			true
		);

		wp_localize_script( 'feryfit-warranty-admin', 'feryfitWarranty', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'rest_url' => get_rest_url(),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		) );
	}

	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">保修申请管理</h1>
			<hr class="wp-header-end">

			<div class="feryfit-filter-box">
				<div class="filter-row">
					<div class="filter-item">
						<label>关键词搜索</label>
						<input type="text" id="feryfit-search-input" placeholder="搜索订单号、邮箱、姓名或国家..." />
					</div>
					<div class="filter-item">
						<label>语言</label>
						<select id="feryfit-language-filter">
							<option value="">全部语言</option>
						</select>
					</div>
				</div>
				<div class="filter-row">
					<div class="filter-item">
						<label>开始日期</label>
						<input type="text" id="feryfit-date-from" placeholder="点击选择日期" readonly />
					</div>
					<div class="filter-item">
						<label>结束日期</label>
						<input type="text" id="feryfit-date-to" placeholder="点击选择日期" readonly />
					</div>
				</div>
				<div class="filter-actions">
					<button id="feryfit-search-btn" class="button button-primary">筛选</button>
					<button id="feryfit-reset-btn" class="button">重置</button>
					<button id="feryfit-export-btn" class="button button-secondary">导出数据</button>
				</div>
			</div>

			<div id="feryfit-warranty-container">
				<div class="loading">加载中...</div>
			</div>
		</div>
		<style>
			.feryfit-filter-box {
				margin: 20px 0;
				padding: 20px;
				background-color: #fff;
				border: 1px solid #ddd;
				border-radius: 4px;
			}
			.filter-row {
				display: flex;
				gap: 20px;
				margin-bottom: 15px;
				flex-wrap: wrap;
			}
			.filter-item {
				flex: 1;
				min-width: 200px;
			}
			.filter-item label {
				display: block;
				margin-bottom: 5px;
				font-weight: 600;
				color: #23282d;
			}
			.filter-item input,
			.filter-item select {
				width: 100%;
				padding: 8px 12px;
				border: 1px solid #ddd;
				border-radius: 4px;
				font-size: 14px;
			}
			.filter-item input[type="text"]:read-only {
				background-color: #f9f9f9;
				cursor: pointer;
			}
			.filter-item input:focus,
			.filter-item select:focus {
				border-color: #c73e1d;
				outline: none;
				box-shadow: 0 0 0 1px #c73e1d;
			}
			.filter-actions {
				display: flex;
				gap: 10px;
				margin-top: 10px;
			}
			.filter-actions button {
				padding: 8px 20px;
				font-size: 14px;
			}
			.result-info {
				margin: 15px 0 10px;
				padding: 10px 15px;
				background-color: #f0f0f1;
				border-left: 4px solid #c73e1d;
				font-weight: 600;
			}
			.feryfit-warranty-table {
				width: 100%;
				border-collapse: collapse;
				margin-top: 20px;
				background-color: #fff;
			}
			.feryfit-warranty-table th,
			.feryfit-warranty-table td {
				border: 1px solid #ddd;
				padding: 12px;
				text-align: left;
			}
			.feryfit-warranty-table th {
				background-color: #f1f1f1;
				font-weight: 600;
				position: sticky;
				top: 32px;
				z-index: 10;
			}
			.feryfit-warranty-table tr:nth-child(even) {
				background-color: #f9f9f9;
			}
			.feryfit-warranty-table tr:hover {
				background-color: #f1f1f1;
			}
			.feryfit-warranty-pagination {
				margin-top: 20px;
				text-align: center;
				padding: 15px 0;
			}
			.feryfit-warranty-pagination a,
			.feryfit-warranty-pagination .page-dots {
				display: inline-block;
				padding: 8px 16px;
				text-decoration: none;
				border: 1px solid #ddd;
				margin: 0 4px;
				border-radius: 4px;
				background-color: #fff;
				color: #333;
				transition: all 0.2s;
			}
			.feryfit-warranty-pagination .page-dots {
				border: none;
				cursor: default;
			}
			.feryfit-warranty-pagination a.current {
				background-color: #c73e1d;
				color: white;
				border-color: #c73e1d;
			}
			.feryfit-warranty-pagination a:hover:not(.current) {
				background-color: #f1f1f1;
				border-color: #999;
			}
			.star-rating {
				color: #f5a623;
				font-size: 16px;
			}
			.btn-delete {
				background-color: #dc3232;
				color: white;
				border: none;
				padding: 5px 10px;
				border-radius: 3px;
				cursor: pointer;
				font-size: 13px;
				transition: background-color 0.2s;
			}
			.btn-delete:hover {
				background-color: #c0392b;
			}
			.loading {
				text-align: center;
				padding: 40px;
				font-size: 16px;
				color: #666;
			}
			.loading:before {
				content: "";
				display: inline-block;
				width: 20px;
				height: 20px;
				border: 3px solid #f3f3f3;
				border-top: 3px solid #c73e1d;
				border-radius: 50%;
				animation: spin 1s linear infinite;
				margin-right: 10px;
				vertical-align: middle;
			}
			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
			.empty-message {
				text-align: center;
				padding: 40px;
				color: #666;
				font-size: 15px;
			}
			.checkbox-yes {
				color: #2ecc71;
				font-weight: bold;
			}
			.checkbox-no {
				color: #95a5a6;
			}
			/* jQuery UI Datepicker 样式调整 */
			.ui-datepicker {
				z-index: 9999 !important;
				font-size: 12px;
				width: auto;
			}
			.ui-datepicker table {
				font-size: 11px;
			}
			.ui-datepicker .ui-datepicker-header {
				padding: 4px 0;
			}
			.ui-datepicker .ui-datepicker-title {
				line-height: 1.8em;
			}
			.ui-datepicker td {
				padding: 1px;
			}
			.ui-datepicker td span,
			.ui-datepicker td a {
				padding: 2px;
				text-align: center;
			}
		</style>
		<?php
	}
}

new FeryFit_Warranty_Manager();

// Run table creation on theme activation
function feryfit_create_warranty_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'feryfit_warranty_applications';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		order_number VARCHAR(100) NOT NULL,
		email VARCHAR(255) NOT NULL,
		name VARCHAR(255) DEFAULT '',
		country VARCHAR(100) DEFAULT '',
		language VARCHAR(10) DEFAULT '',
		rating TINYINT(1) DEFAULT 0,
		receive_updates TINYINT(1) DEFAULT 0,
		future_tests TINYINT(1) DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'feryfit_create_warranty_table' );

// Also create table on init if it doesn't exist
function feryfit_check_warranty_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'feryfit_warranty_applications';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		feryfit_create_warranty_table();
	}
}
add_action( 'init', 'feryfit_check_warranty_table' );
