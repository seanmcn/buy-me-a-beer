<?php
/**
 *
 * Buy Me A Beer plugin is a plugin to add a 'buy me a beer' donation button to your blog
 *
 * @package bmab
 *
 * @wordpress-plugin
 * Plugin Name:       Buy Me A Beer
 * Plugin URI:        http://github.com/Seanmcn/BuyMeABeer
 * Description:       Plugin to add a 'Buy me a beer' donation button. Easily customisable to be a "Buy Me Whatever" Paypal plugin.
 * Version:           1.0.0
 * Author:            Sean McNamara
 * Author URI:        http://seanmcn.com
 * Text Domain:       BuyMeABeer
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */
// If this file is called directly, then abort execution.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/App.php';
//require_once plugin_dir_path( __FILE__ ) . 'includes/manager.php';
//require_once plugin_dir_path( __FILE__ ) . "includes/config.php";
//require_once plugin_dir_path( __FILE__ ) . "includes/repo/BaseRepository.php";
//
//require_once plugin_dir_path( __FILE__ ) . "includes/model/ItemModel.php";
//require_once plugin_dir_path( __FILE__ ) . "includes/repo/ItemRepository.php";
//
//require_once plugin_dir_path( __FILE__ ) . "includes/model/PaymentModel.php";
//require_once plugin_dir_path( __FILE__ ) . "includes/repo/PaymentRepository.php";
//
//require_once plugin_dir_path( __FILE__ ) . "includes/repo/SettingRepository.php";
//
//
//require_once plugin_dir_path( __FILE__ ) . "includes/model/WidgetModel.php";
//require_once plugin_dir_path( __FILE__ ) . "includes/repo/WidgetRepository.php";
//
//require_once plugin_dir_path( __FILE__ ) . "payment_services/paypal.php";

function runBuyMeABeerPlugin() {

	$bmab = new bmab\BuyMeABeer();
	register_activation_hook( __FILE__, array( $bmab, 'activatePlugin' ) );
	$bmab->run();

}

runBuyMeABeerPlugin();