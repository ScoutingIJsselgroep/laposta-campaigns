<?php
if (!defined('ABSPATH')) {
    exit;
}

class Laposta_Campaigns_Plugin {
    public function init() {
        add_shortcode('laposta_campaigns', array($this, 'render_shortcode'));
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'number' => '12',
            'show_screenshot' => 'no',
            'include_name' => '',
            'exclude_name' => '',
            'include' => '',
            'exclude' => '',
            'group_by' => 'none', // none|year
            'year' => '',          // filter specific year
        ), $atts, 'laposta_campaigns');

        $client = new Laposta_Api_Client();
        $data = $client->get_campaigns();

        if (is_wp_error($data)) {
            if (current_user_can('manage_options')) {
                return '<div class="laposta-campaigns-error">' . esc_html($data->get_error_message()) . '</div>';
            }
            return '';
        }

        $campaigns = is_array($data) ? $data : array();

        // Filter helpers
        $include_name_terms = $this->parse_terms($atts['include_name']);
        $exclude_name_terms = $this->parse_terms($atts['exclude_name']);
        $include_subject_terms = $this->parse_terms($atts['include']);
        $exclude_subject_terms = $this->parse_terms($atts['exclude']);
        $year_filter = trim((string) $atts['year']) !== '' ? (int) $atts['year'] : null;

        $campaigns = array_values(array_filter($campaigns, function ($c) use ($include_name_terms, $exclude_name_terms, $include_subject_terms, $exclude_subject_terms, $year_filter) {
            // Year filter
            if ($year_filter !== null) {
                if (!isset($c['year']) || (int) $c['year'] !== $year_filter) {
                    return false;
                }
            }

            $name = isset($c['name']) ? $c['name'] : '';
            $subject = isset($c['subject']) ? $c['subject'] : '';

            // Include by name
            if (!empty($include_name_terms) && !$this->contains_any($name, $include_name_terms)) {
                return false;
            }
            // Exclude by name
            if (!empty($exclude_name_terms) && $this->contains_any($name, $exclude_name_terms)) {
                return false;
            }

            // Include by subject
            if (!empty($include_subject_terms) && !$this->contains_any($subject, $include_subject_terms)) {
                return false;
            }
            // Exclude by subject
            if (!empty($exclude_subject_terms) && $this->contains_any($subject, $exclude_subject_terms)) {
                return false;
            }

            return true;
        }));

        // Sort by date desc if available
        usort($campaigns, function ($a, $b) {
            $ta = isset($a['timestamp']) && $a['timestamp'] ? (int) $a['timestamp'] : 0;
            $tb = isset($b['timestamp']) && $b['timestamp'] ? (int) $b['timestamp'] : 0;
            return $tb <=> $ta;
        });

        // Number limit
        $limit = null;
        if (strtolower((string) $atts['number']) !== 'all') {
            $limit = max(0, (int) $atts['number']);
        }
        if ($limit !== null) {
            $campaigns = array_slice($campaigns, 0, $limit);
        }

        $show_screenshot = strtolower($atts['show_screenshot']) === 'yes' || strtolower($atts['show_screenshot']) === 'true';

        // Enqueue minimal styles once
        wp_register_style('laposta-campaigns', plugins_url('assets/css/laposta-campaigns.css', LAPOSTA_CAMPAIGNS_FILE), array(), LAPOSTA_CAMPAIGNS_VERSION);
        wp_enqueue_style('laposta-campaigns');

        ob_start();
        echo '<div class="laposta-campaigns">';

        if (strtolower($atts['group_by']) === 'year') {
            $groups = array();
            foreach ($campaigns as $c) {
                $y = isset($c['year']) && $c['year'] ? $c['year'] : __('Unknown', 'laposta-campaigns');
                if (!isset($groups[$y])) {
                    $groups[$y] = array();
                }
                $groups[$y][] = $c;
            }

            foreach ($groups as $year => $items) {
                echo '<div class="laposta-group">';
                echo '<h3 class="laposta-group-title">' . esc_html((string) $year) . '</h3>';
                echo '<ul class="laposta-list">';
                foreach ($items as $c) {
                    $this->render_item($c, $show_screenshot);
                }
                echo '</ul>';
                echo '</div>';
            }
        } else {
            echo '<ul class="laposta-list">';
            foreach ($campaigns as $c) {
                $this->render_item($c, $show_screenshot);
            }
            echo '</ul>';
        }

        echo '</div>';
        $html = ob_get_clean();

        return apply_filters('laposta_campaigns_html', $html, $campaigns, $atts);
    }

    private function render_item($c, $show_screenshot) {
        $title = isset($c['name']) ? $c['name'] : '';
        $subject = isset($c['subject']) ? $c['subject'] : '';
        $url = isset($c['webversion_url']) ? $c['webversion_url'] : '';
        $img = isset($c['screenshot_url']) ? $c['screenshot_url'] : '';
        $time = isset($c['timestamp']) && $c['timestamp'] ? (int) $c['timestamp'] : 0;
        $date_str = $time ? date_i18n(get_option('date_format'), $time) : '';

        echo '<li class="laposta-item">';
        echo '<div class="laposta-item-inner">';
        if ($show_screenshot && $img) {
            $img_html = '<img class="laposta-item-image" src="' . esc_url($img) . '" alt="' . esc_attr($title) . '" loading="lazy" />';
            if ($url) {
                echo '<a class="laposta-item-image-link" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . $img_html . '</a>';
            } else {
                echo $img_html;
            }
        }

        echo '<div class="laposta-item-content">';
        if ($title) {
            if ($url) {
                echo '<h4 class="laposta-item-title"><a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($title) . '</a></h4>';
            } else {
                echo '<h4 class="laposta-item-title">' . esc_html($title) . '</h4>';
            }
        }
        if ($subject) {
            echo '<div class="laposta-item-subject">' . esc_html($subject) . '</div>';
        }
        if ($date_str) {
            echo '<time class="laposta-item-date" datetime="' . esc_attr(date('c', $time)) . '">' . esc_html($date_str) . '</time>';
        }
        echo '</div>';

        echo '</div>';
        echo '</li>';
    }

    private function parse_terms($raw) {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return array();
        }
        $parts = preg_split('/\s*,\s*/', $raw);
        $terms = array();
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') {
                $terms[] = mb_strtolower($p);
            }
        }
        return $terms;
    }

    private function contains_any($haystack, $terms) {
        $h = mb_strtolower((string) $haystack);
        foreach ($terms as $t) {
            if ($t !== '' && mb_stripos($h, $t) !== false) {
                return true;
            }
        }
        return false;
    }
}


