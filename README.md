# Laposta Campaigns (WordPress plugin)

Display your Laposta email campaigns on any WordPress site using a simple shortcode, with filtering and optional grouping by year.

- Shortcode: `[laposta_campaigns]`
- Settings: configure your Laposta API key under **Settings → Laposta Campaigns**
- Uses Laposta API v2 (`GET /campaign`) with Basic Auth
- Translation-ready (English default, i18n via `load_plugin_textdomain`)

## Features

- Display latest campaigns with optional screenshot
- Limit number or show all
- Include/exclude by words in campaign name or subject
- Group by year or flat list
- Year-only filter
- Simple caching (default 5 minutes)

## Shortcode usage

Basic:

```txt
[laposta_campaigns]
```

Options:

- `number`: number of campaigns to show; use `all` for no limit. Default: `12`.
- `show_screenshot`: `yes` or `no`. Default: `no`.
- `include_name`: comma-separated terms to include by campaign name.
- `exclude_name`: comma-separated terms to exclude by campaign name.
- `include`: comma-separated terms to include by campaign subject.
- `exclude`: comma-separated terms to exclude by campaign subject.
- `group_by`: `none` or `year`. Default: `none`.
- `year`: numeric year to filter.

Examples:

```txt
[laposta_campaigns number="all" show_screenshot="no" group_by="year"]
[laposta_campaigns include_name="nieuwsbrief" exclude="update"]
[laposta_campaigns number="24" year="2024" show_screenshot="yes"]
```

## Install

- Upload the `laposta-campaigns` folder to `wp-content/plugins/`
- Activate the plugin
- Go to Settings → Laposta Campaigns, add your API key

## Filters

### `laposta_campaigns_cache_ttl`

Change the cache duration (default: 300 seconds / 5 minutes). You can also clear the cache manually from the settings page.

```php
add_filter('laposta_campaigns_cache_ttl', function () { return 60; }); // 1 minute
```

### `laposta_campaigns_published_statuses`

Customize which campaign statuses are considered "published". Default: `sent`, `published`, `live`, `public`, `verzonden`. Campaigns with delivery timestamps or a web version URL are always shown regardless of this filter.

```php
add_filter('laposta_campaigns_published_statuses', function ($statuses) {
    $statuses[] = 'completed';
    return $statuses;
});
```

### `laposta_campaigns_html`

Modify the final HTML output of the shortcode. Receives the HTML string, the array of campaign data, and the shortcode attributes.

```php
add_filter('laposta_campaigns_html', function ($html, $campaigns, $atts) {
    return '<div class="my-wrapper">' . $html . '</div>';
}, 10, 3);
```

## Notes

- API documentation: `https://api.laposta.nl/doc/`
- Endpoint used: `GET https://api.laposta.nl/v2/campaign`
- Requires PHP 7.4+, WordPress 5.8+

## License

GPL-2.0-or-later

## Changelog

- 1.1.0: Polish for broader use — English default strings, i18n support, admin cache control, accessibility, uninstall cleanup
- 1.0.3: Use 'web' field for webversion URL; choose best screenshot size from object
- 1.0.2: Publish detection via delivery_started/delivery_ended; add release zip workflow
- 1.0.1: Only show published/sent, list uses UL/LI with title link to web version
- 1.0.0: Initial release
