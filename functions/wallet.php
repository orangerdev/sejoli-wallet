<?php
/**
 * Add cashback
 * @since   1.0.0
 * @param   array   $args
 * @return  array   Response
 */
function sejoli_add_cashback($args) {

    $args = wp_parse_args($args, array(
        'order_id'    => NULL,
        'product_id'  => NULL,
        'user_id'     => NULL,
        'value'       => NULL,
        'refundable'  => false,
        'label'       => 'cashback',
        'valid_point' => false,
        'meta_data'   => array()
    ));

    $response   =  \SEJOLI_WALLET\Model\Wallet::reset()
                        ->set_order_id($args['order_id'])
                        ->set_product_id($args['product_id'])
                        ->set_user_id($args['user_id'])
                        ->set_value($args['value'])
                        ->set_label($args['label'])
                        ->set_refundable($args['refundable'])
                        ->set_meta_data($args['meta_data'])
                        ->set_valid_point($args['valid_point'])
                        ->add_cashback()
                        ->respond();

    return wp_parse_args($response, array(
        'valid'    => false,
        'wallet'   => NULL,
        'messages' => array()
    ));
}

/**
 * Update wallet valid point
 * @since   1.0.0
 * @param   array   $args
 * @return  array   Response
 */
function sejoli_update_wallet_valid_point($args) {

    $args = wp_parse_args($args, array(
        'order_id'    => NULL,
        'user_id'     => NULL,
        'valid_point' => false
    ));

    $response   =  \SEJOLI_WALLET\Model\Wallet::reset()
                        ->set_order_id($args['order_id'])
                        ->set_valid_point($args['valid_point'])
                        ->update_valid_point()
                        ->respond();

    return wp_parse_args($response, array(
        'valid'    => false,
        'wallet'   => NULL,
        'messages' => array()
    ));
}

/**
 * Get user wallet data
 * @since   1.0.0
 * @param   integer $user_id
 * @return  array   Response
 */
function sejoli_get_user_wallet_data($user_id = 0) {

    $user_id = (0 === $user_id) ? get_current_user_id() : $user_id;

    $response = \SEJOLI_WALLET\Model\Wallet::reset()
                    ->set_user_id($user_id)
                    ->get_user_wallet()
                    ->respond();

    return wp_parse_args($response, array(
        'valid'    => false,
        'wallet'   => NULL,
        'messages' => array()
    ));

}

/**
 * Use wallet data for example payment
 * @since   1.0.0
 * @param   float   $amount
 * @param   integer $user_id
 * @param   string  $label      Type of wallet use
 * @param   array   $meta_data  Use wallet information
 * @return  array   Response
 */
function sejoli_use_wallet($amount, $user_id = 0, $order_id = 0, $label = 'order', $meta_data = array()) {

    $user_id  = (0 === $user_id) ? get_current_user_id() : $user_id;
    $amount   = floatval($amount);
    $valid    = false;
    $messages = array();
    $wallet_response = sejoli_get_user_wallet_data($user_id);

    if(false !== $wallet_response['valid']) :

        $wallet = $wallet_response['wallet'];

        if(floatval($wallet->available_total) >= $amount ) :

            $use_response = \SEJOLI_WALLET\Model\Wallet::reset()
                                ->set_user_id($user_id)
                                ->set_order_id($order_id)
                                ->set_type('out')
                                ->set_label($label)
                                ->set_value($amount)
                                ->set_meta_data($amount)
                                ->use_wallet()
                                ->respond();

            if(false !== $use_response['valid']) :
                $valid = true;
                $messages['success'] = array(
                    sprintf(
                        __('Berhasil digunakan saldo anda sebesar %s', 'sejoli'),
                        sejolisa_price_format($amount)
                    )
                );
            else :
                $messages = $use_response['messages'];
            endif;
        else :
            $messages['error'] = array(
                sprintf(
                    __('Jumlah yang anda gunakan sebesar %s melebihi saldo yang tersedia yaitu %s', 'sejoli'),
                    sejolisa_price_format($amount),
                    sejolisa_price_format($wallet->available_total)
                )
            );
        endif;
    else :
        $messages = $wallet_response['messages'];
    endif;

    return array(
        'valid'    => $valid,
        'wallet'   => NULL,
        'messages' => $messages
    );
}

/**
 * Calculate cashback by an order
 * @since   1.0.0
 * @param   array   $order
 * @param   integer $user_id
 * @return  array
 * - total          Total cashback
 * - refundable     Is cashback refundable
 * - setup          Cashback setup
 */
function sejoli_calculate_cashback(array $order, $user_id = 0) {

    $current_user_id = get_current_user_id();

    if(0 !== $user_id) :
        wp_set_current_user($user_id);
    endif;

    $setup          = 'product';
    $refundable     = false;
    $total_cashback = 0.0;
    $product        = sejolisa_get_product($order['product_id'], true);
    $user_group     = sejolisa_get_user_group($order['user_id']);
    $buyer_group    = (isset($user_group['name'])) ? $user_group['name'] : '-';

    if($product->cashback['activate']) :

        if('percentage' === $product->cashback['type']) :

            $total_cashback = round($order['grand_total'] * $product->cashback['value'] / 100);

        else :

            $total_cashback = $order['quantity'] * $product->cashback['value'];

        endif;

        if(
            0 < $product->cashback['max'] &&
            $total_cashback > $product->cashback['max']
        ) :
            $total_cashback = $product->cashback['max'];
        endif;

        $setup      = $product->cashback['setup'];
        $refundable = $product->cashback['refundable'];

    endif;

    if(0 !== $user_id) :
        wp_set_current_user($current_user_id);
    endif;

    return array(
        'total'      => $total_cashback,
        'refundable' => $refundable,
        'setup'      => $setup,
        'group'      => $buyer_group
    );

}
