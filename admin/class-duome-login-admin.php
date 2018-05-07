<?php
/**
 * Admin options.
 *
 * @package duomeLogin
 */

/**
 * Class to manage config screen options
 */
class Duome_Login_Admin {
	/**
	 * Config options name. Receives the value used in Duome_Login.
	 *
	 * @var string
	 * @see Duome_Login::options_id
	 */
	protected $options_id;

	/**
	 * Plugin configurations array.
	 *
	 * @var array
	 * @see Duome_Login::get_settings()
	 */
	protected $plugin_settings;

	/**
	 * Constructor. Hook necessary functions.
	 */
	public function __construct() {
		$this->options_id      = Duome_Login::get_instance()->options_id;
		$this->plugin_settings = Duome_Login::get_instance()->get_settings();

		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( DUOME_LOGIN_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Create config page
	 */
	public function add_options_page() {
		add_options_page(
			__( 'Frontend Login and Register Settings', 'duome-login' ),
			__( 'Login and register', 'duome-login' ),
			'manage_options',
			'duome-login',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Config screen HTML
	 */
	public function options_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_errors( $this->options_id );
				settings_fields( $this->options_id );
				do_settings_sections( $this->options_id );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register plugin options.
	 */
	public function register_settings() {
		$group_id        = $this->options_id;
		$section_id      = $group_id . '_section';
		$settings_prefix = $this->options_id . '_';

		register_setting(
			$group_id,
			$this->options_id,
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			$section_id,
			__( 'Settings', 'duome-login' ),
			array( $this, 'settings_page_header_html' ),
			$group_id
		);

		add_settings_field(
			$settings_prefix . 'redirect_after_login',
			__( 'After login redirect to:', 'duome-login' ),
			array( $this, 'setting_redirect_after_login_output' ),
			$group_id,
			$section_id,
			array(
				'label_for' => $settings_prefix . 'redirect_after_login',
			)
		);
	}

	/**
	 * Show message before fields. Not used yet.
	 */
	public function settings_page_header_html() {
		?>
		<p></p>
		<?php
	}

	/**
	 * Redirect after login field HTML
	 */
	public function setting_redirect_after_login_output() {
		$field_name = "{$this->options_id}[redirect_after_login]";
		$current    = ! empty( $this->plugin_settings['redirect_after_login'] ) ? $this->plugin_settings['redirect_after_login'] : '';
		?>
		<input
			type="text"
			name="<?php echo $field_name; ?>"
			value="<?php echo esc_attr( $current ); ?>"
			id="<?php echo $this->options_id; ?>_redirect_after_login"
			class="widefat"
			/>
		<?php
	}

	/**
	 * Add Settings link in plugins screen
	 *
	 * @param array $links Links já adicionados.
	 */
	function add_settings_link( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=duome-login' ) . '">' . __( 'Settings', 'duome-login' ) . '</a>',
			),
			$links
		);

	}

	/**
	 * Validate and sanitize settings
	 *
	 * @param  array $input Data before validation.
	 * @return array        Sanitized data
	 */
	function sanitize_settings( $input ) {
		return $input;
	}
}

return new Duome_Login_Admin();
