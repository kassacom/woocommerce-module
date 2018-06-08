<?php


namespace KassaCom\WCPlugin;


use KassaCom\SDK\Model\PaymentStatuses;
use KassaCom\SDK\Notification;

class KassaComPayment {

	public function processCallback() {
		if ( strtolower( $_SERVER['REQUEST_METHOD'] ) != 'post'
		     || ! isset( $_REQUEST['kassacom_callback'] )
		     || strtolower( $_REQUEST['kassacom_callback'] ) != 'notification' ) {
			return;
		}

		$notification = new Notification();
		$notification->setApiKey( get_option( KassaComOptions::OPTION_NOTIFICATION_TOKEN ) );
		$notification->setSkipIpCheck();

		try {
			$notificationRequest = $notification->process();
		} catch ( \Exception $e ) {
			exit();
		}

		$token = $notificationRequest->getToken();
		$order = $this->getOrderByToken( $token );

		if ( ! $order ) {
			$notification->errorResponse( __( 'Заказ не найден', 'kassa-com-checkout' ) );
			exit();
		}

		$transactionId = $order->get_transaction_id();

		switch ( $notificationRequest->getStatus() ) {
			case PaymentStatuses::STATUS_SUCCESSFUL:
				$order->payment_complete( $transactionId );
				break;
			case PaymentStatuses::STATUS_ERROR:
				$order->update_status( KassaComOptions::WC_STATUS_CANCELED );
				break;
			case PaymentStatuses::STATUS_PROCESS:
				$order->update_status( KassaComOptions::WC_STATUS_ON_HOLD );
				break;
			case PaymentStatuses::STATUS_WAIT_CAPTURE:
				if ( $order->get_status() === KassaComOptions::WC_STATUS_COMPLETED ) {
					try {
						$client  = \KassaComPlugin::getClient();
						$payment = $client->capturePayment( $token );
					} catch ( \Exception $e ) {
						$notification->errorResponse( __( 'Ошибка обработки платежа', 'kassa-com-checkout' ) );
						exit();
					}

					if ( $payment->getStatus() == PaymentStatuses::STATUS_SUCCESSFUL ) {
						$order->payment_complete( $transactionId );
						$order->add_order_note( sprintf(
								__( 'Номер транзакции в Kassa.com: %1$s. Сумма: %2$s  %3$s', 'kassa-com-checkout'
								), $transactionId, $payment->getOrder()->getAmount(), $payment->getOrder()->getCurrency() )
						);
					} elseif ( $payment->getStatus() == PaymentStatuses::STATUS_ERROR ) {
						$order->update_status( KassaComOptions::WC_STATUS_CANCELED );
					}
				}
				break;
		}

		exit();
	}

	public function addGateways() {
		return [
			KassaComGateway::class
		];
	}

	private function getOrderByToken( $token ) {
		global $wpdb;

		$query  = "
			SELECT *
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_transaction_id' AND meta_value = %s 
		";
		$sql    = $wpdb->prepare( $query, $token );
		$result = $wpdb->get_row( $sql );

		if ( $result ) {
			$order = new \WC_Order( $result->post_id );

			return $order;
		}

		return null;
	}
}
