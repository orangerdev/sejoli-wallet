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
 * @param   integer     $order_id
 * @param   boolean     $valid
 * @return  array       Response
 */
function sejoli_update_wallet_valid_point($order_id, $valid = false) {

    $response   =  \SEJOLI_WALLET\Model\Wallet::reset()
                        ->set_order_id($order_id)
                        ->set_valid_point($valid)
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
 * Get cashback from an order
 * @since   1.0.0
 * @param   array   $args
 * @return  array   Response
 */
function sejoli_get_single_user_cashback_from_an_order($args) {

    $args = wp_parse_args($args, array(
        'order_id'  => NULL,
        'user_id'   => NULL
    ));

    $response = \SEJOLI_WALLET\Model\Wallet::reset()
                    ->set_user($args['user_id'])
                    ->set_order_id($args['order_id'])
                    ->get_single_cashback()
                    ->respond();

    return $response;

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
 * @param   array   $order  Array with product_id and user_id
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
    $quantity       = (isset($order['quantity'])) ? $order['quantity'] : '1';

    if($product->cashback['activate']) :

        if('percentage' === $product->cashback['type']) :

            $total_cashback = round($order['grand_total'] * $product->cashback['value'] / 100);

        else :

            $total_cashback = $quantity * $product->cashback['value'];

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

/**
 * Get available all user point
 * @since   1.0.0
 * @param   array  $args
 * @return  array
 */
function sejoli_get_all_user_wallet($args = array()) {

    $args = wp_parse_args($args, array(
                'user_id' => NULL
            ));
    $response   = \SEJOLI_WALLET\Model\Wallet::reset()
                    ->set_filter_from_array($args)
                    ->get_all_user_wallet()
                    ->respond();

    return $response;

}

/**
 * Get wallet history
 * @since   1.0.0
 * @param   array  $args
 * @param   array  $table
 * @return  array
 */
function sejoli_wallet_get_history(array $args, $table = array()) {

    $args = wp_parse_args($args,[
        'user_id'     => NULL,
        'product_id'  => NULL,
        'type'        => NULL,
        'valid_point' => true
    ]);

    $table = wp_parse_args($table, [
        'start'   => NULL,
        'length'  => NULL,
        'order'   => NULL,
        'filter'  => NULL
    ]);

    if(isset($args['date-range']) && !empty($args['date-range'])) :

        $table['filter']['date-range'] = $args['date-range'];
        unset($args['date-range']);

    endif;

    $query = SEJOLI_WALLET\Model\Wallet::reset()
                ->set_filter_from_array($args)
                ->set_data_start($table['start']);

    if(isset($table['filter']['date-range']) && !empty($table['filter']['date-range'])) :

        list($start, $end) = explode(' - ', $table['filter']['date-range']);
        $query = $query->set_filter('created_at', $start , '>=')
                    ->set_filter('created_at', $end, '<=');

    endif;

    if(0 < $table['length']) :

        $query->set_data_length($table['length']);

    endif;

    if(!is_null($table['order']) && is_array($table['order'])) :

        foreach($table['order'] as $order) :

            $query->set_data_order($order['column'], $order['sort']);

        endforeach;

    endif;

    $response = $query->get()->respond();

    foreach($response['wallet'] as $i => $point) :

        $response['wallet'][$i]->meta_data = maybe_unserialize($point->meta_data);

    endforeach;

    return wp_parse_args($response,[
        'valid'    => false,
        'points'   => NULL,
        'messages' => []
    ]);

}

/**
 * Get wallet history
 * @since   1.0.0
 * @param   array  $args
 * @param   array  $table
 * @return  array
 */
function sejoli_get_all_request_fund(array $args, $table = array()) {

    $args = wp_parse_args($args,[
        'user_id'     => NULL,
        'type'        => NULL,
        'label'       => NULL,
        'valid_point' => true
    ]);

    $args['type']   = 'out';
    $args['label']  = 'request';

    $table = wp_parse_args($table, [
        'start'   => NULL,
        'length'  => NULL,
        'order'   => NULL,
        'filter'  => NULL
    ]);

    if(isset($args['date-range']) && !empty($args['date-range'])) :

        $table['filter']['date-range'] = $args['date-range'];
        unset($args['date-range']);

    endif;

    $query = SEJOLI_WALLET\Model\Wallet::reset()
                ->set_filter_from_array($args)
                ->set_data_start($table['start']);

    if(isset($table['filter']['date-range']) && !empty($table['filter']['date-range'])) :

        list($start, $end) = explode(' - ', $table['filter']['date-range']);
        $query = $query->set_filter('created_at', $start , '>=')
                    ->set_filter('created_at', $end, '<=');

    endif;

    if(0 < $table['length']) :

        $query->set_data_length($table['length']);

    endif;

    if(!is_null($table['order']) && is_array($table['order'])) :

        foreach($table['order'] as $order) :

            $query->set_data_order($order['column'], $order['sort']);

        endforeach;

    endif;

    $response = $query->get()->respond();

    foreach($response['wallet'] as $i => $point) :

        $response['wallet'][$i]->meta_data = maybe_unserialize($point->meta_data);

    endforeach;

    return wp_parse_args($response,[
        'valid'    => false,
        'points'   => NULL,
        'messages' => []
    ]);

}

/**
 * Request wallet fund
 * @since   1.0.0
 * @param   array    $args
 * @return  array    Response
 */
function sejoli_request_wallet_fund(array $args) {

    $user_id = get_current_user_id();
    $args    = wp_parse_args($args, array(
                    'value' => $amount,
                    'note'  => NULL
                ));

    $response = sejoli_get_user_wallet_data($user_id);

    if(false !== $response['valid']) :

        $wallet = $response['wallet'];

        if(floatval($args['value']) > floatval($wallet->available_cash)) :

            $response = array(
                'valid'    => false,
                'messages' => array(
                    'error' => array(
                        __('Jumlah yang anda minta untuk pencairan melebihi dana yang tersedia di akun anda', 'sejoli')
                    )
                )
            );

        else :

            $response = \SEJOLI_WALLET\Model\Wallet::reset()
                            ->set_user_id($user_id)
                            ->set_value($args['value'])
                            ->set_label('request')
                            ->set_meta_data(array('note' => $args['note']))
                            ->request_fund()
                            ->respond();

            if(false !== $response['valid']) :
                do_action('sejoli/notification/wallet/request-fund', $response['wallet']);
            endif;

        endif;

    endif;

    return $response;

}

/**
 * Get single wallet detail
 * @since   1.0.0
 * @param   integer $request_id
 * @return  array   Response
 */
function sejoli_get_wallet_detail($request_id) {

    $response = \SEJOLI_WALLET\Model\Wallet::reset()
                    ->set_id($request_id)
                    ->get_point_detail()
                    ->respond();

    return $response;

}

/**
 * Update request fund status
 * @since   1.0.0
 * @param   array    $args
 * @return  array    Response
 */
function sejoli_update_request_fund(array $args) {

    $args = wp_parse_args($args, array(
        'id'          => NULL,
        'valid_point' => false,
        'meta_data'   => array()
    ));

    $response = \SEJOLI_WALLET\Model\Wallet::reset()
                    ->set_id($args['id'])
                    ->set_valid_point($args['valid_point'])
                    ->set_meta_data($args['meta_data'])
                    ->update_request_fund()
                    ->respond();

    return $response;

}

/**
 * Manual input wallet
 * @since   1.1.0
 * @param   array   $args
 * @return  array   Response
 */
function sejoli_manual_input_wallet( $args ) {

    if( ! current_user_can('manage_sejoli_sejoli') ) :

        return array(
            'valid' => false,
            'messages'  => array(
                'error' => array(
                    __('Current user doesn\'t have capability to process this function', 'sejoli')
                )
            )
        );

    endif;

    $args = wp_parse_args($args, array(
        'user_id'     => NULL,
        'value'       => NULL,
        'refundable'  => false,
        'type'        => 'in',
        'label'       => 'manual',
        'valid_point' => false,
        'meta_data'   => array()
    ));

    $response   =  \SEJOLI_WALLET\Model\Wallet::reset()
                        ->set_user_id($args['user_id'])
                        ->set_type($args['type'])
                        ->set_value($args['value'])
                        ->set_label($args['label'])
                        ->set_refundable($args['refundable'])
                        ->set_meta_data($args['meta_data'])
                        ->set_valid_point($args['valid_point'])
                        ->manual_input()
                        ->respond();

    return wp_parse_args($response, array(
        'valid'    => false,
        'wallet'   => NULL,
        'messages' => array()
    ));

}
