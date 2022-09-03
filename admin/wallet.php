<?php

namespace Sejoli_Wallet\Admin;

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
	 * Check if current admin page is a sejoli page
	 * Hooked via filter sejoli/admin/is-sejoli-page, priority 1111
	 * @param  boolean $is_sejoli_page
	 * @return boolean
	 */
	public function is_current_page_sejoli_page($is_sejoli_page) {

		global $pagenow;

		if(
			isset($_GET['page']) &&
            in_array($_GET['page'], array('sejoli-wallet', 'sejoli-request-fund'))
		) :
			return true;
		endif;

		return $is_sejoli_page;
	}

    /**
	 * Set local JS variables
	 * Hooked via filter sejoli/admin/js-localize-data, priority 12
	 * @since 	1.0.0
	 * @param 	array $js_vars
	 * @return 	array
	 */
	public function set_localize_js_vars($js_vars) {

		$js_vars['wallet'] = array(
			'table'	=> array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-wallet-table'
					), admin_url('admin-ajax.php')
				),
				'nonce'	=> wp_create_nonce('sejoli-render-wallet-table')
			),
			'single_table'	=> array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-single-wallet-table'
					), admin_url('admin-ajax.php')
				),
				'nonce'   => wp_create_nonce('sejoli-render-single-wallet-table'),
				'user_id' => (isset($_GET['user_id'])) ? intval($_GET['user_id']) : get_current_user_id()
			),
			'request_table' => array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-request-fund-table'
					), admin_url('admin-ajax.php')
				),
				'nonce'	=> wp_create_nonce('sejoli-render-request-fund-table')
			),
			'update-request'	=> array(
				'ajaxurl'	=> add_query_arg(array(
						'action' => 'sejoli-update-request-fund'
					), admin_url('admin-ajax.php')
				),
				'nonce'   => wp_create_nonce('sejoli-update-request-fund')
			),
			'export_prepare' =>  array(
				'ajaxurl' => add_query_arg(array(
					'action' => 'sejoli-request-wallet-export-prepare'
				), admin_url('admin-ajax.php')),
				'nonce' => wp_create_nonce('sejoli-request-wallet-export-prepare')
			),
			'export_single_prepare' =>  array(
				'ajaxurl' => add_query_arg(array(
					'action' => 'sejoli-request-wallet-export-single-prepare'
				), admin_url('admin-ajax.php')),
				'nonce' => wp_create_nonce('sejoli-request-wallet-export-single-prepare')
			),
		);

		return $js_vars;
	}

    /**
	 * Add new submenu under Sejoli
	 * Hooked via action admin_menu, priority 1002
	 * @since 	1.0.0
	 * @since 	1.1.0	Add manual input wallet data menu
	 * @return	void
	 */
	public function add_sejoli_submenu() {

        add_submenu_page(
            'crb_carbon_fields_container_sejoli.php',
            __('Data Saldo Semua User', 'sejoli'),
            __('Saldo', 'sejoli'),
            'manage_sejoli_sejoli',
            'sejoli-wallet',
            array($this, 'display_wallet_page')
        );

		/**
		 * @since 	1.1.0
		 */
		add_submenu_page(
			'crb_carbon_fields_container_sejoli.php',
			__('Form Perubahan Saldo', 'sejoli'),
			__('Perubahan Saldo', 'sejoli'),
			'manage_sejoli_sejoli',
			'sejoli-wallet-input-form',
			array($this, 'display_wallet_input_form')
		);

		add_submenu_page(
            'crb_carbon_fields_container_sejoli.php',
            __('Data Permintaan Pencairan Dana', 'sejoli'),
            __('Pencairan Dana', 'sejoli'),
            'manage_sejoli_sejoli',
            'sejoli-request-fund',
            array($this, 'display_request_fund_page')
        );



	}

    /**
     * Display wallet page
     * @since   1.0.0
     * @return  void
     */
    public function display_wallet_page() {

		if(isset($_GET['user_id'])) :
			$user 	= sejolisa_get_user(intval($_GET['user_id']));
			require_once( plugin_dir_path( __FILE__ ) . 'partials/single-user-wallet.php' );
		else :
        	require_once( plugin_dir_path( __FILE__ ) . 'partials/user-wallet.php' );
		endif;

    }

	/**
	 * Display notice in wallet page
	 * Hooked via action admin_notices, priority 1.1.0
	 * @since 	1.1.0
	 * @return 	void
	 */
	public function display_notice() {

		if(
			isset($_GET['page'] ) &&
			'sejoli-wallet-input-form' === $_GET['page']
		) :
		?>
			<div class="sejoli-wallet-form-response notice" style='display:none'>

			</div>
		<?php
		endif;

	}

	/**
     * Display request fund page
     * @since   1.0.0
     * @return  void
     */
    public function display_request_fund_page() {

        require_once( plugin_dir_path( __FILE__ ) . 'partials/request-fund.php' );

    }

	/**
	 * Display wallet input form
	 * Called internally
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function display_wallet_input_form() {

		require_once( plugin_dir_path( __FILE__ ) . 'partials/input-form.php' );

	}

	/**
	 * Add manual input wallet data
	 * Hooked via action wp_ajax_nopriv_add-input-wallet-data, priority 1
	 * @since 1.1.0
	 */
	public function add_manual_input_data() {

		$response 	= array(
			'success' => false,
			'message' => __('Ada kesalahan terjadi di sistem', 'sejoli')
		);

		if(
			isset( $_POST['noncekey'] ) &&
			check_ajax_referer( 'sejoli-add-wallet-data', 'noncekey' ) &&
			isset( $_POST['data'] ) &&
			current_user_can( 'manage_sejoli_sejoli' )
		) :

			$user 	= wp_get_current_user();

			$data 	= wp_parse_args( $_POST['data'], array(
				'user_id'    => 0,
				'wallet'     => 0,
				'operation'  => 'add',
				'refundable' => 1,
				'note'       => ''
			));

			$wallet_respond = sejoli_manual_input_wallet( array(
				'order_id'    => 0,
				'product_id'  => 0,
				'user_id'     => $data['user_id'],
				'value'       => $data['wallet'],
				'refundable'  => $data['refundable'],
				'type'		  => 'add' === $data['operation'] ? 'in' : 'out',
				'label'       => 'manual',
				'valid_point' => true,
				'meta_data'   => array(
					'note'	=> esc_html( $data['note'] ),
					'input'	=> sprintf( __('Input by %s', 'sejoli'), $user->display_name )
				)
			) );

			if( false !== $wallet_respond['valid'] ) :

				$user 	= get_user_by( 'ID', $data['user_id'] );
				$response['success']	= true;
				$response['message']	= sprintf(
											__('%s dana sebesar %s untul wallet %s sudah berhasil diproses.', 'sejoli' ),
											(  'add' === $data['operation'] ) ? 'Penambahan' : 'Pengurangan',
											sejolisa_price_format( $data['wallet'] ),
											$user->display_name
										  );

			else :

				$response['message'] = implode( '<br />', $wallet_respond['messages']['error'] );

			endif;

		endif;

		echo wp_send_json($response);
		exit;
	}

	/**
	 * Export order data to CSV
	 * Hooked via action sejoli_ajax_sejoli-wallet-export, priority 1
	 * @since 	1.1.0
	 * @since 	1.5.3 	Add more information data to CSV
	 * @return 	void
	 */
	public function export_csv() {

		$post_data = wp_parse_args($_GET,[
			'sejoli-nonce' => NULL,
			'backend'      => false
		]);

		if( wp_verify_nonce( $post_data['sejoli-nonce'], 'sejoli-wallet-export') ) :

			$filename = 'export-pencairan-dana-' . strtoupper( sanitize_title( get_bloginfo('name') ) ) . '-' . date( 'Y-m-d-H-i-s', current_time('timestamp') );

			if( !current_user_can('manage_sejoli_sejoli') || false === $post_data['backend'] ) :

				$post_data['user_id'] = get_current_user_id();

			endif;

			if( isset( $post_data['user_id'] ) ) :

				$filename .= '-'. $post_data['user_id'];

			endif;

			unset( $post_data['backend'], $post_data['sejoli-nonce'] );

			$response = sejoli_get_all_request_fund( $post_data );

			$csv_data    = [];
			$csv_data[0] = array(
				'created_at', 'name', 'email', 'value', 'note', 'accepted',
			);

			$j    = 1;
			$data = [];

			foreach( $response['wallet'] as $i => $_data ) :

                $data[$i] = array(
					'id'           => $_data->ID,
					'created_at'   => date( 'Y/m/d', strtotime( $_data->created_at ) ),
                    'user_id'      => $_data->user_id,
                    'display_name' => $_data->display_name,
                    'user_email'   => $_data->user_email,
                    'value'        => sejolisa_price_format( $_data->value ),
                    'meta_data'    => maybe_unserialize( $_data->meta_data )
                );

                if( array_key_exists( 'note', $data[$i]['meta_data'] ) ) :

					$data[$i]['meta_data']['note'] = nl2br( $data[$i]['meta_data']['note'] );

				endif;

				if( array_key_exists( 'accepted', $data[$i]['meta_data'] ) ) :

					$data[$i]['meta_data']['accepted'] = date( 'd F Y', strtotime( $data[$i]['meta_data']['accepted'] ) );

				endif;

				$csv_data[$j] = array(
					$data[$i]['created_at'],
					$data[$i]['display_name'],
					$data[$i]['user_email'],
					$data[$i]['value'],
					$data[$i]['meta_data']['note'],
					( isset( $data[$i]['meta_data']['accepted'] ) ? $data[$i]['meta_data']['accepted'] : '' )
				);

				$j++;

			endforeach;

			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '.csv"' );

			$fp = fopen( 'php://output', 'wb' );
			foreach( $csv_data as $line ) :

			    fputcsv( $fp, $line, ',' );

			endforeach;
			fclose( $fp );

		endif;

		exit;

	}

}
