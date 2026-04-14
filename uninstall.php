<?php
/**
 * Laposta Campaigns uninstall handler.
 *
 * Cleans up plugin options and transients when the plugin is deleted.
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('laposta_api_key');
delete_transient('laposta_campaigns_all_v2');
