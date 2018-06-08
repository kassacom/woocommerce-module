<?php
/**
 * @package Kassa.com
 * @version 1.0.0
 *
 * @wordpress-plugin
 *
 * Plugin Name: Kassa.com для WooCommerce
 * Description: Платежный модуль для работы с Kassa.com через плагин WooCommerce
 * Author: Kassa.com Developers
 * Version: 1.0.0
 * Author URI: https://kassa.com
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	require plugin_dir_path( __FILE__ ) . 'src/KassaComPlugin.php';
	$plugin = new KassaComPlugin();
}
