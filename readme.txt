=== Laposta Campaigns ===
Contributors: scoutingijsselgroep
Tags: laposta, newsletter, campaigns, shortcode
Requires at least: 5.8
Tested up to: 6.6
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

List Laposta campaigns anywhere via a shortcode, with filtering and optional grouping by year.

== Description ==

This plugin lists Laposta campaigns via shortcode. Configure your Laposta API key under Settings → Laposta Campaigns.

Shortcode: `[laposta_campaigns]`

Features:

- Display latest campaigns with optional screenshot
- Limit number or show all
- Include/exclude by words in campaign name or subject
- Group by year or flat list
- Year-only filter
- Simple caching (default 5 minutes)

== Installation ==

1. Upload the `laposta-campaigns` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Settings → Laposta Campaigns and enter your API key

== Usage ==

Basic:

`[laposta_campaigns]`

Options:

- `number`: number to show or `all`. Default `12`.
- `show_screenshot`: `yes` or `no`. Default `no`.
- `include_name`: terms to include by campaign name (comma-separated)
- `exclude_name`: terms to exclude by campaign name (comma-separated)
- `include`: terms to include by subject (comma-separated)
- `exclude`: terms to exclude by subject (comma-separated)
- `group_by`: `none` or `year`. Default `none`.
- `year`: year number to filter

Examples:

`[laposta_campaigns number="all" show_screenshot="no" group_by="year"]`
`[laposta_campaigns include_name="nieuwsbrief" exclude="update"]`
`[laposta_campaigns number="24" year="2024" show_screenshot="yes"]`

== Frequently Asked Questions ==

= Where do I get the API key? =
In your Laposta account. See the documentation: https://api.laposta.nl/doc/index.nl.php

= Can I change the cache duration? =
Yes, filter `laposta_campaigns_cache_ttl` (seconds).

== Changelog ==

= 1.0.3 =
Use 'web' field for webversion URL; choose best screenshot size from object

= 1.0.2 =
Publish detection via delivery_started/delivery_ended; add release zip workflow

= 1.0.1 =
Only show published/sent; switch to UL/LI; title links to web version

= 1.0.0 =
Initial release
