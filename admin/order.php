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
	 * Check if order use wallet
	 * Hooked via action sejoli/order/grand-total, priority 152
	 * @since 	1.0.0
	 * @param  	array  $post_data
	 * @return
	 */
	public function check_wallet_use($total, array $post_data) {
		
		if(array_key_exists('wallet', $post_data) && 'false' !== $post_data['wallet']) :

			$wallet_data = sejoli_get_user_wallet_data();

			if(false !== $wallet_data['valid'] && 0.0 < floatval($wallet_data['wallet']->available_total)) :

				$subtotal = $total - floatval($wallet_data['wallet']->available_total);

				if($subtotal < 0) :

					$total = 0.0;
					$this->wallet_amount = $total;

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
	 * Add cart detail
	 * Hooked via filter sejoli/order/cart-detail priority 152
	 * @since 	1.0.0
	 * @param 	array $detail
	 * @param 	array $post_data
	 */
	public function add_cart_detail($detail, $post_data) {

		if($this->order_use_wallet) :
			$detail['wallet'] = $this->wallet_amount;
		endif;

		return $detail;
	}

    /**
     * Check order and add cashback if possible
     * Hooked via action sejoli/order/new, priority 8
     * @since   1.0.0
     * @param   array $order_data
     */
    public function add_cashback_for_buyer(array $order_data) {

        $this->product  = sejolisa_get_product($order_data['product_id']);

        if(
            property_exists($this->product, 'cashback') &&
            $this->product->cashback['activate']
        ) :
            $cashback = sejoli_calculate_cashback($order_data, $order_data['user_id']);

            $cashback_response = sejoli_add_cashback(array(
                'order_id'    => $order_data['ID'],
                'product_id'  => $this->product->ID,
                'user_id'     => $order_data['user_id'],
                'value'       => $cashback['total'],
                'refundable'  => $cashback['refundable'],
                'label'       => 'cashback',
                'valid_point' => ('completed' !== $order_data['status']) ? false : true,
                'meta_data'   => array(
                    'setup' => $cashback['setup'],
                    'group' => $cashback['group']
                )
            ));

            do_action(
                'sejoli/log/write',
                'add-cashback',
                sprintf(
                    __('Add cashback %s from order ID %s for user %s', 'sejoli-reward'),
                    sejolisa_price_format($cashback['total']),
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

        if(
            'completed' === $order_detail['order_data']['status'] &&
            in_array($recipient_type, array('buyer', 'affiliate'))
        ) :

            switch($media) :

                case 'email' :
                    $info_content   = carbon_get_theme_option('info_cashback_email');
                    break;

                case 'whatsapp' :
                    $info_content   = carbon_get_theme_option('info_cashback_whatsapp');
                    break;

                case 'sms' :
                    $info_content   = carbon_get_theme_option('info_cashback_sms');
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
