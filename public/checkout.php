<?php

namespace Sejoli_Wallet\Front;


class Checkout {

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
	 * Set if current user is able to use wallet
	 * @since	1.0.0
	 * @access 	protected
	 * @var 	boolean
	 */
	protected $enable_wallet = false;

	/**
	 * Set product
	 * @since 	1.0.0
	 * @var 	null|WP_Post
	 */
	protected $product;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Display field to use wallet data
	 * @since 	1.0.0
	 * @param  	WP_Post $product
	 * @return 	void
	 */
	public function display_wallet_field(\WP_Post $product) {

		$wallet_data = sejoli_get_user_wallet_data();

		if(false !== $wallet_data['valid'] && 0.0 < floatval($wallet_data['wallet']->available_total)) :

			$this->enable_wallet = true;
			$wallet = $wallet_data['wallet'];

			if('digital' === $product->type) :
				require_once( plugin_dir_path( __FILE__ ) . '/partials/digital/wallet-field.php');
	        else :
				require_once( plugin_dir_path( __FILE__ ) . '/partials/physical/wallet-field.php');
	        endif;
		endif;
	}

    /**
     * Display potential cashback info
     * Hooked via sejoli/checout-template/after-product, priority 11
     * @since 	1.0.0
     * @param  	WP_Post $product
     * @return 	void
     */
    public function display_cashback_info(\WP_Post $product) {

        if(false === $product->cashback['activate'] || 0.0 === floatval($product->cashback['amount'])) :
            return;
        endif;

		$this->product = $product;

        if('digital' === $product->type) :
			require_once( plugin_dir_path( __FILE__ ) . '/partials/digital/cashback-info.php');
        else :
			require_once( plugin_dir_path( __FILE__ ) . '/partials/physical/cashback-info.php');
        endif;
    }

	/**
	 * Add JS script to calculate order wnen change the wallet field
	 * Hooked via wp_footer, priority 999
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function add_js_script() {

		if(is_singular('sejoli-product') && $this->enable_wallet) :

			if('digital' === $this->product->type) :
			?>
			<script type="text/javascript">
			(function($){
				'use strict';

				$(document).ready(function(){
					$('body').on('change', '#use-wallet', function(){
						sejoliSaCheckout.getCalculate();
					});
				});

			})(jQuery);
			</script>
			<?php
			else :
			?>
			<script type="text/javascript">
			(function($){
				'use strict';

				$(document).ready(function(){
					$('body').on('change', '#use-wallet', function(){
						sejoliSaCheckoutFisik.func.changeCalculate();
					});
				});
			})(jQuery);
			</script>
			<?php
			endif;
		endif;

	}
}
