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

						case 'manual'	:

							$detail	= $_data->meta_data['note'] . ' ' . $_data->meta_data['input'];
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

                foreach($return['wallet'] as $i => $_data) :

                    $data[$i] = array(
						'id'           => $_data->ID,
						'created_at'   => date('Y/m/d', strtotime($_data->created_at)),
                        'user_id'      => $_data->user_id,
                        'display_name' => $_data->display_name,
                        'user_email'   => $_data->user_email,
                        'detail_url'   => add_query_arg(array(
                                            'page'    => 'sejoli-wallet',
											'user_id' => $_data->user_id
                                        ), admin_url('admin.php')),
                        'value'        => sejolisa_price_format($_data->value),
                        'meta_data'    => maybe_unserialize($_data->meta_data)
                    );

					if(array_key_exists('note', $data[$i]['meta_data'])) :
						$data[$i]['meta_data']['note'] = nl2br($data[$i]['meta_data']['note']);
					endif;

					if(array_key_exists('accepted', $data[$i]['meta_data'])) :
						$data[$i]['meta_data']['accepted'] = date('d F Y', strtotime($data[$i]['meta_data']['accepted']));
					endif;

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
	 * Update request status
	 * Hooked via action wp_ajax_sejoli-update-request, priority 1
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function ajax_update_request_status() {

		$response = array(
			'valid'   => false,
			'message' => __('Terjadi error saat mengupdate', 'sejoli')
		);

		$args 	= wp_parse_args($_POST, array(
			'request_id' => NULL,
			'type'       => NULL,
			'noncekey'   => NULL
		));

		if(
			wp_verify_nonce($args['noncekey'], 'sejoli-update-request') &&
			!empty($args['request_id']) &&
			!empty($args['type'])
		) :

			$point_response = sejoli_get_wallet_detail($args['request_id']);

			if(false !== $point_response['valid']) :

				$point              = (array) $point_response['point'];
				$point['meta_data'] = maybe_unserialize($point['meta_data']);

				$params = array(
					'id'          => $point['ID'],
					'valid_point' => boolval($point['valid_point']),
					'meta_data'	  => $point['meta_data']
				);

				if('reject' === $args['type']) :

					$params['valid_point'] = false;
					$response['message'] = __('Permintaan pencairan dana telah dibatalkan', 'sejoli');

				elseif('accept' === $args['type']) :

					$params['meta_data']['accepted'] = current_time('mysql');
					$response['message'] = __('Permintaan pencairan dana telah diterima', 'sejoli');

				endif;

				sejoli_update_request_fund($params);
				$response['valid'] = true;

			endif;

		endif;

		echo wp_send_json($response);
		exit;
	}

	/**
     * Prepare for exporting order data
     * Hooked via wp_ajax_sejoli-request-wallet-export-prepare, priority 1
     * @since   1.0.2
     * @return  void
     */
    public function prepare_for_exporting() {

        $table  = \SEJOLI_WALLET\Model\Wallet::set_table_args($_POST);
        $params = wp_parse_args($_POST, array(
            'nonce' => NULL
        ));

        if(
            wp_verify_nonce( $_POST['nonce'], 'sejoli-request-wallet-export-prepare') ) :

            $wallet_data = sejoli_get_all_user_wallet($table['filter']);

            if( !is_wp_error( $wallet_data )) :

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="sejoli-wallet-per-'.date('d-m-Y').'.csv"');

                $fp = fopen('php://output', 'wb');

                fputcsv( $fp, array(
                    'user_id',
                    'name',
                    'email',
                    'cash',
                    'point',
                    'used',
                    'available_cash',
                    'available_total'
                ));

                foreach($wallet_data['wallet'] as $_data) :

                    fputcsv( $fp, array(
                        $_data->user_id,
                        $_data->display_name,
                        $_data->user_email,
                        $_data->cash_value,
                        $_data->point_value,
                        $_data->used_value,
                        $_data->available_cash,
                        $_data->available_total
                    ));

                endforeach;

                fclose($fp);

            else :

                wp_send_json_error( $wallet_data );

            endif;

        endif;

        exit;
    
    }

    /**
     * Set wallet label detail
     * @since   1.0.0
     * @param   object  $wallet_data        Single wallet data
     * @return  string
     */
    public function set_wallet_label_detail( object $wallet_data ) {

        $detail = '';

        switch($wallet_data->label) :

            case 'cashback' :
                $product = sejolisa_get_product($wallet_data->product_id);
                $detail  = sprintf(
                                __('Cashback dari order %s untuk produk %s', 'sejoli'),
                                $wallet_data->order_id,
                                $product->post_title
                           );
                break;

            case 'affiliate' :
                $product = sejolisa_get_product($wallet_data->product_id);
                $detail  = sprintf(
                                __('Poin dari affiliasi order %s untuk produk %s, tier %s', 'sejoli'),
                                $wallet_data->order_id,
                                $product->post_title,
                                $wallet_data->meta_data['tier']
                            );
                break;

            case 'order' :

                $detail = sprintf(
                            __('Pembayaran untuk order %s', 'sejoli'),
                            $wallet_data->order_id
                          );
                break;

            case 'request' :

                $detail = __('Request pencairan', 'sejoli');
                break;

            case 'manual'   :

                $detail = $wallet_data->meta_data['note'] . ' ' . $wallet_data->meta_data['input'];
                break;

        endswitch;

        return apply_filters( 'sejoli/wallet/note', $detail, $wallet_data);

    }

    /**
     * Create csv file for a single user
     * Hooked via action wp_ajax_sejoli-request-wallet-export-single-prepare, priority 1
     * @return  file|json
     */
    public function prepare_for_exporting_for_single() {

        $table  = \SEJOLI_WALLET\Model\Wallet::set_table_args($_POST);
        $params = wp_parse_args($_POST, array(
            'nonce'   => NULL,
            'user_id' => NULL
        ));

        $data = [];

        if(wp_verify_nonce($params['nonce'], 'sejoli-request-wallet-export-single-prepare')) :

            $table['filter']['user_id'] = (empty($params['user_id'])) ? get_current_user_id() : intval($params['user_id']);

            $table['length'] = 0;

            $return = sejoli_wallet_get_history($table['filter'], $table);

            if(false !== $return['valid']) :

                $user = sejolisa_get_user($params['user_id']);
                $name = strtoupper( sanitize_title( $user->data->display_name ) );

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="sejowoo-wallet-by-user-' . $name . '-' . date('d-m-Y') . '.csv"');

                $fp = fopen('php://output', 'wb');

                fputcsv( $fp, array(
                    'date',
                    'detail',
                    'point',
                    'type',
                    'refundable',
                ));

                foreach($return['wallet'] as $_data) :

                    fputcsv( $fp, array(
                        date('Y/m/d', strtotime($_data->created_at)),
                        $this->set_wallet_label_detail($_data),
                        $_data->value,
                        $_data->type,
                        (true === boolval($_data->refundable) ) ? 'Y' : 'N'
                    ));

                endforeach;

                fclose($fp);

            endif;

        endif;

        exit;         

    }

}
