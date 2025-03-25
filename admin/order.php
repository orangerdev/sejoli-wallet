<?php

namespace Sejoli_Wallet\Admin;

class Order {

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
     * Product data
     * @since   1.0.0
     * @var     WP_Post
     */
    protected $product;

    /**
     * Buyer ID
     * @since   1.0.0
     * @var     integer
     */
    protected $buyer_id;

    /**
     * Commission Data
     * @since   1.0.0
     * @var     array
     */
    protected $commissions;

    /**
     * Add reward point detailt
     * @since   1.0.0
     * @var     array
     */
    protected $order_meta = array();

	/**
	 * State if current order use wallet
	 * @since	1.0.0
	 * @var 	booleans
	 */
	protected $order_use_wallet = false;

	/**
	 * Set wallet amount that use for order
	 * @since	1.0.0
	 * @var 	float;
	 */
	protected $wallet_amount = 0.0;

	/**
	 * Set cashback amount
	 * @since	1.0.0
	 * @var 	array|false
	 */
	protected $cashback_setup = false;

	/**
	 * Set cashback amount
	 * @since	1.0.0
	 * @var 	array|false
	 */
	protected $cashback = 0.0;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Check if order use wallet
	 * Hooked via filter sejoli/order/grand-total, priority 152
	 * @since 	1.0.0
	 * @param  	array  $post_data
	 * @return
	 */
	public function check_wallet_use($total, array $post_data) {

		if(array_key_exists('wallet', $post_data) && 'false' !== $post_data['wallet']) :

			$wallet_data = sejoli_get_user_wallet_data();

			if(false !== $wallet_data['valid'] && 0.0 < floatval($wallet_data['wallet']->available_total)) :

				if(isset($post_data['shipment']) && !empty($post_data['shipment']) && 'undefined' !== $post_data['shipment']) {
					list($courier,$service,$cost) = explode(':::', $post_data['shipment']);
					$total += $cost;
				}

				$discount_value = 0;
				if(isset($post_data['coupon'])):
					$getCoupon = sejolisa_get_coupon_by_code( $post_data['coupon'] );

					if(true === boolval($getCoupon['valid'])):
						$discount_value = apply_filters('sejoli/coupon/value', $getCoupon['coupon']['discount']['value'], $total, $getCoupon['coupon'], $post_data);
					endif;
				endif;
		
				$subtotal = ($total - $discount_value) - floatval($wallet_data['wallet']->available_total);
				$total_use_wallet = $total - $discount_value;

				if($subtotal < 0) :

					$total = 0.0;
					$this->wallet_amount = $total_use_wallet;

				else :

					$total = $subtotal;
					$this->wallet_amount = $wallet_data['wallet']->available_total;

				endif;

				$this->order_use_wallet = true;
			endif;

		endif;
		
		return $total;
	
	}

	/**
	 * Calculate possible cashback
	 * Hooked via filter sejoli/order/grand-total, priority 152
	 * @since 	1.4.0
	 * @param  	float 	$total
	 * @param  	array  	$post_data
	 * @return 	float
	 */
	public function calculate_cashback($total, array $post_data) {

		$this->product  = sejolisa_get_product($post_data['product_id']);

		if(
            property_exists($this->product, 'cashback') &&
            $this->product->cashback['activate']
        ) :

			$args = array(
				'product_id'  => $post_data['product_id'],
				'user_id'     => get_current_user_id(),
				'grand_total' => $total,
			);

            $this->cashback_setup = sejoli_calculate_cashback($args);

        endif;

		return $total;
	
	}

	/**
	 * Add cart detail
	 * Hooked via filter sejoli/order/cart-detail priority 152
	 * @since 	1.0.0
	 * @param 	array $detail
	 * @param 	array $post_data
	 */
	public function add_cart_detail($detail, $post_data) {

		if($this->order_use_wallet) :
			
			$detail['wallet'] = '-' .$this->wallet_amount;
	
		endif;

		return $detail;
	
	}

	/**
	 * Set cashback and wallet use in order meta data
	 * Hooked via sejoli/order/meta-data, priority 152
	 * @since 	1.0.0
	 * @param 	array 	$meta_data
	 * @param 	array 	$order_data
	 * @return 	array
	 */
	public function set_order_meta_data(array $meta_data, array $order_data) {

		if($this->cashback_setup) :
	
			$meta_data['cashback']	= $this->cashback_setup;
	
		endif;

		if($this->order_use_wallet) :
	
			$meta_data['wallet'] = $this->wallet_amount;
	
		endif;

		return $meta_data;
	
	}

	/**
	 * Add wallet use amount
	 * Hooked via action sejoli/order/new, priority 8
	 * @since 	1.0.0
	 * @param 	array 	$order_data
	 * @return 	void
	 */
	public function add_wallet_use(array $order_data) {

		if( $order_data['type'] === 'subscription-regular' && $order_data['order_parent_id']  > 0 ) :

			if($order_data['meta_data']['coupon']['discount'] > 0) {
				$this->wallet_amount = $this->wallet_amount - $order_data['meta_data']['coupon']['discount'];
			}
		
		endif;

		if(false !== $this->order_use_wallet) :

			$response = sejoli_use_wallet($this->wallet_amount, $order_data['user_id'], $order_data['ID']);
	
			do_action(
				'sejoli/log/write',
				'use-wallet',
				sprintf(
					__('Use wallet %s from order ID %s for user %s, validity %s', 'sejoli-wallet'),
					sejolisa_price_format($this->wallet_amount),
					$order_data['ID'],
					$order_data['user_id'],
					$response['valid']
				)
			);
	
		endif;

	}

    /**
     * Check order and add cashback if possible
     * Hooked via action sejoli/order/new, priority 8
     * @since   1.0.0
     * @param   array $order_data
     */
    public function add_cashback_for_buyer(array $order_data) {

		if(
			is_array($this->cashback_setup) &&
			array_key_exists('total', $this->cashback_setup)
		) :

			$cashback_response = sejoli_add_cashback(array(
				'order_id'    => $order_data['ID'],
				'product_id'  => $this->product->ID,
				'user_id'     => $order_data['user_id'],
				'value'       => $this->cashback_setup['total'] * $order_data['quantity'],
				'refundable'  => $this->cashback_setup['refundable'],
				'label'       => 'cashback',
				'valid_point' => ('completed' !== $order_data['status']) ? false : true,
				'meta_data'   => array(
					'setup' => $this->cashback_setup['setup'],
					'group' => $this->cashback_setup['group']
				)
			));

			do_action(
				'sejoli/log/write',
				'add-cashback',
				sprintf(
					__('Add cashback %s from order ID %s for user %s', 'sejoli-wallet'),
					(isset($this->cashback['total'])) ? sejolisa_price_format($this->cashback['total']) : null,
					$order_data['ID'],
					$order_data['user_id']
				)
			);

		endif;
    
    }

    /**
     * Update point status to invalid
     * Hooked via action sejoli/order/set-status/on-hold,       priority 111
     * Hooked via action sejoli/order/set-status/in-progress,   priority 111
     * Hooked via action sejoli/order/set-status/shipped,       priority 111
     * Hooked via action sejoli/order/set-status/refunded,      priority 111
     * Hooked via action sejoli/order/set-status/cancelled,     priority 111
     * @param  array  $order_data
     * @return void
     */
    public function update_point_status_to_not_valid(array $order_data) {
    
        sejoli_update_wallet_valid_point($order_data['ID'], false);
    
    }

    /**
     * Update point status to valid
     * Hooked via action sejoli/order/set-status/completed,     priority 111
     * @param  array  $order_data
     * @return void
     */
    public function update_point_status_to_valid(array $order_data) {
    
        sejoli_update_wallet_valid_point($order_data['ID'], true);
    
    }

    /**
     * Add point information in notification
     * Hooked via filter sejoli/notification/content/order-meta, priority 111
     * @since   1.0.0
     * @param   string  $content
     * @param   string  $media
     * @param   string  $recipient_type
     * @param   array   $order_detail
     * @return  string
     */
    public function add_cashback_info($content, $media, $recipient_type, $order_detail) {

        if (
		    isset($order_detail['order_data']) && 
		    isset($order_detail['order_data']['status']) &&
		    'completed' === $order_detail['order_data']['status'] && 
		    in_array($recipient_type, array('buyer', 'affiliate'))
		) :

            switch($media) :

                case 'email' :
                    $info_content   = sejolisa_carbon_get_theme_option('info_cashback_email');
                    break;

                case 'whatsapp' :
                    $info_content   = sejolisa_carbon_get_theme_option('info_cashback_whatsapp');
                    break;

                case 'sms' :
                    $info_content   = sejolisa_carbon_get_theme_option('info_cashback_sms');
                    break;

            endswitch;

            $user_id = $order_detail['order_data']['user_id'];

            $single_response = sejoli_get_single_user_cashback_from_an_order(array(
                'order_id'  => $order_detail['order_data']['ID'],
                'user_id'   => $user_id
            ));

            $all_response = sejoli_get_user_wallet_data($user_id);

            if(
                false !== $single_response['valid'] &&
                false !== $all_response['valid']
            ) :
    
                $info_content = str_replace('{{cashback}}',     sejolisa_price_format($single_response['wallet']->value), $info_content);
                $info_content = str_replace('{{wallet-total}}', sejolisa_price_format($all_response['wallet']->available_total), $info_content);

                $content .= $info_content;

            endif;

        endif;

        return $content;
    
    }
}
