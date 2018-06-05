<?php
/**
 * Main plugin class
 *
 * @package duomeLogin
 */

/**
 * Main plugin class
 */
class Duome_Login {
	/**
	 * The only class instance
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Config options name
	 *
	 * @var string
	 */
	public $options_id = 'duome_login_settings';

	/**
	 * Plugin configurations array
	 *
	 * @var array
	 */
	protected $plugin_settings;

	/**
	 * Array of errors found
	 *
	 * @var array
	 */
	protected $errors;

	/**
	 * Constructor. Run only once (singleton).
	 */
	private function __construct() {
		$this->plugin_settings = $this->get_settings();
		$this->errors          = array(
			'login' => array(),
		);

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'login' ) );

		add_action( 'duome_login_errors', array( $this, 'show_errors' ) );

		if ( is_admin() ) {
			require_once DUOME_LOGIN_DIR . '/admin/class-duome-login-admin.php';
		}
	}

	/**
	 * Load the translation file.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'duome-login', false, basename( DUOME_LOGIN_DIR ) . '/languages/' );
	}

	/**
	 * Process login data.
	 */
	public function login() {
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'duome_login' ) ) {
			$duome_login_username = ( ! empty( $_POST['duome_login_username'] ) ) ? sanitize_text_field( $_POST['duome_login_username'] ) : '';
			$duome_login_password = ( ! empty( $_POST['duome_login_password'] ) ) ? sanitize_text_field( $_POST['duome_login_password'] ) : '';

			if ( empty( $duome_login_username ) || empty( $duome_login_password ) ) {
				$this->errors['login'] = array(
					__( 'All fields are required.', 'duome-login' ),
				);
			} else {
				$remember = ( isset( $_POST['duome_login_remember'] ) && '1' == $_POST['duome_login_remember'] );
				$creds    = array(
					'user_login'    => $duome_login_username,
					'user_password' => $duome_login_password,
					'remember'      => $remember,
				);

				$user = wp_signon( $creds, true );
				if ( isset( $user->ID ) && '' != $user->ID ) {
					wp_set_auth_cookie( $user->ID, $remember );
					wp_redirect( $this->plugin_settings['redirect_after_login'] );
					exit;
				} else {
					$this->errors['login'] = (array) $user->get_error_message();
				}
			}
		}
	}

	/**
	 * Getter for the array of errors found
	 *
	 * @param  string $index Array index: `login`, `create` or `update`.
	 * @return array         Array of errors found.
	 */
	public function get_errors( $index ) {
		return ( isset( $this->errors[ $index ] ) ) ? $this->errors[ $index ] : array();
	}

	/**
	 * Return HTML'd errors.
	 *
	 * @param  string $index Array index, same options of `get_errors`.
	 */
	public function show_errors( $index ) {
		$errors = $this->get_errors( $index );
		$html   = '';
		if ( count( $errors ) ) {
			$html  = '<div class="duome-login duome-login-errors duome-login-errors-' . $index . '">';
			$html .= '<p>';
			$html .= implode( '</p><p>', $errors );
			$html .= '</p>';
			$html .= '</div>';
		}
		echo apply_filters( 'duome_login_errors_msg', $html, $errors, $index );
	}

	/**
	 * SINGLETON. Return class single instance.
	 *
	 * @return object the class single instance.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Return plugin configurations
	 *
	 * @return array Config array.
	 */
	public function get_settings() {
		return wp_parse_args( get_option( $this->options_id, array() ), array(
			'redirect_after_login' => home_url( '/' ),
		) );
	}
}
