<?php

namespace Sejoli_Wallet\Front;


class Checkout {

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
     * Display customer available wallet
     * Hooked via sejoli/checout-template/after-product, priority 11
     * @param  WP_Post $product
     * @return void
     */
    public function display_wallet(\WP_Post $product) {

        if(false === $product->cashback['activate'] || 0.0 === floatval($product->cashback['amount'])) :
            return;
        endif;

        if('digital' === $product->type) :
            ?>
            <tr>
                <th>
                    <?php if('digital' === $product->type || false === $product->enable_quantity) : ?>
                    <p><?php _e('Potensi cashback yang anda dapatkan', 'sejoli'); ?></p>
                    <?php else : ?>
                    <p><?php _e('Potensi cashback yang anda dapatkan per satu item', 'sejoli'); ?></p>
                    <?php endif; ?>
                </th>
                <th>
                    <?php
                        echo sejolisa_price_format($product->cashback['amount']);
                    ?>
                </th>
            </tr><?php
        else :
            ?>
            <tr>
                <td colspan='2'>
                    <?php if(false === $product->enable_quantity) : ?>
					<p><?php _e('Potensi cashback yang anda dapatkan', 'sejoli'); ?></p>
                    <?php else : ?>
                    <p><?php _e('Potensi cashback yang anda dapatkan per satu item', 'sejoli'); ?></p>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                        echo sejolisa_price_format($product->cashback['amount']);
                    ?>
                </td>
            </tr>
            <?php
        endif;
    }
}
