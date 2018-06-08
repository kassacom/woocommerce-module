<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require plugin_dir_path( __FILE__ ) . 'src/KassaComOptions.php';

foreach (KassaComOptions::getAvailableOptions() as $kassaComOption) {
    delete_option($kassaComOption);
}
