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
class User {

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
	 * Set cashback setup in user group detail
	 * Hooked via filter sejoli/user-group/detail, priority 11
	 * @since 	1.0.0
	 * @param 	array  	$group_detail
	 * @param 	integer $group_id
     * @param 	array 	$commissions 	Commission field values
	 * @param 	array 	$per_product	Per product field values
	 * @return 	array
	 */
	public function set_user_group_detail(array $group_detail, $group_id, $commissions, $per_product ) {

		$group_detail['cashback_activate']   = boolval(sejolisa_carbon_get_post_meta($group_id, 'cashback_activate'));
        $group_detail['cashback_value']      = floatval(sejolisa_carbon_get_post_meta($group_id, 'cashback_value'));
        $group_detail['cashback_type']       = sejolisa_carbon_get_post_meta($group_id, 'cashback_type');
        $group_detail['cashback_max']        = floatval(sejolisa_carbon_get_post_meta($group_id, 'cashback_max'));
        $group_detail['cashback_refundable'] = boolval(sejolisa_carbon_get_post_meta($group_id, 'cashback_refundable'));

        // Setup cashback for each product
		if(is_array($per_product) && 0 < count($per_product)) :

			foreach($per_product as $i => $detail) :

				$product_id = absint($detail['product']);

                $group_detail['per_product'][$product_id]['cashback_activate']   = boolval($detail['cashback_activate']);
                $group_detail['per_product'][$product_id]['cashback_value']      = floatval($detail['cashback_value']);
                $group_detail['per_product'][$product_id]['cashback_type']       = $detail['cashback_type'];
                $group_detail['per_product'][$product_id]['cashback_max']        = floatval($detail['cashback_max']);
                $group_detail['per_product'][$product_id]['cashback_refundable'] = boolval($detail['cashback_refundable']);

			endforeach;

		endif;

        return $group_detail;
    }

}
