=== Laposta Campaigns ===
Contributors: scoutingijsselgroep
Tags: laposta, newsletter, campaigns, shortcode, email
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display Laposta email campaigns on your WordPress site via a shortcode, with filtering and optional grouping by year.

== Description ==

Display your Laposta email campaigns anywhere on your WordPress site using a simple shortcode. Configure your Laposta API key under Settings → Laposta Campaigns and you're ready to go.

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

== Developers ==

The plugin provides several filters to customize its behavior.

= laposta_campaigns_cache_ttl =

Change the cache duration (default: 300 seconds / 5 minutes). You can also clear the cache manually from the settings page.

`add_filter('laposta_campaigns_cache_ttl', function () { return 60; }); // 1 minute`

= laposta_campaigns_published_statuses =

Customize which campaign statuses are considered "published". By default the plugin shows campaigns with status `sent`, `published`, `live`, `public`, or `verzonden`. Campaigns with delivery timestamps or a web version URL are always shown regardless of this filter.

`add_filter('laposta_campaigns_published_statuses', function ($statuses) {
    $statuses[] = 'completed';
    return $statuses;
});`

= laposta_campaigns_html =

Modify the final HTML output of the shortcode. Receives the HTML string, the array of campaign data, and the shortcode attributes.

`add_filter('laposta_campaigns_html', function ($html, $campaigns, $atts) {
    return '<div class="my-wrapper">' . $html . '</div>';
}, 10, 3);`

== Frequently Asked Questions ==

= Where do I get the API key? =
In your Laposta account under the API section. See the documentation: https://api.laposta.nl/doc/

= Can I change the cache duration? =
Yes, use the `laposta_campaigns_cache_ttl` filter (see Developers section), or clear the cache from the settings page.

= Can I control which campaigns are shown? =
Yes. Use the shortcode attributes `include_name`, `exclude_name`, `include`, `exclude`, and `year` to filter campaigns. See the Usage section for details.

= Can I style the output? =
The plugin outputs semantic HTML with CSS classes on every element. You can override styles by targeting `.laposta-campaigns` and its children in your theme CSS. For full control, use the `laposta_campaigns_html` filter.

== Changelog ==

= 1.1.0 =
Polish for broader use: English default strings with i18n support, improved admin settings page with cache control, better accessibility and empty state handling, uninstall cleanup

= 1.0.3 =
Use 'web' field for webversion URL; choose best screenshot size from object

= 1.0.2 =
Publish detection via delivery_started/delivery_ended; add release zip workflow

= 1.0.1 =
Only show published/sent; switch to UL/LI; title links to web version

= 1.0.0 =
Initial release
