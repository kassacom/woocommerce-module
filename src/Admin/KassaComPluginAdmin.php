<?php

namespace KassaCom\WCPlugin\Admin;


use KassaCom\WCPlugin\KassaComOptions;

class KassaComPluginAdmin {

	public function addStyles() {
		wp_enqueue_style(
			'kassa-com',
			\KassaComPlugin::$pluginPath . '/assets/css/kassa-com.css'
		);
	}

	public function addMenu() {
		add_submenu_page(
			'woocommerce',
			'Настройки Kassa.com',
			'Настройки Kassa.com',
			'manage_options',
			'kassa_com_settings_menu',
			[ $this, 'showAdminPage' ]
		);
	}

	public function registerSettings() {
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_PROJECT_ID );
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_LOGIN );
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_API_TOKEN );
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_NOTIFICATION_TOKEN );
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_WALLET_ID );
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_PAYMENT_SUCCESS_PAGE );
		register_setting( 'woocommerce-kassacom-api', KassaComOptions::OPTION_PAYMENT_FAIL_PAGE );
	}

	public function showAdminPage() {
		$activeTab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'kassa-com-settings';

		if ( $activeTab == 'kassa-com-settings' ) {
			$this->showSettings();
		} else {
			$this->showTransactions();
		}
	}

	protected function showSettings() {
		$this->displayPage( 'admin/admin-page-settings.php', [
			'wcPages' => get_pages(),
		] );
	}

	protected function showTransactions() {
		$table = new KassaComTransactionsTable();
		$table->prepare_items();

		$this->displayPage( 'admin/admin-page-transactions.php', [
			'table' => $table,
		] );
	}

	/**
	 * @param string $path
	 * @param array  $params
	 */
	protected function displayPage( $path, $params = [] ) {
		extract( $params );
		include plugin_dir_path( dirname( __FILE__ ) ) . 'Views/' . $path;
	}
}
