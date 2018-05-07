<?php
/**
 * Plugin Name: Custom Login (by duo.me)
 * Description: For customizable frontend login forms.
 * Plugin URI: https://github.com/agenciaduome/duome-login
 * Version: 1.0
 * Author: duo.me
 * Author URI: https://agenciaduo.me/
 * Text Domain: duome-login
 * Domain Path: /languages
 *
 * @package duomeLogin
 */

defined( 'ABSPATH' ) || exit;

define( 'DUOME_LOGIN_FILE', __FILE__ );
define( 'DUOME_LOGIN_DIR', __DIR__ );

require_once DUOME_LOGIN_DIR . '/class-duome-login.php';

add_action( 'plugins_loaded', array( 'Duome_Login', 'get_instance' ) );
