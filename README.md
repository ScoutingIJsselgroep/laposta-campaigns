# Laposta Campaigns (WordPress plugin)

List Laposta campaigns anywhere via a shortcode, with filtering and optional grouping by year.

- Shortcode: `[laposta_campaigns]`
- Settings: set your Laposta API key under Settings → Laposta Campaigns
- Uses Laposta API v2 `GET /campaign` with Basic Auth

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

- `laposta_campaigns_cache_ttl` (int seconds): adjust caching TTL.

Example:

```php
add_filter('laposta_campaigns_cache_ttl', function () { return 60; });
```

## Notes

- API: `https://api.laposta.nl/doc/index.nl.php`
- Endpoint used: `GET https://api.laposta.nl/v2/campaign`

## License

GPL-2.0-or-later

## Changelog

- 1.0.0: Initial release
