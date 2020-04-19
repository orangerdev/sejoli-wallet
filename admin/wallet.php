<?php

namespace Sejoli_Wallet\Admin;

class Wallet {

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
	 * Check if current admin page is a sejoli page
	 * Hooked via filter sejoli/admin/is-sejoli-page, priority 1111
	 * @param  boolean $is_sejoli_page
	 * @return boolean
	 */
	public function is_current_page_sejoli_page($is_sejoli_page) {

		global $pagenow;

		if(
			isset($_GET['page']) &&
            'sejoli-wallet' === $_GET['page']
		) :
			return true;
		endif;

		return $is_sejoli_page;
	}

    /**
	 * Set local JS variables
	 * Hooked via filter sejoli/admin/js-localize-data, priority 12
	 * @since 	1.0.0
	 * @param 	array $js_vars
	 * @return 	array
	 */
	public function set_localize_js_vars($js_vars) {

		$js_vars['wallet'] = array(
			'table'	=> array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-wallet-table'
					), admin_url('admin-ajax.php')
				),
				'nonce'	=> wp_create_nonce('sejoli-render-wallet-table')
			),
			'single_table'	=> array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-single-wallet-table'
					), admin_url('admin-ajax.php')
				),
				'nonce'   => wp_create_nonce('sejoli-render-single-wallet-table'),
				'user_id' => (isset($_GET['user_id'])) ? intval($_GET['user_id']) : get_current_user_id()
			)
		);

		return $js_vars;
	}

    /**
	 * Add new submenu under Sejoli
	 * Hooked via action admin_menu, priority 1002
	 * @since 	1.0.0
	 * @return	void
	 */
	public function add_sejoli_submenu() {

        add_submenu_page(
            'crb_carbon_fields_container_sejoli.php',
            __('Saldo', 'sejoli'),
            __('Saldo', 'sejoli'),
            'manage_sejoli_sejoli',
            'sejoli-wallet',
            array($this, 'display_wallet_page')
        );

	}

    /**
     * Display wallet page
     * @since   1.0.0
     * @return  void
     */
    public function display_wallet_page() {
        require_once( plugin_dir_path( __FILE__ ) . 'partials/user-wallet.php' );
    }
}
