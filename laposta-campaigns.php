<?php
/**
 * Plugin Name:       Laposta Campaigns
 * Plugin URI:        https://github.com/TristanDeBoworker/laposta-campaigns
 * Description:       Display Laposta email campaigns on your site via shortcode [laposta_campaigns] with filtering and grouping.
 * Version:           1.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Tristan de Boer
 * Author URI:        https://github.com/TristanDeBoworker
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       laposta-campaigns
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LAPOSTA_CAMPAIGNS_VERSION', '1.1.0');
define('LAPOSTA_CAMPAIGNS_FILE', __FILE__);
define('LAPOSTA_CAMPAIGNS_PATH', plugin_dir_path(__FILE__));
define('LAPOSTA_CAMPAIGNS_URL', plugin_dir_url(__FILE__));

require_once LAPOSTA_CAMPAIGNS_PATH . 'includes/class-laposta-api.php';
require_once LAPOSTA_CAMPAIGNS_PATH . 'includes/class-laposta-campaigns.php';
require_once LAPOSTA_CAMPAIGNS_PATH . 'includes/admin/class-laposta-admin.php';

function laposta_campaigns_bootstrap() {
    load_plugin_textdomain('laposta-campaigns', false, dirname(plugin_basename(__FILE__)) . '/languages');

    $admin = new Laposta_Campaigns_Admin();
    $admin->init();

    $campaigns = new Laposta_Campaigns_Plugin();
    $campaigns->init();
}

add_action('plugins_loaded', 'laposta_campaigns_bootstrap');


