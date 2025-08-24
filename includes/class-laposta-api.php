<?php
if (!defined('ABSPATH')) {
    exit;
}

class Laposta_Api_Client {
    private $api_base = 'https://api.laposta.nl/v2';

    public function get_api_key() {
        $api_key = get_option('laposta_api_key');
        if (is_array($api_key)) {
            $api_key = isset($api_key['api_key']) ? $api_key['api_key'] : '';
        }
        return is_string($api_key) ? trim($api_key) : '';
    }

    public function get_campaigns() {
        $cached = get_transient('laposta_campaigns_all_v2');
        if ($cached !== false) {
            return $cached;
        }

        $api_key = $this->get_api_key();
        if (!$api_key) {
            return new WP_Error('laposta_missing_api_key', __('Laposta API key not configured.', 'laposta-campaigns'));
        }

        $url = trailingslashit($this->api_base) . 'campaign';
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($api_key . ':'),
                'Accept'        => 'application/json',
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($status < 200 || $status >= 300) {
            return new WP_Error('laposta_http_error', sprintf(__('Laposta API error (HTTP %d)', 'laposta-campaigns'), $status), array('body' => $body));
        }

        $json = json_decode($body, true);
        if (!is_array($json)) {
            return new WP_Error('laposta_parse_error', __('Failed to parse Laposta API response.', 'laposta-campaigns'));
        }

        $raw_items = array();
        if (isset($json['data']) && is_array($json['data'])) {
            $raw_items = $json['data'];
        } elseif (isset($json['campaign'])) {
            $raw_items = array($json);
        }

        $campaigns = array();
        foreach ($raw_items as $item) {
            $campaign = isset($item['campaign']) ? $item['campaign'] : $item;
            if (!is_array($campaign)) {
                continue;
            }
            $campaigns[] = $this->normalize_campaign($campaign);
        }

        // Cache for 5 minutes by default
        $ttl = apply_filters('laposta_campaigns_cache_ttl', 5 * 60);
        set_transient('laposta_campaigns_all_v2', $campaigns, $ttl);

        return $campaigns;
    }

    private function normalize_campaign($c) {
        $id = isset($c['campaign_id']) ? $c['campaign_id'] : (isset($c['id']) ? $c['id'] : '');
        $name = isset($c['name']) ? $c['name'] : (isset($c['title']) ? $c['title'] : '');
        $subject = isset($c['subject']) ? $c['subject'] : '';
        $status = isset($c['state']) ? $c['state'] : (isset($c['status']) ? $c['status'] : '');
        $webversion_url = isset($c['webversion']) ? $c['webversion'] : (isset($c['webversion_url']) ? $c['webversion_url'] : '');
        $screenshot_url = isset($c['screenshot']) ? $c['screenshot'] : (isset($c['screenshot_url']) ? $c['screenshot_url'] : '');

        $date_raw = null;
        foreach (array('sent_date', 'sent_on', 'date', 'scheduled_for', 'created') as $key) {
            if (!empty($c[$key])) {
                $date_raw = $c[$key];
                break;
            }
        }
        $timestamp = $date_raw ? strtotime($date_raw) : null;

        return array(
            'id' => $id,
            'name' => $name,
            'subject' => $subject,
            'status' => $status,
            'webversion_url' => $webversion_url,
            'screenshot_url' => $screenshot_url,
            'date_raw' => $date_raw,
            'timestamp' => $timestamp,
            'year' => $timestamp ? (int) date('Y', $timestamp) : null,
        );
    }
}


