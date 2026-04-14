<?php
if (!defined('ABSPATH')) {
    exit;
}

class Laposta_Campaigns_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'maybe_show_missing_key_notice'));
        add_action('wp_ajax_laposta_clear_cache', array($this, 'ajax_clear_cache'));
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
            __('API Settings', 'laposta-campaigns'),
            function () {
                echo '<p>' . esc_html__('Enter your Laposta API key to fetch campaigns.', 'laposta-campaigns') . '</p>';
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

        add_settings_field(
            'laposta_cache_control',
            __('Cache', 'laposta-campaigns'),
            array($this, 'render_cache_field'),
            'laposta-campaigns',
            'laposta_campaigns_section'
        );
    }

    public function sanitize_api_key($key) {
        $key = is_string($key) ? trim($key) : '';
        delete_transient('laposta_campaigns_all_v2');
        return $key;
    }

    public function render_api_key_field() {
        $key = get_option('laposta_api_key');
        echo '<input type="password" id="laposta_api_key" name="laposta_api_key" value="' . esc_attr((string) $key) . '" class="regular-text" autocomplete="off" />';
        echo '<p class="description">' . wp_kses_post(sprintf(__('Find your API key in your Laposta account. See the <a href="%s" target="_blank" rel="noopener">documentation</a>.', 'laposta-campaigns'), esc_url('https://api.laposta.nl/doc/'))) . '</p>';
    }

    public function render_cache_field() {
        $cached = get_transient('laposta_campaigns_all_v2');
        if ($cached !== false) {
            $count = is_array($cached) ? count($cached) : 0;
            echo '<p class="description">' . esc_html(sprintf(
                /* translators: %d: number of cached campaigns */
                __('%d campaigns cached.', 'laposta-campaigns'),
                $count
            )) . '</p>';
        } else {
            echo '<p class="description">' . esc_html__('Cache is empty.', 'laposta-campaigns') . '</p>';
        }
        echo '<button type="button" class="button" id="laposta-clear-cache">' . esc_html__('Clear cache', 'laposta-campaigns') . '</button>';
        echo '<span id="laposta-cache-status" style="margin-left:.5em;"></span>';
        ?>
        <script>
        document.getElementById('laposta-clear-cache').addEventListener('click', function() {
            var btn = this;
            var status = document.getElementById('laposta-cache-status');
            btn.disabled = true;
            status.textContent = '<?php echo esc_js(__('Clearing...', 'laposta-campaigns')); ?>';
            fetch(ajaxurl + '?action=laposta_clear_cache&_wpnonce=<?php echo esc_js(wp_create_nonce('laposta_clear_cache')); ?>', {method:'POST'})
                .then(function(r){ return r.json(); })
                .then(function(d){
                    status.textContent = d.success ? '<?php echo esc_js(__('Cache cleared.', 'laposta-campaigns')); ?>' : '<?php echo esc_js(__('Error clearing cache.', 'laposta-campaigns')); ?>';
                    btn.disabled = false;
                })
                .catch(function(){
                    status.textContent = '<?php echo esc_js(__('Error clearing cache.', 'laposta-campaigns')); ?>';
                    btn.disabled = false;
                });
        });
        </script>
        <?php
    }

    public function ajax_clear_cache() {
        check_ajax_referer('laposta_clear_cache');
        if (!current_user_can('manage_options')) {
            wp_send_json_error();
        }
        delete_transient('laposta_campaigns_all_v2');
        wp_send_json_success();
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
            echo '<div class="notice notice-warning"><p>' . wp_kses_post(sprintf(__('Laposta API key is missing. Configure it under <a href="%s">Settings &rarr; Laposta Campaigns</a>.', 'laposta-campaigns'), esc_url(admin_url('options-general.php?page=laposta-campaigns')))) . '</p></div>';
        }
    }
}
