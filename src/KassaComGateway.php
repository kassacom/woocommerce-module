<?php

namespace KassaCom\WCPlugin;

use KassaCom\SDK\Client;
use KassaCom\SDK\Model\Request\Item\OrderRequestItem;
use KassaCom\SDK\Model\Request\Item\SettingsRequestItem;
use KassaCom\SDK\Model\Request\Payment\CreatePaymentRequest;

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

class KassaComGateway extends \WC_Payment_Gateway {

	public $paymentMethod = 'kassa-com';

	public $id = 'kassa-com';

	/**
	 * KassaComSuper constructor.
	 */
	public function __construct() {
		$this->has_fields = false;

		$this->init_form_fields();
		$this->init_settings();

		$this->method_title       = __( 'Kassa.com', 'kassa-com-checkout' );
		$this->method_description = __( 'Kassa.com позволяет принимать оплату по тем каналам, которые удобны для Ваших клиентов и неважно будут ли переводы в рублях, долларах или евро.', 'kassa-com-checkout' );
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$woocommerceNewVersion    = '2.0.0';
		$woocommerceVersion       = defined( WOOCOMMERCE_VERSION ) ? WOOCOMMERCE_VERSION : $woocommerceNewVersion;

		if ( version_compare( $woocommerceVersion, $woocommerceNewVersion, '>=' ) ) {
			add_action(
				'woocommerce_update_options_payment_gateways_' . $this->id,
				[ $this, 'process_admin_options', ]
			);
		} else {
			add_action( 'woocommerce_update_options_payment_gateways', [ $this, 'process_admin_options' ] );
		}

	}

	public function init_form_fields() {
		$this->form_fields = [
			'enabled'     => [
				'title' => __( 'Включить/Выключить', 'kassa-com-checkout' ),
				'type'  => 'checkbox',
				'label' => $this->method_description,
			],
			'title'       => [
				'title'       => __( 'Заголовок', 'kassa-com-checkout' ),
				'type'        => 'text',
				'description' => __( 'Название, которое пользователь видит во время оплаты', 'kassa-com-checkout' ),
				'default'     => __( 'KASSA.COM' ),
			],
			'description' => [
				'title'       => __( 'Описание', 'kassa-com-checkout' ),
				'type'        => 'textarea',
				'description' => __( 'Описание, которое пользователь видит во время оплаты', 'kassa-com-checkout' ),
				'default'     => __( 'Профессиональный сервис онлайн-платежей для бизнеса' ),
			],
		];
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$payment = $this->createPayment( $order );

		if ( is_wp_error( $payment ) ) {
			wc_add_notice( __( 'Платеж не прошел. Попробуйте еще раз', 'kassa-com-checkout' ), 'error' );

			return [ 'result' => 'fail', 'redirect' => $order->get_view_order_url() ];
		}

		$order->update_status( KassaComOptions::WC_STATUS_ON_HOLD, __( 'Awaiting cheque payment', 'woocommerce' ) );
		wc_reduce_stock_levels( $order_id );

		$woocommerce->cart->empty_cart();

		return [
			'result'   => 'success',
			'redirect' => $payment->getPaymentUrl(),
		];
	}

	/**
	 * Init settings for gateways.
	 */
	public function init_settings() {
		parent::init_settings();
		$this->enabled = ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'] ? 'yes' : 'no';
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \KassaCom\SDK\Model\Response\Payment\CreatePaymentResponse|\WP_Error
	 */
	protected function createPayment( \WC_Order $order ) {
		try {
			$client = \KassaComPlugin::getClient();
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$orderTotal = $order->get_total();
		} else {
			$orderTotal = number_format( $order->order_total, 2, '.', '' );
		}

		$orderRequest = new OrderRequestItem();
		$orderRequest
			->setAmount( (float) $orderTotal )
			->setCurrency( $order->get_currency() );

		$projectId  = (int) get_option( KassaComOptions::OPTION_PROJECT_ID );
		$successUrl = get_option( KassaComOptions::OPTION_PAYMENT_SUCCESS_PAGE );
		$successUrl = $successUrl ? $successUrl : $order->get_checkout_order_received_url();
		$successUrl = $this->getPaymentUrl( $successUrl, $order );

		$failUrl = get_option( KassaComOptions::OPTION_PAYMENT_FAIL_PAGE );
		$failUrl = $failUrl ? $failUrl : $order->get_checkout_order_received_url();
		$failUrl = $this->getPaymentUrl( $failUrl, $order );

		$settings = new SettingsRequestItem();
		$settings
			->setProjectId( $projectId )
			->setSuccessUrl( $successUrl )
			->setFailUrl( $failUrl );

		$walletId = get_option( KassaComOptions::OPTION_WALLET_ID );

		if ( $walletId ) {
			$settings->setWalletId( (int)$walletId );
		}

		$paymentRequest = new CreatePaymentRequest();
		$paymentRequest
			->setOrder( $orderRequest )
			->setSettings( $settings )
			->setCustomParameters( [
				'cms_name'       => 'woocommerce',
				'module_version' => \KassaComPlugin::VERSION,
				'sdk_version'    => Client::VERSION,
			] );

		try {
			$payment = $client->createPayment( $paymentRequest );
			$order->set_transaction_id( $payment->getToken() );
			$order->update_status( KassaComOptions::WC_STATUS_PENDING );
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		return $payment;
	}

	protected function getPaymentUrl( $name, \WC_Order $order ) {
		switch ( $name ) {
			case 'wc_success':
				return $order->get_checkout_order_received_url();
				break;
			case 'wc_checkout':
				return $order->get_view_order_url();
				break;
			case 'wc_payment':
				return $order->get_checkout_payment_url();
				break;
			default:
				return get_page_link( $name );
				break;
		}
	}
}
