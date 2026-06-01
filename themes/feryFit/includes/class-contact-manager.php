<?php

class FeryFit_Contact_Manager {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'feryfit_contact_messages';

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	public function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			email VARCHAR(255) NOT NULL,
			name VARCHAR(255) NOT NULL,
			message TEXT NOT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function register_rest_routes() {
		register_rest_route( 'feryfit/v1', '/submit-contact', array(
			'methods' => 'POST',
			'callback' => array( $this, 'handle_form_submission' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( 'feryfit/v1', '/contact-messages', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_messages' ),
			'permission_callback' => array( $this, 'check_permission' ),
		) );

		register_rest_route( 'feryfit/v1', '/contact-messages/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_message' ),
			'permission_callback' => array( $this, 'check_permission' ),
		) );
	}

	public function handle_form_submission( $request ) {
		global $wpdb;

		$email = sanitize_email( $request->get_param( 'email' ) );
		$name = sanitize_text_field( $request->get_param( 'name' ) );
		$message = sanitize_textarea_field( $request->get_param( 'message' ) );

		if ( empty( $email ) || empty( $name ) || empty( $message ) ) {
			return new WP_Error( 'missing_fields', 'Email, Name and Message are required', array( 'status' => 400 ) );
		}

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', 'Invalid email address', array( 'status' => 400 ) );
		}

		$result = $wpdb->insert(
			$this->table_name,
			array(
				'email' => $email,
				'name' => $name,
				'message' => $message,
			),
			array(
				'%s',
				'%s',
				'%s',
			)
		);

		if ( $result ) {
			return array( 'success' => true, 'message' => 'Message submitted successfully!' );
		} else {
			return new WP_Error( 'db_error', 'Failed to save message', array( 'status' => 500 ) );
		}
	}

	public function get_messages( $request ) {
		global $wpdb;

		$page = intval( $request->get_param( 'page' ) ) ?: 1;
		$per_page = intval( $request->get_param( 'per_page' ) ) ?: 10;
		$search = sanitize_text_field( $request->get_param( 'search' ) );
		$offset = ( $page - 1 ) * $per_page;

		$where_clause = '';
		if ( ! empty( $search ) ) {
			$search = '%' . $wpdb->esc_like( $search ) . '%';
			$where_clause = $wpdb->prepare(
				" WHERE email LIKE %s OR name LIKE %s OR message LIKE %s",
				$search,
				$search,
				$search
			);
		}

		$total = $wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name $where_clause" );
		$messages = $wpdb->get_results(
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
			'data' => $messages,
		);
	}

	public function delete_message( $request ) {
		global $wpdb;

		$id = intval( $request->get_param( 'id' ) );

		if ( ! $id ) {
			return new WP_Error( 'invalid_id', 'Invalid message ID', array( 'status' => 400 ) );
		}

		$result = $wpdb->delete( $this->table_name, array( 'id' => $id ), array( '%d' ) );

		if ( $result ) {
			return array( 'success' => true, 'message' => 'Message deleted successfully' );
		} else {
			return new WP_Error( 'delete_error', 'Failed to delete message', array( 'status' => 500 ) );
		}
	}

	public function check_permission() {
		return current_user_can( 'manage_options' );
	}

	public function add_admin_menu() {
		add_menu_page(
			'联系消息管理',
			'联系消息',
			'manage_options',
			'feryfit-contact',
			array( $this, 'render_admin_page' ),
			'dashicons-email',
			7
		);
	}

	public function enqueue_admin_scripts( $hook_suffix ) {
		if ( $hook_suffix !== 'toplevel_page_feryfit-contact' ) {
			return;
		}

		wp_enqueue_script(
			'feryfit-contact-admin',
			get_template_directory_uri() . '/assets/js/contact-admin.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script( 'feryfit-contact-admin', 'feryfitContact', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'rest_url' => get_rest_url(),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		) );
	}

	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">联系消息管理</h1>
			<hr class="wp-header-end">

			<div class="feryfit-search-box">
				<input type="text" id="feryfit-contact-search-input" placeholder="搜索邮箱、姓名或消息..." />
				<button id="feryfit-contact-search-btn" class="button">搜索</button>
				<button id="feryfit-contact-reset-btn" class="button">重置</button>
			</div>

			<div id="feryfit-contact-container">
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
			.feryfit-contact-table {
				width: 100%;
				border-collapse: collapse;
				margin-top: 20px;
			}
			.feryfit-contact-table th,
			.feryfit-contact-table td {
				border: 1px solid #ddd;
				padding: 12px;
				text-align: left;
			}
			.feryfit-contact-table th {
				background-color: #f1f1f1;
				font-weight: 600;
			}
			.feryfit-contact-table tr:nth-child(even) {
				background-color: #f9f9f9;
			}
			.feryfit-contact-table tr:hover {
				background-color: #f1f1f1;
			}
			.feryfit-contact-pagination {
				margin-top: 20px;
				text-align: center;
			}
			.feryfit-contact-pagination a {
				display: inline-block;
				padding: 8px 16px;
				text-decoration: none;
				border: 1px solid #ddd;
				margin: 0 4px;
				border-radius: 4px;
			}
			.feryfit-contact-pagination a.current {
				background-color: #c73e1d;
				color: white;
				border-color: #c73e1d;
			}
			.feryfit-contact-pagination a:hover:not(.current) {
				background-color: #f1f1f1;
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
			.message-preview {
				max-width: 300px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
		</style>
		<?php
	}
}

new FeryFit_Contact_Manager();

function feryfit_create_contact_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'feryfit_contact_messages';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		email VARCHAR(255) NOT NULL,
		name VARCHAR(255) NOT NULL,
		message TEXT NOT NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

add_action( 'init', 'feryfit_create_contact_table' );
