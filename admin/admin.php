<?php

namespace Sejoli_Wallet;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Sejoli_Wallet
 * @subpackage Sejoli_Wallet/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sejoli_Wallet
 * @subpackage Sejoli_Wallet/admin
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Enqueue needed css and js files
	 * Hooked via action admin_enqueue_scripts, priority 1099
	 * @since 	1.1.1
	 * @return 	void
	 */
	public function enqueue_css_js_scripts() {

		global $pagenow;

		if(
			'admin.php' === $pagenow &&
			isset($_GET['page']) &&
			'sejoli-wallet-input-form' === $_GET['page']
		) :

			wp_enqueue_style 	( 'select2' );
			wp_enqueue_script 	( 'select2' );

		endif;
	}

}
