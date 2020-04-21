<?php

namespace Sejoli_Wallet\Front;

class Wallet {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Submit request fund
     * Hooked via action sejoli_ajax_submit-request-fund
     * @since   1.0.0
     * @return  json
     */
    public function submit_request_fund() {

        $valid = true;

        $response = array(
            'valid'   => false,
            'message' => NULL
        );

        $params = wp_parse_args($_POST, array(
            'amount'      => 0.0,
            'information' => NULL,
            'noncekey'    => NULL
        ));


        if(
            is_user_logged_in() &&
            wp_verify_nonce($params['noncekey'], 'sejoli-request-fund')
        ) :
            $messages = array();

            if(empty($params['amount'])) :
                $valid      = false;
                $messages[] = __('Jumlah pencairan dana kosong', 'sejoli');
            endif;

            if(empty($params['information'])) :
                $valid      = false;
                $messages[] = __('Informasi terkait rekening penerima kosong', 'sejoli');
            endif;

            if($valid) :
                $response = sejoli_request_wallet_fund(array(
                    'value' => $params['amount'],
                    'note'  => $params['information']
                ));

                if(false !== $response['valid']) :
                    $response['message'] = __('Permintaan pencairan dana telah dikirim. Kami akan mengecek dan memvalidasi pencairan dana anda', 'sejoli');
                else :
                    $response['message'] = implode('<br />', $response['messages']['error']);
                endif;
            else :
                $response['message'] = implode('<br />', $messages);
            endif;

        endif;

        $response['data']   = $_POST;

        echo wp_send_json($response);
        exit;
    }
}
