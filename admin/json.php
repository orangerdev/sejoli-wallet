<?php

namespace Sejoli_Wallet\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Sejoli_Reward
 * @subpackage Sejoli_Reward/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sejoli_Reward
 * @subpackage Sejoli_Reward/admin
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Json extends \SejoliSA\JSON {

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
     * Set for table data via AJAX
     * Hooked via action wp_ajax_sejoli-wallet-table, priority 1
     * @since   1.0.0
     * @return  array
     */
    public function ajax_set_for_table() {

        $table  = $this->set_table_args($_POST);
        $params = wp_parse_args($_POST, array(
            'nonce' => NULL
        ));

        $total = 0;
        $data  = [];

        if(wp_verify_nonce($params['nonce'], 'sejoli-render-wallet-table')) :

    		$return = sejoli_get_all_user_wallet($table['filter']);

            if(false !== $return['valid']) :

                foreach($return['wallet'] as $_data) :

                    $data[] = array(
                        'user_id'         => $_data->user_id,
                        'display_name'    => $_data->display_name,
                        'user_email'      => $_data->user_email,
                        'cash_value'      => sejolisa_price_format($_data->cash_value),
                        'point_value'     => sejolisa_price_format($_data->point_value),
                        'used_value'      => sejolisa_price_format($_data->used_value),
                        'available_cash'  => sejolisa_price_format($_data->available_cash),
                        'available_total' => sejolisa_price_format($_data->available_total),
                        'detail_url'      => add_query_arg(array(
                                                'page'    => 'sejoli-wallet',
												'user_id' => $_data->user_id
                                            ), admin_url('admin.php'))
                    );

                endforeach;

                $total = count($data);

            endif;

        endif;

        echo wp_send_json([
            'table'           => $table,
            'draw'            => $table['draw'],
            'data'            => $data,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ]);

        exit;
    }

	/**
     * Get wallet history for a user
     * Hooked via filter wp_ajax_sejoli-single-wallet-table, priority 1
     * @since   1.0.0
     * @return  array
     */
    public function ajax_set_single_user_for_table() {

        $table  = $this->set_table_args($_POST);
        $params = wp_parse_args($_POST, array(
            'nonce' 	=> NULL,
			'user_id'   => NULL
        ));

        $total = 0;
        $data  = [];

        if(wp_verify_nonce($params['nonce'], 'sejoli-render-single-wallet-table')) :

			$table['filter']['user_id']	= (empty($params['user_id'])) ? get_current_user_id() : intval($params['user_id']);

    		$return = sejoli_wallet_get_history($table['filter'], $table);

            if(false !== $return['valid']) :

                foreach($return['wallet'] as $_data) :

					$detail = '';

					switch($_data->label) :

						case 'cashback' :
							$product = sejolisa_get_product($_data->product_id);
							$detail  = sprintf(
											__('Cashback dari order %s untuk produk %s', 'sejoli'),
											$_data->order_id,
											$product->post_title
									   );
							break;

						case 'affiliate' :
							$product = sejolisa_get_product($_data->product_id);
							$detail  = sprintf(
											__('Poin dari affiliasi order %s untuk produk %s, tier %s', 'sejoli'),
											$_data->order_id,
											$product->post_title,
											$_data->meta_data['tier']
										);
							break;

						case 'order' :

							$detail = sprintf(
										__('Pembayaran untuk order %s', 'sejoli'),
										$_data->order_id
									  );
							break;

						case 'request' :

							$detail = __('Request pencairan', 'sejoli');
							break;

					endswitch;

                    $data[] = array(
						'created_at'	=> date('Y/m/d', strtotime($_data->created_at)),
						'detail'        => $detail,
                        'point' 		=> sejolisa_price_format($_data->value),
                        'type'  		=> $_data->type,
						'refundable'    => boolval($_data->refundable)
                    );

                endforeach;

                $total = count($data);

            endif;

        endif;

        echo wp_send_json([
            'table'           => $table,
            'draw'            => $table['draw'],
            'data'            => $data,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ]);

        exit;
    }

	/**
     * Set for request fund table data via AJAX
     * Hooked via action wp_ajax_sejoli-request-fund-table, priority 1
     * @since   1.0.0
     * @return  array
     */
    public function ajax_set_request_fund_for_table() {

        $table  = $this->set_table_args($_POST);
        $params = wp_parse_args($_POST, array(
            'nonce' => NULL
        ));

        $total = 0;
        $data  = [];

        if(wp_verify_nonce($params['nonce'], 'sejoli-render-request-fund-table')) :

    		$return = sejoli_get_all_request_fund($table['filter'], $table);

            if(false !== $return['valid']) :

                foreach($return['wallet'] as $_data) :

                    $data[] = array(
						'created_at'   => date('Y/m/d', strtotime($_data->created_at)),
                        'user_id'      => $_data->user_id,
                        'display_name' => $_data->display_name,
                        'user_email'   => $_data->user_email,
                        'value'        => sejolisa_price_format($_data->value),
                        'meta_data'    => maybe_unserialize($_data->meta_data)
                    );

                endforeach;

                $total = count($data);

            endif;

        endif;

        echo wp_send_json([
            'table'           => $table,
            'draw'            => $table['draw'],
            'data'            => $data,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ]);

        exit;
    }
}
