<?php

namespace Sejoli_Wallet\Notification;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class RequestFund extends \SejoliSA\Notification\Main {

    /**
     * Recipient data
     * @since   1.0.0
     * @var     array
     */
    protected $recipiens;

    /**
     * Set user data
     * @var array
     */
    protected $user_data;

    /**
     * Set point data
     * @var array
     */
    protected $wallet_data;

    /**
     * Attachment for file
     * @since   1.0.0
     * @var     bool|array
     */
    public $attachments = false;

    /**
     * Construction
     */
    public function __construct() {

        add_filter('sejoli/notification/fields',    [$this, 'add_setting_fields'], 111);
        // add_filter('sejoli/notification/content',   [$this, 'set_user_detail_content'], 30, 3);
    }

    /**
     * Add notification setting fields
     * Hooked via filter, sejoli/notification/fields priority 122
     * @since   1.0.0
     * @param   array $fields All fields for notification setting form
     */
    public function add_setting_fields(array $fields) {

        $fields['request-fund'] = [
			'title'  => __('Pencairan Dana', 'sejoli'),
			'fields' => [

                // Untuk buyer
				Field::make('separator'	,'sep_request_fund_email', 	__('Email' ,'sejoli'))
					->set_help_text(__('Pengaturan konten untuk media email', 'sejoli')),

				Field::make('text', 	'request_fund_email_title',	 __('Judul' ,'sejoli'))
					->set_required(true)
					->set_default_value(__('{{user-name}}, anda telah melakukan pencairan dana sebesar {{request-fund}}', 'sejoli')),

				Field::make('rich_text', 'request_fund_email_content', __('Konten', 'sejoli'))
					->set_required(true)
					->set_default_value(sejoli_get_notification_content('request-fund')),

				Field::make('separator'	,'sep_request_fund_sms', 	__('SMS' ,'sejoli'))
					->set_help_text(__('Pengaturan konten untuk media sms', 'sejoli')),

				Field::make('textarea', 'request_fund_sms_content', __('Konten', 'sejoli'))
                    ->set_default_value(sejoli_get_notification_content('request-fund', 'sms')),

				Field::make('separator'	,'sep_request_fund_whatsapp', 	__('WhatsApp' ,'sejoli'))
					->set_help_text(__('Pengaturan konten untuk media whatsapp', 'sejoli')),

				Field::make('textarea', 'request_fund_whatsapp_content', __('Konten', 'sejoli'))
                    ->set_default_value(sejoli_get_notification_content('request-fund', 'whatsapp')),

                // Untuk admin
				Field::make('separator', 	'sep_request_fund_admin', 		__('Konten untuk admin dan lainnya','sejoli'))
					->set_classes('main-title'),

				Field::make('checkbox',		'request_fund_admin_active',	__('Aktifkan notifikasi untuk admin', 'sejoli'))
                    ->set_default_value(true),

				Field::make('separator',	'sep_request_fund_admin_email', 	__('Email' ,'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('text',		'request_fund_admin_email_recipient', __('Alamat Email Penerima', 'sejoli'))
					->set_default_value(get_option('admin_email'))
					->set_help_text(__('Gunakan tanda koma jika penerima ada lebih dari 1', 'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('text', 		'request_fund_admin_email_title',	 __('Judul' ,'sejoli'))
					->set_required(true)
					->set_default_value(__('{{user-name}} telah meminta pencairan dana sebesar {{request-fund}}', 'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('rich_text', 'request_fund_admin_email_content', __('Konten', 'sejoli'))
					->set_required(true)
					->set_default_value(sejoli_get_notification_content('request-fund-admin'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('separator'	,'sep_request_fund_admin_sms', 	__('SMS' ,'sejoli'))
					->set_help_text(__('Pengaturan konten untuk media sms', 'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('text',		'request_fund_admin_sms_recipient', __('Nomor SMS Penerima', 'sejoli'))
					->set_help_text(__('Gunakan tanda koma jika penerima ada lebih dari 1', 'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('textarea', 'request_fund_admin_sms_content', __('Konten', 'sejoli'))
					->set_help_text(__('Dengan mengosongkan isian ini, tidak akan ada notifikasi yang dikirimkan via sms'))
					->set_default_value(sejoli_get_notification_content('request-fund-admin', 'sms'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('separator'	,'sep_request_fund_admin_whatsapp', 	__('WhatsApp' ,'sejoli'))
					->set_help_text(__('Pengaturan konten untuk media whatsapp', 'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('text',		'request_fund_admin_whatsapp_recipient', __('Nomor WhatsApp Penerima', 'sejoli'))
					->set_help_text(__('Gunakan tanda koma jika penerima ada lebih dari 1', 'sejoli'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),

				Field::make('textarea', 'request_fund_admin_whatsapp_content', __('Konten', 'sejoli'))
					->set_help_text(__('Dengan mengosongkan isian ini, tidak akan ada notifikasi yang dikirimkan via whatsapp'))
					->set_default_value(sejoli_get_notification_content('request-fund-admin', 'whatsapp'))
					->set_conditional_logic([
						[
							'field'	=> 'request_fund_admin_active',
							'value' => true,
						]
					]),
			]
		];



        return $fields;
    }

    /**
     * Prepare content for notification
     * @since   1.0.0
     * @return  void
     */
    protected function set_content() {

        // ***********************
		// Setup content for buyer
		// ***********************

		$this->set_recipient_title  ('buyer', 'email', carbon_get_theme_option('request_fund_email_title'));
		$this->set_recipient_content('buyer', 'email', $this->set_notification_content(
												carbon_get_theme_option('request_fund_email_content'),
												'email',
                                                'buyer'
											 ));

		if(!empty(carbon_get_theme_option('request_fund_whatsapp_content'))) :

            $this->set_enable_send('whatsapp', 'buyer', true);
			$this->set_recipient_content('buyer', 'whatsapp', $this->set_notification_content(
		                                                carbon_get_theme_option('request_fund_whatsapp_content'),
		                                                'whatsapp',
                                                        'buyer'
                                                    ));
        endif;

		if(!empty(carbon_get_theme_option('request_fund_sms_content'))) :
            $this->set_enable_send('sms', 'buyer', true);
			$this->set_recipient_content('buyer', 'sms', $this->set_notification_content(
                                    				carbon_get_theme_option('request_fund_sms_content'),
                                    				'sms',
                                                    'buyer'
                                    			));
        endif;

		// ***********************
		// Setup content for buyer
		// ***********************

		if(false !== carbon_get_theme_option('request_fund_admin_active')) :

            $this->set_enable_send('email', 'admin', true);
    		$this->set_recipient_title('admin', 'email', carbon_get_theme_option('request_fund_admin_email_title'));
    		$this->set_recipient_content('admin', 'email', $this->set_notification_content(
													carbon_get_theme_option('request_fund_admin_email_content'),
													'email',
                                                    'admin'
												));

    		if(!empty(carbon_get_theme_option('request_fund_admin_whatsapp_content'))) :

				$this->set_enable_send('whatsapp', 'admin', true);
				$this->set_recipient_content('admin', 'whatsapp', $this->set_notification_content(
                                                			carbon_get_theme_option('request_fund_admin_whatsapp_content'),
                                                			'whatsapp',
                                                            'admin'
                                                		));
            endif;

    		if(!empty(carbon_get_theme_option('request_fund_admin_sms_content'))) :
                $this->set_enable_send('sms', 'admin', true);
				$this->set_recipient_content('admin', 'sms', $this->set_notification_content(
                                        				carbon_get_theme_option('request_fund_admin_sms_content'),
                                        				'sms',
                                                        'admin'
                                        			));
            endif;
        endif;
    }

    /**
     * Check current media recipients, the data will be stored in $this->recipients
     * @since   1.0.0
     * @param   string  $media
     * @param   string  $role
     * @return  void
     */
    protected function check_recipients($media = 'email', $role = 'admin') {

        $recipients       = carbon_get_theme_option('request_fund_' . $role . '_' . $media . '_recipient');
        $this->recipients = explode(',', $recipients);
    }

    /**
     * Add user data to shortcodes
     * Hooked via filter sejoli/notification/shortcodes, priority 10
     * @param array $shortcodes
     * @return array
     */
    public function add_shortcode_detail(array $shortcodes) {

        $meta_data = wp_parse_args(maybe_unserialize($this->wallet_data['meta_data']), array(
                        'note'  => NULL
                     ));

        $shortcodes['{{site-url}}']     = home_url('/');
        $shortcodes['{{user-name}}']    = $this->user_data['user_name'];
        $shortcodes['{{buyer-name}}']   = $this->user_data['user_name'];
        $shortcodes['{{request-fund}}'] = sejolisa_price_format($this->wallet_data['value']);
        $shortcodes['{{instruction}}']  = $meta_data['note'];

        return $shortcodes;
    }

    /**
     * Trigger to send notification
     * @since   1.0.0
     * @param   array   $wallet_data   Point data
     * @param   array   $user_data
     * @return  void
     */
    public function trigger($wallet_data, $user_data) {

        $this->wallet_data = $wallet_data;
        $this->user_data  = $user_data;
        $media_libraries  = $this->get_media_libraries();

        $this->shortcode_data = $this->add_shortcode_detail([]);
        $this->set_content();

        $this->trigger_email($user_data, $media_libraries);
        $this->trigger_whatsapp($user_data, $media_libraries);
        $this->trigger_sms($user_data, $media_libraries);

    }

    /**
     * Trigger to send email
     * @since   1.0.0
     * @param   array   $user_data          Array of recipient data
     * @param   array   $media_libraries    Array of available media libraries
     * @return  void
     */
    protected function trigger_email($user_data, $media_libraries) {

        // send email for buyer
		$media_libraries['email']->set_data([
			'user_data' => $user_data,
		]);

		$media_libraries['email']->send(
			array($user_data['user_email']),
			$this->render_shortcode($this->get_recipient_content('buyer', 'email')),
			$this->render_shortcode($this->get_recipient_title('buyer', 'email'))
		);

        if(false !== $this->is_able_to_send('email', 'admin')) :
            $this->check_recipients('email', 'admin');
            $media_libraries['email']->send(
    			$this->recipients,
    			$this->render_shortcode($this->get_recipient_content('admin', 'email')),
    			$this->render_shortcode($this->get_recipient_title('admin', 'email')),
                'admin'
    		);
        endif;
    }

    /**
     * Trigger to send whatsapp
     * @since   1.0.0
     * @param   array   $user_data          Array of recipient data
     * @param   array   $media_libraries    Array of available media libraries
     * @return  void
     */
    protected function trigger_whatsapp($user_data, $media_libraries) {

        // send whatsapp for buyer
        if(false !== $this->is_able_to_send('whatsapp', 'buyer')) :
    		$media_libraries['whatsapp']->set_data([
                'user_data' => $user_data,
    		]);

            $media_libraries['whatsapp']->send(
    			array($user_data['user_phone']),
    			$this->render_shortcode($this->get_recipient_content('buyer', 'whatsapp'))
    		);
        endif;

        if(false !== $this->is_able_to_send('whatsapp', 'admin')) :
            $this->check_recipients('whatsapp', 'admin');
            $media_libraries['whatsapp']->send(
    			$this->recipients,
    			$this->render_shortcode($this->get_recipient_content('admin', 'whatsapp'))
    		);
        endif;

    }

    /**
     * Trigger to SMS whatsapp
     * @since   1.0.0
     * @param   array   $user_data          Array of recipient data
     * @param   array   $media_libraries    Array of available media libraries
     * @return  void
     */
    protected function trigger_sms($user_data, $media_libraries) {

        // send sms for buyer
        if(false !== $this->is_able_to_send('sms', 'buyer')) :
    		$media_libraries['sms']->set_data([
                'user_data' => $user_data,
    		]);

            $media_libraries['sms']->send(
    			array($user_data['user_phone']),
    			$this->render_shortcode($this->get_recipient_content('buyer', 'sms'))
    		);
        endif;

        if(false !== $this->is_able_to_send('sms', 'admin')) :
            $this->check_recipients('sms', 'admin');
            $media_libraries['sms']->send(
                $this->recipients,
                $this->render_shortcode($this->get_recipient_content('admin', 'sms'))
            );
        endif;

    }
}
