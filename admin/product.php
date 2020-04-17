<?php

namespace Sejoli_Wallet\Admin;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

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
class Product {

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
     * Add cashback setup in product fields
     * Hooked via filter sejoli/product/fields, priority 10
     * @since   1.0.0
     * @param   array   $fields
     * @return  array
     */
    public function set_product_fields($fields) {

        $fields[]   = array(
            'title'     => __('Cashback', 'sejoli'),
            'fields'    => array(
                Field::make('separator', 'sep_cashback', __('Pengaturan Cashback', 'sejoli'))
                    ->set_classes('sejoli-with-help'),

                Field::make('text', 'cashback_value',   __('Nilai cashback', 'sejoli'))
                    ->set_attribute('type', 'number')
                    ->set_attribute('min', 0)
                    ->set_default_value(0)
                    ->set_width(50),

                Field::make('select', 'cashback_type', __('Tipe cashback', 'sejoli'))
                    ->set_options(array(
                        'fixed'      => __('Tetap', 'sejoli'),
                        'percentage' => __('Persentase', 'sejoli')
                    ))
                    ->set_width(50),

                Field::make('text', 'cashback_max', __('Maksimal cashback (Rp). '))
                    ->set_attribute('type', 'number')
                    ->set_attribute('min', 0)
                    ->set_default_value(0)
                    ->set_help_text(
                        __('Kosongkan jika tidak ada maksimum cashback.', 'sejoli')
                    ),

                Field::make('checkbox', 'cashback_refundable', __('Cashback bisa dicairkan?', 'sejoli'))
                    ->set_help_text(
                        __('Cashback bisa dibelanjakan kembali, namun jika cashback bisa dicair maka silahkan aktifkan fitur ini', 'sejoli')
                    )
            )
        );

        return $fields;
    }

	/**
	 * Add cashback setting in each product setup in user group fields
	 * Hooked via filter sejoli/user-group/fields, priority 11
	 * @since 	1.0.0
	 * @param 	array $fields
	 * @return 	array
	 */
	public function set_user_group_fields($fields) {

		$extra_fields = array(
			Field::make('separator', 'sep_cashback', __('Pengaturan Cashback', 'sejoli'))
				->set_classes('sejoli-with-help'),

			Field::make('checkbox',	'cashback_activate', __('Aktifkan cashback', 'sejoli')),

			Field::make('text', 'cashback_value',   __('Nilai cashback', 'sejoli'))
				->set_attribute('type', 'number')
				->set_attribute('min', 0)
				->set_default_value(0)
				->set_width(50)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				)),

			Field::make('select', 'cashback_type', __('Tipe cashback', 'sejoli'))
				->set_options(array(
					'fixed'      => __('Tetap', 'sejoli'),
					'percentage' => __('Persentase', 'sejoli')
				))
				->set_width(50)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				)),

			Field::make('text', 'cashback_max', __('Maksimal cashback (Rp). '))
				->set_attribute('type', 'number')
				->set_attribute('min', 0)
				->set_default_value(0)
				->set_help_text(
					__('Kosongkan jika tidak ada maksimum cashback.', 'sejoli')
				)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				)),

			Field::make('checkbox', 'cashback_refundable', __('Cashback bisa dicairkan?', 'sejoli'))
				->set_help_text(
					__('Cashback bisa dibelanjakan kembali, namun jika cashback bisa dicair maka silahkan aktifkan fitur ini', 'sejoli')
				)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				))
		);

		array_splice($fields, 2, 0, $extra_fields);

		return $fields;
	}

	/**
	 * Add cashback setting in each product setup in user group fields
	 * Hooked via filter sejoli/user-group/per-product/fields, priority 11
	 * @since 	1.0.0
	 * @param 	array $fields
	 * @return 	array
	 */
	public function set_user_group_per_product_fields($fields) {

		$extra_fields = array(
			Field::make('separator', 'sep_cashback', __('Pengaturan Cashback', 'sejoli'))
				->set_classes('sejoli-with-help'),

			Field::make('checkbox',	'cashback_activate', __('Aktifkan cashback', 'sejoli')),

			Field::make('text', 'cashback_value',   __('Nilai cashback', 'sejoli'))
				->set_attribute('type', 'number')
				->set_attribute('min', 0)
				->set_default_value(0)
				->set_width(50)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				)),

			Field::make('select', 'cashback_type', __('Tipe cashback', 'sejoli'))
				->set_options(array(
					'fixed'      => __('Tetap', 'sejoli'),
					'percentage' => __('Persentase', 'sejoli')
				))
				->set_width(50)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				)),

			Field::make('text', 'cashback_max', __('Maksimal cashback (Rp). '))
				->set_attribute('type', 'number')
				->set_attribute('min', 0)
				->set_default_value(0)
				->set_help_text(
					__('Kosongkan jika tidak ada maksimum cashback.', 'sejoli')
				)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				)),

			Field::make('checkbox', 'cashback_refundable', __('Cashback bisa dicairkan?', 'sejoli'))
				->set_help_text(
					__('Cashback bisa dibelanjakan kembali, namun jika cashback bisa dicair maka silahkan aktifkan fitur ini', 'sejoli')
				)
				->set_conditional_logic(array(
					array(
						'field'	=> 'cashback_activate',
						'value'	=> true
					)
				))
		);

		array_splice($fields, 1, 0, $extra_fields);

		return $fields;
	}

	/**
	 * Set cashback data into product meta
	 * Hooked via filter sejoli/product/meta-data, priority 122
	 * @since 	1.0.0
	 * @param 	WP_Post $product
	 * @return 	WP_Post
	 */
	public function set_product_cashback(\WP_Post $product) {

	}
}
