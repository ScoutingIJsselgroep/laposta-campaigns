<?php
if (!defined('ABSPATH')) {
    exit;
}

class Laposta_Campaigns_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'maybe_show_missing_key_notice'));
    }

    public function add_settings_page() {
        add_options_page(
            __('Laposta Campaigns', 'laposta-campaigns'),
            __('Laposta Campaigns', 'laposta-campaigns'),
            'manage_options',
            'laposta-campaigns',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('laposta_campaigns', 'laposta_api_key', array(
            'type' => 'string',
            'sanitize_callback' => array($this, 'sanitize_api_key'),
            'default' => '',
        ));

        add_settings_section(
            'laposta_campaigns_section',
            __('API instellingen', 'laposta-campaigns'),
            function () {
                echo '<p>' . esc_html__('Voer je Laposta API key in om campagnes op te halen.', 'laposta-campaigns') . '</p>';
            },
            'laposta-campaigns'
        );

        add_settings_field(
            'laposta_api_key',
            __('API key', 'laposta-campaigns'),
            array($this, 'render_api_key_field'),
            'laposta-campaigns',
            'laposta_campaigns_section'
        );
    }

    public function sanitize_api_key($key) {
        $key = is_string($key) ? trim($key) : '';
        // Clear cache when API key changes
        delete_transient('laposta_campaigns_all_v2');
        return $key;
    }

    public function render_api_key_field() {
        $key = get_option('laposta_api_key');
        echo '<input type="text" id="laposta_api_key" name="laposta_api_key" value="' . esc_attr((string) $key) . '" class="regular-text" autocomplete="off" />';
        echo '<p class="description">' . wp_kses_post(sprintf(__('Je vindt de key in je Laposta account. Zie de <a href="%s" target="_blank" rel="noopener">documentatie</a>.', 'laposta-campaigns'), esc_url('https://api.laposta.nl/doc/index.nl.php'))) . '</p>';
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Laposta Campaigns', 'laposta-campaigns') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('laposta_campaigns');
        do_settings_sections('laposta-campaigns');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function maybe_show_missing_key_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $screen = get_current_screen();
        if ($screen && strpos((string) $screen->id, 'laposta-campaigns') !== false) {
            return;
        }
        $key = get_option('laposta_api_key');
        if (!$key) {
            echo '<div class="notice notice-warning"><p>' . wp_kses_post(sprintf(__('Laposta API key ontbreekt. Stel deze in bij <a href="%s">Instellingen → Laposta Campaigns</a>.', 'laposta-campaigns'), esc_url(admin_url('options-general.php?page=laposta-campaigns')))) . '</p></div>';
        }
    }
}


