=== Customizable Post Listings ===
Contributors: coffee2code
Donate link: http://coffee2code.com
Tags: posts, listings, get posts, query, recent posts
Requires at least: 2.3
Tested up to: 2.5
Stable tag: trunk
Version: 3.0.1

Easily find posts matching any number of criteria (date, author, category, etc) and list and display anything about them in the manner you want.

== Description ==

Easily find posts matching any number of criteria (date, author, category, etc) and list and display anything about them in the manner you want.

Display Recent Posts, Recently Commented Posts, Recently Modified Posts, Random Posts, and other post, page, or draft listings using the post information of your choosing in an easily customizable manner.  You can narrow post searches by specifying categories and/or authors, among other things.

For help, see the <a href="#percenttags">Percent-substitution tags</a> section for a listing and explanation of all the percent-substitution format tags or the <a href="#templatefuncs">Template functions</a> section for an explanation of the template functions and their arguments.

== Installation ==

1. Unzip `customizable-post-listings.zip` inside the `/wp-content/plugins/` directory, or copy `customizable-post-listings.php` into `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Go to the new `Options` -> `CPL` (or in WP 2.5: `Settings` -> `CPL`) admin options page.  Optionally customize the settings.  You can read documentation on the template tags, arguments, and percent-substitution tags there as well.
1. In your sidebar.php (or other template file), insert calls to post listings function(s) provided by the plugin.

== Template Tags ==

The plugin provides four optional template tags for use in your theme templates.

= Functions =

* `<?php function c2c_get_recent_posts( $num_posts = 5,
  $format = "<li>%post_date%: %post_URL%</li>",
  $categories = '',
  $orderby = 'date',
  $order = 'DESC',
  $offset = 0,
  $date_format = 'm/d/Y',
  $authors = '',
  $post_type = 'post',
  $post_status = 'publish',
  $include_passworded_posts = false,
  $extra_sql_where_clause = '' ) ?>`
Highly customizable way to retrieve and display posts.

* `<?php function c2c_get_random_posts( $num_posts = 5,
  \$format = "<li>%post_date%: %post_URL%</li>",
  \$categories = '',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'm/d/Y',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false ) ?>`
Get random posts.

* `<?php function c2c_get_recently_commented( \$num_posts = 5, 
  \$format = "<li>%comments_URL%<br />%last_comment_date%<br />%comments_fancy%</li>",
  \$categories = '',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'm/d/Y h:i a',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false ) ?>`
Get recently commented posts.

* `<?php function c2c_get_recently_modified( \$num_posts = 5,
  \$format = "<li>%post_URL%<br />Updated: %post_modified%</li>",
  \$categories = '',
  \$order = 'DESC',
  \$offset = 0,
  \$date_format = 'm/d/Y',
  \$authors = '',
  \$post_type = 'post',
  \$post_status = 'publish',
  \$include_passworded_posts = false ) ?>`

= Arguments =

* `$num_posts`
The number of posts to list.
Default: `5`

* `$format`
String containing <a href="#percenttags">percent-substitution tags</a> and text to be displayed for each post retrieved.

* `$categories`
*Optional.* Space-separated list of category IDs -- leave empty to search for posts regardless of category.
Default value: `''`

* `$orderby`
*Optional.* The post field to be used as the sort field.  Except for a few exceptions, the value provided will assume 
that `post_` will be prepended to it. So a value of `'title'` will sort the posts by the 
`'post_title'` field.
Possible values: any post table field that begins with `post_`. In practice, it'll likely be one of the following: `'ID', 'date', 'modified', 'name', 'title'`.  The exceptions to this are: `'rand()'` (to randomize the order of posts), `'max_comment_date'` (to order by the date of the most recent comment for each post), and `'SQL:$sql_here'` (if you're feeling advanced, you can directly specify the SQL for the ORDER BY clause by prepending SQL: to the $orderby value)
Default value: `'date'`

* `$order`
*Optional.* Indicates whether the posts should be listed in ascending or descending order.
Possible values: `'ASC'` or `'DESC'`
Default value: `'DESC'`

* `$offset`
*Optional.* Number of posts to skip from the beginning of the list of posts found.
Default value: `0`

* `$date_format`
*Optional.* Date format, PHP-style, to use to format any dates.  If set to `''`, then the blog default date format of
`'$default_date'` will be used.  Keep in mind that the template functions provide their own date format.
Possible values: see <a href="http://us2.php.net/date" title="">http://us2.php.net/date</a> for more info on date format strings.
Default value: `'m/d/Y'`

* `$authors`
*Optional.* Space-separated list of author IDs -- leave empty to search for posts regardless of author.
Default value: `''`

* `$post_type`
*Optional.* Space-separated list of post_type values to consider in the search.
Possible values: `post, page, attachment`
Default value: `'post'`

* `$post_status
*Optional.* Space-separated list of post_status values to consider in the search.
Possible values: `publish, draft, private, pending, and/or future`
Default value: `'publish'`

* `$include_passworded_posts`
*Optional.* Should passworded posts be included in the serach?
Possible values: `true` or `false`
Default value: `false`

* `$extra_sql_where_clause`
*Optional.* Additional SQL to be added to the query's WHERE clause, to facilitate refining the post search in
a manner not achievable with existing arguments.
Default value: `''`

== Examples ==

* `<ul>
   Recent Posts
  <?php c2c_get_recent_posts(3); ?>
</ul>`

* `<ul>Recently Commented
   <?php c2c_get_recently_commented(3); ?>
</ul>`

* `<ul>Recently Updated
   <?php c2c_get_recently_modified(3); ?>
</ul>`

* The 3 most recent posts in category "34", with custom format display:
`<ul>
<?php c2c_get_recent_posts(3, "<li>%post_date%: %post_URL%<br />by: %post_author_posts%<br />%post_excerpt_short%</li>", "34"); ?>
</ul>`

* Visit Lorelle's blog for fantastic documentation and examples of this plugin:
http://lorelle.wordpress.com/2007/02/14/customizable-post-listings-wordpress-plugin/

== Screenshots ==

1. A screenshot of the admin options page for the plugin showing the options input fields.
1. A screenshot of the admin options page for the plugin showing the percent-substitution tag documentation.
1. A screenshot of the admin options page for the plugin showing the pseudo-function percent-substitution tag documentation.
1. A screenshot of the admin options page for the plugin showing the template function documentation.
1. A screenshot of the admin options page for the plugin showing the template function argument documentation.
