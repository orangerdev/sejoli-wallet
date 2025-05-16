<?php

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

        if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) :

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table (
                ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                order_id int(11) NOT NULL,
                product_id int(11) NOT NULL,
                user_id int(11) NOT NULL,
                value int(11) NOT NULL,
                type ENUM('in', 'out') NOT NULL,
                label varchar(255) NOT NULL,
                refundable boolean NOT NULL,
                valid_point boolean NOT NULL,
                meta_data text DEFAULT NULL,
                PRIMARY KEY (ID)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

        endif;

    }

}
