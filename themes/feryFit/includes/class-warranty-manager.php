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
	}

	public function handle_form_submission( $request ) {
		global $wpdb;

		$order_number = sanitize_text_field( $request->get_param( 'order_number' ) );
		$email = sanitize_email( $request->get_param( 'email' ) );
		$name = sanitize_text_field( $request->get_param( 'name' ) );
		$country = sanitize_text_field( $request->get_param( 'country' ) );
		$rating = intval( $request->get_param( 'rating' ) );
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
				'rating' => $rating,
				'receive_updates' => $receive_updates,
				'future_tests' => $future_tests,
			),
			array(
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
		$offset = ( $page - 1 ) * $per_page;

		$where_clause = '';
		if ( ! empty( $search ) ) {
			$search = '%' . $wpdb->esc_like( $search ) . '%';
			$where_clause = $wpdb->prepare(
				" WHERE order_number LIKE %s OR email LIKE %s OR name LIKE %s OR country LIKE %s",
				$search,
				$search,
				$search,
				$search
			);
		}

		$total = $wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name $where_clause" );
		$applications = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $this->table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d",
				$per_page,
				$offset
			),
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
			'dashicons-clipboard-list',
			6
		);
	}

	public function enqueue_admin_scripts( $hook_suffix ) {
		if ( $hook_suffix !== 'toplevel_page_feryfit-warranty' ) {
			return;
		}

		wp_enqueue_script(
			'feryfit-warranty-admin',
			get_template_directory_uri() . '/assets/js/warranty-admin.js',
			array( 'jquery' ),
			'1.0.0',
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

			<div class="feryfit-search-box">
				<input type="text" id="feryfit-search-input" placeholder="搜索订单号、邮箱、姓名或国家..." />
				<button id="feryfit-search-btn" class="button">搜索</button>
				<button id="feryfit-reset-btn" class="button">重置</button>
			</div>

			<div id="feryfit-warranty-container">
				<div class="loading">加载中...</div>
			</div>
		</div>
		<style>
			.feryfit-search-box {
				margin: 20px 0;
				padding: 15px;
				background-color: #fff;
				border: 1px solid #ddd;
				border-radius: 4px;
				display: flex;
				gap: 10px;
			}
			.feryfit-search-box input {
				padding: 8px 12px;
				width: 400px;
				margin-right: 10px;
				border: 1px solid #ddd;
				border-radius: 4px;
			}
			.feryfit-search-box button {
				padding: 8px 16px;
				margin-right: 5px;
			}
			.feryfit-warranty-table {
				width: 100%;
				border-collapse: collapse;
				margin-top: 20px;
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
			}
			.feryfit-warranty-pagination a {
				display: inline-block;
				padding: 8px 16px;
				text-decoration: none;
				border: 1px solid #ddd;
				margin: 0 4px;
				border-radius: 4px;
			}
			.feryfit-warranty-pagination a.current {
				background-color: #c73e1d;
				color: white;
				border-color: #c73e1d;
			}
			.feryfit-warranty-pagination a:hover:not(.current) {
				background-color: #f1f1f1;
			}
			.star-rating {
				color: #f5a623;
			}
			.btn-delete {
				background-color: #dc3232;
				color: white;
				border: none;
				padding: 5px 10px;
				border-radius: 3px;
				cursor: pointer;
				font-size: 13px;
			}
			.btn-delete:hover {
				background-color: #c0392b;
			}
			.loading {
				text-align: center;
				padding: 40px;
				font-size: 16px;
			}
			.empty-message {
				text-align: center;
				padding: 40px;
				color: #666;
			}
			.checkbox-yes {
				color: #2ecc71;
				font-weight: bold;
			}
			.checkbox-no {
				color: #95a5a6;
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