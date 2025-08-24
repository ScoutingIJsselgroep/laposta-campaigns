<?php
/**
 * Plugin Name:       Laposta Campaigns
 * Description:       List Laposta campaigns via shortcode [laposta_campaigns] with filters and grouping.
 * Version:           1.0.0
 * Author:            Tristan de Boer
 * Text Domain:       laposta-campaigns
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LAPOSTA_CAMPAIGNS_VERSION', '1.0.0');
define('LAPOSTA_CAMPAIGNS_FILE', __FILE__);
define('LAPOSTA_CAMPAIGNS_PATH', plugin_dir_path(__FILE__));
define('LAPOSTA_CAMPAIGNS_URL', plugin_dir_url(__FILE__));

require_once LAPOSTA_CAMPAIGNS_PATH . 'includes/class-laposta-api.php';
require_once LAPOSTA_CAMPAIGNS_PATH . 'includes/class-laposta-campaigns.php';
require_once LAPOSTA_CAMPAIGNS_PATH . 'includes/admin/class-laposta-admin.php';

function laposta_campaigns_bootstrap() {
    // Admin
    $admin = new Laposta_Campaigns_Admin();
    $admin->init();

    // Front / Shortcode
    $campaigns = new Laposta_Campaigns_Plugin();
    $campaigns->init();
}

add_action('plugins_loaded', 'laposta_campaigns_bootstrap');


