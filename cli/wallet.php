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
                property_exists($order['product'], 'reward_point') &&
                0 < $order['product']->reward_point
            ) :

                $point_response = sejoli_reward_add_point(array(
                    'order_id'     => $order['ID'],
                    'product_id'   => $order['product']->ID,
                    'order_status' => $order['status'],
                    'user_id'      => $order['user_id'],
                    'point'        => $order['product']->reward_point
                ));

                if(false !== $point_response['valid']) :

                    $this->message(
                        sprintf(
                            __('%s Point from order %s and user %s already added', 'ttom'),
                            $point_response['point']['point'],
                            $point_response['point']['order_id'],
                            $point_response['point']['user_id']
                        ), 'success');

                else :

                    $this->message($point_response['messages']['error'], 'error');

                endif;

            endif;

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
