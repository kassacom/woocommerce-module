<?php


namespace KassaCom\WCPlugin;


class KassaComTranslations {
	public function loadPluginTranslations() {
		load_plugin_textdomain(
			'kassa-com-checkout',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/translations/'
		);
	}
}
