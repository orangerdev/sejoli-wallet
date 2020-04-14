<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Fired during plugin activation
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Sejoli_Wallet
 * @subpackage Sejoli_Wallet/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sejoli_Wallet
 * @subpackage Sejoli_Wallet/includes
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Sejoli_Wallet_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		$table = $wpdb->prefix . 'sejolisa_wallet';

		if(!Capsule::schema()->hasTable( $table )):
            Capsule::schema()->create( $table, function($table){
                $table->increments  ('ID');
                $table->datetime    ('created_at');
                $table->integer     ('order_id');
				$table->string      ('order_status');
                $table->integer     ('product_id');
                $table->integer     ('user_id');
                $table->integer     ('value');
                $table->enum        ('type', array('in', 'out'));
				$table->string 		('label');
				$table->boolean     ('refundable');
				$table->boolean		('valid_point');
                $table->text        ('meta_data')->nullable();
            });
        endif;

	}

}
