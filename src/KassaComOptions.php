<?php


namespace KassaCom\WCPlugin;


class KassaComOptions {
	const OPTION_PROJECT_ID = 'kassa_com_project_id';
	const OPTION_LOGIN = 'kassa_com_login';
	const OPTION_API_TOKEN = 'kassa_com_api_token';
	const OPTION_NOTIFICATION_TOKEN = 'kassa_com_notification_token';
	const OPTION_WALLET_ID = 'kassa_com_wallet_Id';
	const OPTION_PAYMENT_SUCCESS_PAGE = 'kassa_com_payment_success_page';
	const OPTION_PAYMENT_FAIL_PAGE = 'kassa_com_payment_fail_page';

	const WC_STATUS_PENDING = 'pending';
	const WC_STATUS_CANCELED = 'cancelled';
	const WC_STATUS_ON_HOLD = 'on-hold';
	const WC_STATUS_COMPLETED = 'completed';
	const WC_STATUS_FAILED = 'failed';
	const WC_STATUS_PROCESSING = 'processing';
	const WC_STATUS_REFUNDED = 'refunded';

    public static function getAvailableOptions()
    {
        return [
            self::OPTION_PROJECT_ID,
            self::OPTION_LOGIN,
            self::OPTION_API_TOKEN,
            self::OPTION_NOTIFICATION_TOKEN,
            self::OPTION_WALLET_ID,
            self::OPTION_PAYMENT_SUCCESS_PAGE,
            self::OPTION_PAYMENT_FAIL_PAGE,
        ];
	}
}
