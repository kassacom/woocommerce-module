<h2 class="nav-tab-wrapper">
    <a class="nav-tab nav-tab-active"
       href="?page=kassa_com_settings_menu&tab=kassa-com-settings">
		<?= __( 'Настройки модуля Kassa.com для WooCommerce', 'kassa-com-checkout' ) ?>
    </a>
    <a class="nav-tab"
       href="?page=kassa_com_settings_menu&tab=kassa-com-transactions">
		<?= __( 'Список платежей через модуль', 'kassa-com-checkout' ) ?>
    </a>
</h2>

<div class="wrap">
    <h2><?= __( 'Настройки модуля Kassa.com для WooCommerce', 'kassa-com-checkout' ) ?></h2>
    <p>
		<?= __( 'Для работы с модулем необходимо подключить магазин к <a target="_blank" href="http://kassa.com/">Kassa.com</a>', 'kassa-com-checkout' ) ?>
    </p>

    <form id="kassacom-settings" method="post" action="options.php">
		<?php
		wp_nonce_field( 'update-options' );
		settings_fields( 'woocommerce-kassacom-api' );
		do_settings_sections( 'woocommerce-kassacom-api' );
		?>
        <h3>Параметры Kassa.com</h3>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_project_id"><?= __( 'ID Проекта', 'kassa-com-checkout' ) ?>*</label>
                </th>
                <td>
                    <input type="text" id="kassa_com_project_id" name="kassa_com_project_id"
                           class="kassa-com-admin-input"
                           value="<?php echo get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PROJECT_ID ); ?>"/>
                    <br/>
                    <span class="help-text"><?= __( 'Скопируйте Project Id из личного кабинета Kassa.com', 'kassa-com-checkout' ) ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_login"><?= __( 'Логин', 'kassa-com-checkout' ) ?>*</label>
                </th>
                <td>
                    <input type="text" id="kassa_com_login" name="kassa_com_login" class="kassa-com-admin-input"
                           value="<?php echo get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_LOGIN ); ?>"/>
                    <br/>
                    <span class="help-text"><?= __( 'Ваш логин в KASSA.COM', 'kassa-com-checkout' ); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_api_token"><?= __( 'API ключ (платежи)', 'kassa-com-checkout' ) ?>*</label>
                </th>
                <td>
                    <input type="text" id="kassa_com_api_token" name="kassa_com_api_token" class="kassa-com-admin-input"
                           value="<?php echo get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_API_TOKEN ); ?>"/>
                    <br/>
                    <span class="help-text"><?= __( 'API ключ с правами "платежи"', 'kassa-com-checkout' ); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_notification_token"><?= __( 'API ключ (нотификации)', 'kassa-com-checkout' ) ?>*
                </th>
                <td>
                    <input type="text" id="kassa_com_notification_token" name="kassa_com_notification_token"
                           class="kassa-com-admin-input"
                           value="<?php echo get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_NOTIFICATION_TOKEN ); ?>"/>
                    <br/>
                    <span class="help-text"><?= __( 'API ключ с правами "нотификации"', 'kassa-com-checkout' ); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_wallet_Id"><?= __( 'Wallet ID', 'kassa-com-checkout' ) ?></>
                </th>
                <td>
                    <input type="text" id="kassa_com_wallet_Id" name="kassa_com_wallet_Id" class="kassa-com-admin-input"
                           value="<?php echo get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_WALLET_ID ); ?>"/>
                    <br/>
                    <span class="help-text"><?= __( 'Идентификатор кошелька. Заполняется, если у вас используется мультикошельковый аккаунт.', 'kassa-com-checkout' ); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_payment_success_page"><?= __( 'Страница успеха платежа', 'kassa-com-checkout' ); ?></label>
                </th>
                <td>
                    <select id="kassa_com_payment_success_page" name="kassa_com_payment_success_page">
                        <option value="wc_success" <?php echo( ( get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PAYMENT_SUCCESS_PAGE ) == 'wc_success' ) ? ' selected' : '' ); ?>>
							<?= __( 'Страница "Заказ принят" от WooCommerce', 'kassa-com-checkout' ); ?>
                        </option>
                        <option value="wc_checkout" <?php echo( ( get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PAYMENT_SUCCESS_PAGE ) == 'wc_checkout' ) ? ' selected' : '' ); ?>>
							<?= __( 'Страница оформления заказа от WooCommerce', 'kassa-com-checkout' ); ?>
                        </option>
						<?php
						if ( $wcPages ) {
							foreach ( $wcPages as $wcPage ) {
								$selected = ( $wcPage->ID == get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PAYMENT_SUCCESS_PAGE ) ) ? ' selected' : '';
								echo '<option value="' . $wcPage->ID . '" ' . $selected . '>' . $wcPage->post_title . '</option>';
							}
						}
						?>
                    </select>
                    <br/>
                    <span class="help-text">
                        <?= __( 'Эту страницу увидит покупатель, когда оплатит заказ', 'kassa-com-checkout' ); ?>
                    </span>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="kassa_com_payment_fail_page"><?= __( 'Страница отказа', 'kassa-com-checkout' ); ?></label>
                </th>
                <td><select id="kassa_com_payment_fail_page" name="kassa_com_payment_fail_page">
                        <option value="wc_checkout" <?php echo( ( get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PAYMENT_FAIL_PAGE ) == 'wc_checkout' ) ? ' selected' : '' ); ?>>
							<?= __( 'Страница оформления заказа от WooCommerce', 'kassa-com-checkout' ); ?>
                        </option>
                        <option value="wc_payment" <?php echo( ( get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PAYMENT_FAIL_PAGE ) == 'wc_payment' ) ? ' selected' : '' ); ?>>
							<?= __( 'Страница оплаты заказа от WooCommerce', 'kassa-com-checkout' ); ?>
                        </option>
						<?php
						if ( $wcPages ) {
							foreach ( $wcPages as $wcPage ) {
								$selected = ( $wcPage->ID == get_option( \KassaCom\WCPlugin\KassaComOptions::OPTION_PAYMENT_FAIL_PAGE ) ) ? ' selected' : '';
								echo '<option value="' . $wcPage->ID . '" ' . $selected . '>' . $wcPage->post_title . '</option>';
							}
						}
						?></select>
                    <br/>
                    <span class="help-text">
                        <?= __( 'Эту страницу увидит покупатель, если что-то пойдет не так: например, если ему не хватит денег на карте', 'kassa-com-checkout' ); ?>
                    </span>
                </td>
            </tr>
        </table>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="kassacom_callback_notification"><?= __( 'Обработчик для нотификаций', 'kassa-com-checkout' ); ?></>
                </th>
                <td>
                    <input type="text" id="kassacom_callback_notification" readonly="readonly"
                           class="kassa-com-admin-input"
                           value="<?php echo site_url( '/?kassacom_callback=notification' ); ?>">
                    <br/>
                    <span class="help-text"><?= __( 'Укажите данный URL в настройках проекта в KASSA.COM (поле обработчик)', 'kassa-com-checkout' ); ?><span>
                </td>
            </tr>
        </table>

		<?php submit_button(); ?>
    </form>
</div>
