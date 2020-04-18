<?php

namespace Sejoli_Wallet\CLI;

class Wallet extends \SejoliSA\CLI {

    /**
     * Add cashback
     *
     * <order_id>
     * : The order id
     *
     *  wp sejolisa wallet add_cashback 123
     *
     * @when after_wp_load
     */
    public function add_cashback(array $args) {

        list($order_id) = $args;

        $response = sejolisa_get_order(array('ID' => $order_id));

        if(false !== $response['valid']) :

            $order  = $response['orders'];

            if(
                property_exists($order['product'], 'cashback') &&
                $order['product']->cashback['activate']
            ) :

                $cashback = sejoli_calculate_cashback($order, $order['user_id']);

                $cashback_response = sejoli_add_cashback(array(
                    'order_id'    => $order['ID'],
                    'product_id'  => $order['product']->ID,
                    'user_id'     => $order['user_id'],
                    'value'       => $cashback['total'],
                    'refundable'  => $cashback['refundable'],
                    'label'       => 'cashback',
                    'valid_point' => ('completed' !== $order['status']) ? false : true,
                    'meta_data'   => array(
                        'setup' => $cashback['setup'],
                        'group' => $cashback['group']
                    )
                ));

                if(false !== $cashback_response['valid']) :

                    $this->message(
                        sprintf(
                            __('Cashback %s from order %s and user %s already added', 'ttom'),
                            $cashback_response['wallet']['value'],
                            $cashback_response['wallet']['order_id'],
                            $cashback_response['wallet']['user_id']
                        ), 'success');

                else :

                    $this->message($cashback_response['messages']['error'], 'error');

                endif;

            endif;

        endif;
    }

    /**
     * Update wallet valid point
     *
     * <order_id>
     * : The order id
     *
     * <valid>
     * : Validity status
     *
     *  wp sejolisa wallet update_valid_point 2227 1
     *
     * @when after_wp_load
     */
    public function update_valid_point(array $args) {

        list($order_id, $valid) = $args;

        $response = sejolisa_get_order(array('ID' => $order_id));

        if(false !== $response['valid']) :

            $order  = $response['orders'];

            $cashback_response = sejoli_update_wallet_valid_point(array(
                'order_id'    => $order['ID'],
                'valid_point' => boolval($valid)
            ));

            __debug($cashback_response);

        endif;
    }

    /**
     * Get user wallet
     *
     * <user_id>
     * : The user id
     *
     *  wp sejolisa wallet get_user_wallet 14
     *
     * @when after_wp_load
     */
    public function get_user_wallet(array $args) {

        list($user_id)  = $args;

        $response = sejoli_reward_get_user_point($user_id);

        if(false !== $response['valid']) :
            $this->render(
                array(
                    (array) $response['point']
                ),
                'table',
                array(
                    'user_id',
                    'added_point',
                    'reduce_point',
                    'available_point',
                )
            );
        else :
            $this->message($response['messages']);
        endif;
    }

    /**
     * Get available user wallet
     *
     *  wp sejolisa wallet get_all_user_wallet
     *
     * @when after_wp_load
     */
    public function get_all_user_wallet() {

        $response = sejoli_reward_get_all_user_point();

        if(false !== $response['valid']) :

            $data = array();

            foreach($response['points'] as $i => $_data) :
                $data[$i]   = (array) $_data;
            endforeach;

            $this->render(
                $data,
                'table',
                array(
                    'user_id',
                    'display_name',
                    'user_email',
                    'added_point',
                    'reduce_point',
                    'available_point',
                )
            );
        else :
            $this->message($response['messages']);
        endif;
    }

    /**
     * Get possible cashback by an order
     *
     * <order_id>
     * : The order id
     *
     * <user_id>
     * : The user id
     *
     *  wp sejolisa wallet calculate_cashback 2193 14
     *
     * @when after_wp_load
     */
    public function calculate_cashback(array $args) {

        list($order_id, $user_id) = $args;

        wp_set_current_user( $user_id );

        $setup = $type = $refundable = $total_cashback = false;
        $buyer_group    = '-';
        $order_response = sejolisa_get_order(array('ID' => $order_id));

        if(false !== $order_response['valid']) :

            $order       = $order_response['orders'];
            $product     = sejolisa_get_product($order['product_id']);
            $user_group  = sejolisa_get_user_group($order['user_id']);
            $buyer_group = (isset($user_group['name'])) ? $user_group['name'] : $buyer_group;

            if($product->cashback['activate']) :

                if('percentage' === $product->cashback['type']) :

                    $total_cashback = round($order['grand_total'] * $product->cashback['value'] / 100);

                else :

                    $total_cashback    = $order['quantity'] * $product->cashback['value'];

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

        endif;

        $this->render(array(
            array(
                'product'        => $order['product']->post_title,
                'buyer_group'    => $buyer_group,
                'total_cashback' => $total_cashback,
                'refundable'     => $refundable,
                'setup'           => $setup,
            )
        ),'yaml',array(
            'product',
            'buyer_group',
            'total_cashback',
            'refundable',
            'setup'
        ));
    }

}
