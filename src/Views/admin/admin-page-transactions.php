<h2 class="nav-tab-wrapper">
    <a class="nav-tab"
       href="?page=kassa_com_settings_menu&tab=kassa-com-settings">
		<?= __( 'Настройки модуля Kassa.com для WooCommerce', 'kassa-com-checkout' ) ?>
    </a>
    <a class="nav-tab nav-tab-active"
       href="?page=kassa_com_settings_menu&tab=kassa-com-transactions">
		<?= __( 'Список платежей через модуль', 'kassa-com-checkout' ) ?>
    </a>
</h2>

<div class="wrap">
    <h2><?= __( 'Список платежей через модуль Kassa.com', 'kassa-com-checkout' ) ?></h2>
    <p>
		<?= __( 'Для работы с модулем необходимо подключить магазин к <a target="_blank" href="http://kassa.com/">Kassa.com</a>', 'kassa-com-checkout' ) ?>
    </p>
    <?= $table->display() ?>
</div>
