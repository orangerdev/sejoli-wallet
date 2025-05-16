<?php

namespace Sejoli_Wallet\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Notification {

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
	 * Notification libraries
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	array
	 */
	protected $libraries = false;

    /**
	 * Series of notification files
	 * @since	1.0.0
	 * @var 	array
	 */
	protected $notification_files = array(
		'request-fund-admin',
		'request-fund',
		'cancel-request-fund-admin',
		'cancel-request-fund'
	);

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

		add_action('admin_init',	array($this, 'test'));
	}

	public function test() {
		if(isset($_GET['dylan'])) :
			exit;
		endif;
	}

    /**
	 * Modification directory
	 *
	 * Hooked via filter sejoli/email/template-directory, 	 priority 11
	 * Hooked via filter sejoli/sms/template-directory, 	 priority 11
	 * Hooked via filter sejoli/whatsapp/template-directory, priority 11
	 *
	 * @since 	1.0.0
	 * @param 	string 	$directory_path
	 * @param 	string 	$filename
	 * @param 	string 	$media
	 * @param 	array 	$vars
	 * @return 	string
	 */
	public function set_notification_directory($directory_path, $filename, $media, $vars) {

		if(in_array($filename, $this->notification_files)) :
			$directory_path = SEJOLI_WALLET_DIR . 'template/' . $media . '/';
		endif;

		return $directory_path;
	}

    /**
     * Add custom notification libraries
     * Hooked via filter sejoli/notification/libraries, priority 12
     * @since   1.0.0
     * @param   $libraries [description]
     */
    public function add_libraries($libraries) {

        require_once( SEJOLI_WALLET_DIR . 'notification/request-fund.php');
        require_once( SEJOLI_WALLET_DIR . 'notification/cancel-request-fund.php');

        $libraries['request-fund']          = new \Sejoli_Wallet\Notification\RequestFund;
        $libraries['cancel-request-fund']   = new \Sejoli_Wallet\Notification\CancelRequestFund;

		$this->libraries = $libraries;

        return $libraries;
    }

	/**
	 * Add notification setting fields
	 * Hooke via filter sejoli/notification/fields, priority 111
	 * @since 	1.0.0
	 * @param 	array $fields
	 */
	public function set_notification_fields(array $fields) {

		$fields['cashback'] = [
			'title'		=> __('Informasi Cashback', 'sejoli'),
			'fields'	=> array(
				Field::make('separator', 'sep_info_cashback_email', __('Email', 'sejoli'))
					->set_help_text(__('Pengturan konten untuk media email', 'sejoli')),

				Field::make('rich_text', 'info_cashback_email', __('Konten', 'sejoli'))
					->set_required(true)
					->set_default_value('Selamat, Anda mendapatkan poin sebesar {{cashback}}! Total dana anda sekarang adalah {{wallet-total}}'),

				Field::make('separator', 'sep_info_cashback_whatsapp', __('Whatsapp', 'sejoli'))
					->set_help_text(__('Pengturan konten untuk media whatsapp', 'sejoli')),

				Field::make('textarea', 'info_cashback_whatsapp', __('Konten', 'sejoli'))
					->set_required(true)
					->set_default_value('Selamat, Anda mendapatkan poin sebesar {{cashback}}! Total dana anda sekarang adalah {{wallet-total}}.'),

				Field::make('separator', 'sep_info_cashback_sms', __('SMS', 'sejoli'))
					->set_help_text(__('Pengturan konten untuk media SMS', 'sejoli')),

				Field::make('textarea', 'info_cashback_sms', __('Konten', 'sejoli'))
					->set_required(true)
					->set_default_value('Total dana anda sekarang {{wallet-total}}'),

			)
		];

		return $fields;
	}

	/**
	 * Send request fund notification
	 * Hooked via action sejoli/notification/wallet/request-fund, priority 11
	 * @since 	1.0.0
	 * @param  	array $wallet_data
	 * @return 	void
	 */
	public function send_request_fund_notification($wallet_data) {

		$wallet_data = (array) $wallet_data;
		$user        = sejolisa_get_user($wallet_data['user_id']);

		$this->libraries['request-fund']->trigger(
			$wallet_data,
			array(
				'user_name'  => $user->display_name,
				'user_email' => $user->user_email,
				'user_phone' => $user->meta->phone
			));

	}

	/**
	 * Send cancel request fund notification
	 * Hooked via action sejoli/notification/wallet/cancel-request-fund, priority 11
	 * @since 	1.0.0
	 * @param  	array $wallet_data
	 * @return 	void
	 */
	public function send_cancel_request_fund_notification($wallet_data) {

		$wallet_data = (array) $wallet_data;
		$user        = sejolisa_get_user($wallet_data['user_id']);

		$this->libraries['reward-cancel']->trigger(
			(array) $wallet_data,
			array(
				'user_name'  => $user->display_name,
				'user_email' => $user->user_email,
				'user_phone' => $user->meta->user_phone
			));

	}
}
