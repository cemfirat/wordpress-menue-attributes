<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;
global $wpdb;
$meta_keys = ['_menu_item_custom_id','_menu_item_custom_class','_menu_item_custom_attr'];
foreach ($meta_keys as $key) {
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", $key));
}
