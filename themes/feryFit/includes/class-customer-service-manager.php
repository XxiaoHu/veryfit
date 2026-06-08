<?php

class FeryFit_Customer_Service_Manager {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function register_settings() {
		register_setting( 'feryfit_customer_service_group', 'feryfit_whatsapp', array(
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default' => '',
		) );

		register_setting( 'feryfit_customer_service_group', 'feryfit_email', array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_email',
			'default' => '',
		) );

		register_setting( 'feryfit_customer_service_group', 'feryfit_phone', array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default' => '',
		) );

		register_setting( 'feryfit_customer_service_group', 'feryfit_facebook', array(
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default' => '',
		) );
	}

	public function register_rest_routes() {
		register_rest_route( 'feryfit/v1', '/customer-service', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_customer_service_data' ),
			'permission_callback' => '__return_true',
		) );
	}

	public function get_customer_service_data() {
		return array(
			'whatsapp' => get_option( 'feryfit_whatsapp', '' ),
			'email'    => get_option( 'feryfit_email', '' ),
			'phone'    => get_option( 'feryfit_phone', '' ),
			'facebook' => get_option( 'feryfit_facebook', '' ),
		);
	}

	public function add_admin_menu() {
		add_menu_page(
			'客服联系方式和邮箱',
			'客服联系方式',
			'manage_options',
			'feryfit-customer-service',
			array( $this, 'render_admin_page' ),
			'dashicons-megaphone',
			8
		);
	}

	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1>客服联系方式和邮箱</h1>

			<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
				<div class="notice notice-success is-dismissible">
					<p>设置已保存。</p>
				</div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( 'feryfit_customer_service_group' ); ?>
				<?php do_settings_sections( 'feryfit_customer_service_group' ); ?>

				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="feryfit_whatsapp">WhatsApp</label>
						</th>
						<td>
							<input
								type="url"
								id="feryfit_whatsapp"
								name="feryfit_whatsapp"
								value="<?php echo esc_url( get_option( 'feryfit_whatsapp', '' ) ); ?>"
								class="regular-text"
								placeholder="https://wa.me/123456789"
							/>
							<p class="description">请输入 WhatsApp 链接地址，格式：https://wa.me/123456789</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="feryfit_email">Email</label>
						</th>
						<td>
							<input
								type="email"
								id="feryfit_email"
								name="feryfit_email"
								value="<?php echo esc_attr( get_option( 'feryfit_email', '' ) ); ?>"
								class="regular-text"
								placeholder="example@domain.com"
							/>
							<p class="description">请输入客服邮箱地址。</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="feryfit_phone">Phone</label>
						</th>
						<td>
							<input
								type="text"
								id="feryfit_phone"
								name="feryfit_phone"
								value="<?php echo esc_attr( get_option( 'feryfit_phone', '' ) ); ?>"
								class="regular-text"
								placeholder="+1234567890"
							/>
							<p class="description">请输入客服电话号码，格式：+1234567890</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="feryfit_facebook">Facebook</label>
						</th>
						<td>
							<input
								type="url"
								id="feryfit_facebook"
								name="feryfit_facebook"
								value="<?php echo esc_url( get_option( 'feryfit_facebook', '' ) ); ?>"
								class="regular-text"
								placeholder="https://facebook.com/yourpage"
							/>
							<p class="description">请输入 Facebook 页面链接地址。</p>
						</td>
					</tr>
				</table>

				<?php submit_button( '保存设置' ); ?>
			</form>
		</div>
		<?php
	}
}

new FeryFit_Customer_Service_Manager();
