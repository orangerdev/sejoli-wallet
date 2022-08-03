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

			$this->product 		 = $product;
			$this->enable_wallet = true;

			$wallet 					= $wallet_data['wallet'];
			$disable_wallet 			= boolval(carbon_get_post_meta($product->ID, 'deactivate_wallet'));
			$using_wallet_only 			= boolval(carbon_get_post_meta($product->ID, 'buy_using_wallet_only'));
			// $available_payment_gateways = apply_filters('sejoli/payment/available-payment-gateways', []);
			// $payment_options 			= sejolisa_get_payment_options();

            $request = wp_parse_args( $_POST,[
                'product_id'      => 0,
                'coupon'          => NULL,
                'quantity'        => 1,
                'type'            => 'regular',
                'payment_gateway' => 'manual',
                'shipment'        => NULL,
                'variants'        => [],
				'wallet'		  => false,
            ]);
            $response = [];
            $request['product_id'] = $product->ID;
            do_action('sejoli/frontend/checkout/calculate', $request);
            $response['calculate'] = sejolisa_get_respond('total');

			// do_action('sejoli/checkout/calculate', $request);
			// $response['calculate'] = sejolisa_get_respond('total');

			if(false === $disable_wallet && true === $using_wallet_only) :

				if('digital' === $product->type) :

					$get_total = $response['calculate']['total'];
					require_once( plugin_dir_path( __FILE__ ) . '/partials/digital/wallet-field.php');
		        
		        else :
					
					$get_total = $response['calculate']['total'];
					require_once( plugin_dir_path( __FILE__ ) . '/partials/physical/wallet-field.php');
		        
		        endif;
		    
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

		$this->product = ( !is_a($this->product, 'WP_Post') ) ? $product : $this->product;

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

			$disable_wallet    = boolval(carbon_get_post_meta($this->product->ID, 'deactivate_wallet'));
            $using_wallet_only = boolval(carbon_get_post_meta($this->product->ID, 'buy_using_wallet_only'));

            if(false === $disable_wallet && true === $using_wallet_only) :
				if('digital' === $this->product->type) :
					require_once( plugin_dir_path( __FILE__ ) . 'partials/digital/footer-js.php');
				else :
					require_once( plugin_dir_path( __FILE__ ) . 'partials/physical/footer-js.php');
				endif;
			endif;

		endif;

	}
}
