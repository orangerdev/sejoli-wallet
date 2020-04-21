<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Sejoli_Wallet
 * @subpackage Sejoli_Wallet/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sejoli_Wallet
 * @subpackage Sejoli_Wallet/includes
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Sejoli_Wallet {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sejoli_Wallet_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SEJOLI_WALLET_VERSION' ) ) {
			$this->version = SEJOLI_WALLET_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sejoli-wallet';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_cli();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sejoli_Wallet_Loader. Orchestrates the hooks of the plugin.
	 * - Sejoli_Wallet_i18n. Defines internationalization functionality.
	 * - Sejoli_Wallet_Admin. Defines all hooks for the admin area.
	 * - Sejoli_Wallet_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejoli-wallet-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejoli-wallet-i18n.php';

		/**
		 * The class responsible for datatabase model
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/wallet.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/order.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/json.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/notification.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/product.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/user.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wallet.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/member.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/public.php';

		/**
		 * The class responsible for defining CLI command
		 */
		if ( class_exists( 'WP_CLI' ) ) :
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cli/wallet.php';
		endif;


		/**
		 * Routine functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'functions/wallet.php';

		$this->loader = new Sejoli_Wallet_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sejoli_Wallet_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sejoli_Wallet_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register CLI command
	 * @since 	1.0.0
	 * @return 	void
	 */
	private function register_cli() {

		if ( !class_exists( 'WP_CLI' ) ) :
			return;
		endif;

		$wallet 	= new Sejoli_Wallet\CLI\Wallet();

		WP_CLI::add_command('sejolisa wallet', $wallet);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$admin 	 = new Sejoli_Wallet\Admin( $this->get_plugin_name(), $this->get_version() );

		$json 	 = new Sejoli_Wallet\Admin\Json( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_ajax_sejoli-wallet-table',			$json, 'ajax_set_for_table', 1);
		$this->loader->add_action( 'wp_ajax_sejoli-single-wallet-table',	$json, 'ajax_set_single_user_for_table', 1);
		$this->loader->add_action( 'sejoli_ajax_single-wallet-table',		$json, 'ajax_set_single_user_for_table', 1);

		$notification = new Sejoli_Wallet\Admin\Notification( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/email/template-directory',		$notification, 'set_notification_directory', 11, 4);
		$this->loader->add_filter( 'sejoli/sms/template-directory',			$notification, 'set_notification_directory', 11, 4);
		$this->loader->add_filter( 'sejoli/whatsapp/template-directory',	$notification, 'set_notification_directory', 11, 4);

		$this->loader->add_filter( 'sejoli/notification/fields',    					$notification, 'set_notification_fields', 111);
		$this->loader->add_filter( 'sejoli/notification/libraries',						$notification, 'add_libraries', 11);
		$this->loader->add_action( 'sejoli/notification/wallet/request-fund',			$notification, 'send_request_fund_notification', 11);
		$this->loader->add_action( 'sejoli/notification/wallet/cancel-request-fund',	$notification, 'send_cancel_request_fund_notification', 11);

		$order = new Sejoli_Wallet\Admin\Order( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'sejoli/order/new',							$order, 'add_cashback_for_buyer', 			   8);
		$this->loader->add_action( 'sejoli/order/set-status/on-hold',			$order, 'update_cashback_status_to_not_valid', 111);
		$this->loader->add_action( 'sejoli/order/set-status/in-progress',		$order, 'update_cashback_status_to_not_valid', 111);
		$this->loader->add_action( 'sejoli/order/set-status/shipped',			$order, 'update_cashback_status_to_not_valid', 111);
		$this->loader->add_action( 'sejoli/order/set-status/refunded',			$order, 'update_cashback_status_to_not_valid', 111);
		$this->loader->add_action( 'sejoli/order/set-status/cancelled',			$order, 'update_cashback_status_to_not_valid', 111);
		$this->loader->add_action( 'sejoli/order/set-status/completed',			$order, 'update_cashback_status_to_valid', 	   111);
		$this->loader->add_action( 'sejoli/notification/content/order-detail',	$order, 'add_cashback_info',				   111, 4);

		$product = new Sejoli_Wallet\Admin\Product( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/product/fields',					$product, 'set_product_fields', 	11);
		$this->loader->add_filter( 'sejoli/user-group/fields',				$product, 'set_user_group_fields',	11);
		$this->loader->add_filter( 'sejoli/user-group/per-product/fields',	$product, 'set_user_group_per_product_fields',  11);
		$this->loader->add_filter( 'sejoli/product/meta-data',				$product, 'set_product_cashback',	122);

		$user = new Sejoli_Wallet\Admin\User( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/user-group/detail',				$user, 'set_user_group_detail', 11, 4);

		$wallet = new Sejoli_Wallet\Admin\Wallet( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/admin/is-sejoli-page', 	$wallet, 'is_current_page_sejoli_page', 1111);
		$this->loader->add_filter( 'sejoli/admin/js-localize-data',	$wallet, 'set_localize_js_vars', 11);
		$this->loader->add_action( 'admin_menu',					$wallet, 'add_sejoli_submenu', 1002);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$front = new Sejoli_Wallet\Front( $this->get_plugin_name(), $this->get_version() );

		$member = new Sejoli_Wallet\Front\Member( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'sejoli/member-area/menu',			$member, 'register_menu', 11);
		$this->loader->add_filter( 'sejoli/member-area/backend/menu',	$member, 'add_menu_in_backend', 1111);
		$this->loader->add_filter( 'sejoli/member-area/menu-link',		$member, 'display_link_list_in_menu', 11, 4);
		$this->loader->add_filter( 'sejoli/template-file',				$member, 'set_template_file', 111, 2);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sejoli_Wallet_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
