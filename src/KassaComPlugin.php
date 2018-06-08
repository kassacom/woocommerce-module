<?php

use GuzzleHttp\Client as GuzzleClient;
use KassaCom\SDK\Client;
use KassaCom\SDK\Transport\GuzzleApiTransport;
use KassaCom\WCPlugin\Admin\KassaComPluginAdmin;
use KassaCom\WCPlugin\KassaComOptions;
use KassaCom\WCPlugin\KassaComPayment;
use KassaCom\WCPlugin\KassaComTranslations;

class KassaComPlugin {

	const VERSION = '1.0.0';

	static $pluginPath;

	/**
	 * KassaComPlugin constructor.
	 */
	public function __construct() {
		self::$pluginPath = plugin_dir_url( dirname( __FILE__ ) );
		$this->includes();
		$this->setTranslations();
		$this->enableAdmin();
		$this->enablePaymentHooks();
	}

	private function includes() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
	}

	private function setTranslations() {
		$translations = new KassaComTranslations();
		add_action( 'plugins_loaded', [ $translations, 'loadPluginTranslations' ] );
	}

	private function enableAdmin() {
		$adminPlugin = new KassaComPluginAdmin();
		add_action( 'admin_init', [ $adminPlugin, 'registerSettings' ] );
		add_action( 'admin_menu', [ $adminPlugin, 'addMenu' ] );
		add_action( 'admin_enqueue_scripts', [ $adminPlugin, 'addStyles' ] );
	}

	private function enablePaymentHooks() {
		$payment = new KassaComPayment();

		add_action( 'parse_request', [ $payment, 'processCallback' ] );
		add_action( 'woocommerce_payment_gateways', [ $payment, 'addGateways' ] );
	}

	/**
	 * @return Client
	 *
	 * @throws \KassaCom\SDK\Exception\ClientIncorrectAuthTypeException
	 */
	public static function getClient() {
		$guzzleClient = new GuzzleClient();
		$apiClient    = new GuzzleApiTransport( $guzzleClient );
		$client       = new Client( $apiClient );

		$login = get_option( KassaComOptions::OPTION_LOGIN );
		$token = get_option( KassaComOptions::OPTION_API_TOKEN );
		$client->setAuth( $login, $token );

		return $client;
	}
}
