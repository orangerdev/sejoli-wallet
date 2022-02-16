<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ridwan-arifandi.com
 * @since             1.0.0
 * @package           Sejoli_Wallet
 *
 * @wordpress-plugin
 * Plugin Name:       Sejoli - Wallet
 * Plugin URI:        https://sejoli.co.id
 * Description:       Implement wallet system into SEJOLI premium membership WordPress plugin
 * Version:           1.1.4
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sejoli-wallet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $sejoli_wallet;

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SEJOLI_WALLET_VERSION'	, '1.1.4' );
define( 'SEJOLI_WALLET_DIR' 	, plugin_dir_path( __FILE__ ) );
define( 'SEJOLI_WALLET_URL' 	, plugin_dir_url( __FILE__ ) );

require SEJOLI_WALLET_DIR . '/third-parties/autoload.php';

add_action('muplugins_loaded', 'sejoli_wallet_check_sejoli');

function sejoli_wallet_check_sejoli() {

	if(!defined('SEJOLISA_VERSION')) :

		add_action('admin_notices', 'sejoli_wallet_no_sejoli_functions');

		function sejoli_wallet_no_sejoli_functions() {
			?><div class='notice notice-error'>
			<p><?php _e('Anda belum menginstall atau mengaktifkan SEJOLI terlebih dahulu.', 'sejoli'); ?></p>
			</div><?php
		}

		return;
	endif;

}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sejoli-wallet-activator.php
 */
function activate_sejoli_wallet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-wallet-activator.php';
	Sejoli_Wallet_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sejoli-wallet-deactivator.php
 */
function deactivate_sejoli_wallet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-wallet-deactivator.php';
	Sejoli_Wallet_Deactivator::deactivate();
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sejoli-wallet.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sejoli_wallet() {

	$plugin = new Sejoli_Wallet();
	$plugin->run();

}

require_once(SEJOLI_WALLET_DIR . 'third-parties/yahnis-elsts/plugin-update-checker/plugin-update-checker.php');

$update_checker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/orangerdev/sejoli-wallet',
	__FILE__,
	'sejoli-wallet'
);

$update_checker->setBranch('master');

run_sejoli_wallet();

register_activation_hook( __FILE__, 'activate_sejoli_wallet' );
register_deactivation_hook( __FILE__, 'deactivate_sejoli_wallet' );
