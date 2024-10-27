=== Aggregate Rating Schema Generator for Blogs ===
Contributors: 7yborg
Donate link: https://www.buymeacoffee.com/7yborg
Tags: star ratings, user ratings, aggregate rating, schema markup, SEO schema markup
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 1.9.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost your blog with user reviews and ratings. Use Schema markup for aggregate ratings to improve SEO and engagement.

== Description ==

**Aggregate Rating Schema Generator for Blogs** enhances your WordPress blog posts and pages by allowing users to submit star ratings. It automatically generates Schema.org markup for aggregate ratings, improving your site's SEO and increasing user engagement.

**Key Features:**

- **User Star Ratings:** Allow visitors to rate your posts and pages with a 5-star rating system.
- **Aggregate Ratings Schema Markup:** Automatically adds Schema.org `AggregateRating` markup to your content, helping search engines display rich snippets.
- **Customizable Display:** Choose where to display the ratings (posts, pages, or both).
- **AJAX-Powered:** Ratings are submitted asynchronously without reloading the page.
- **Caching for Performance:** Implements caching to reduce database queries and improve performance.
- **Confetti Animation:** Delight users with a confetti animation upon successful rating submission.

**Benefits:**

- **Improve SEO:** Enhanced search results with star ratings can increase click-through rates.
- **Increase Engagement:** Encourage user interaction by allowing visitors to rate your content.
- **Easy to Use:** Simple setup with minimal configuration required.

== Installation ==

1. **Upload Plugin Files:**
   - Upload the plugin files to the `/wp-content/plugins/aggregate-rating-schema-generator-for-blogs/` directory, or install the plugin through the WordPress plugins screen directly.

2. **Activate the Plugin:**
   - Activate the plugin through the 'Plugins' screen in WordPress.

3. **Configure Settings (Optional):**
   - Navigate to **Settings > Aggregate Rating Schema Generator** to configure where the ratings should appear (posts, pages, or both).

== Frequently Asked Questions ==

= How do I display the star ratings on my posts or pages? =

The plugin automatically adds the star ratings to your posts and/or pages based on your settings. By default, it appears on all posts. You can change this in the plugin settings.

= Can I customize the look of the star ratings? =

Yes, you can customize the appearance using custom CSS. The plugin includes CSS classes that you can target to style the ratings to match your theme.

= Does the plugin support translation? =

Yes, the plugin is ready for translation. You can translate it into your language using standard WordPress translation methods.

= How does the plugin prevent duplicate ratings from the same user? =

The plugin uses the user's IP address to prevent multiple ratings from the same user on the same post.

= Is the plugin GDPR compliant? =

While the plugin collects the user's IP address to prevent duplicate ratings, it does not store any personal identifiable information beyond that. Please inform your users as per GDPR requirements if necessary.

== Screenshots ==

1. **Star Rating Display:** Front-end view of the star rating system on a blog post.
2. **Aggregate Rating Schema Markup:** Example of how the Schema markup appears in the page source.
3. **Settings Page:** Plugin settings where you can choose where to display the ratings.

== Changelog ==

= 1.9.3 =
* Improved security and code quality.
* Updated function prefixes to meet WordPress guidelines.
* Implemented caching for better performance.
* Added validation and sanitization for user inputs.
* Prepared plugin for translation (internationalization).

= 1.9.1 =
* Initial release.

== Upgrade Notice ==

= 1.9.3 =
We recommend updating to this version to benefit from improved security, performance enhancements, and compliance with WordPress plugin guidelines.

== License ==

This plugin is released under the GPLv2 (or later) license. See [License URI](http://www.gnu.org/licenses/gpl-2.0.html) for details.

== Credits ==

Developed by [Najmus Sayadat](https://infoverse.org.in).

== Additional Notes ==

For support or inquiries, please visit the [support forum](https://wordpress.org/support/plugin/aggregate-rating-schema-generator-for-blogs/) or contact the plugin author.

