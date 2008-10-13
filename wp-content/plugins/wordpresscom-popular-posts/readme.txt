=== Plugin Name ===
Contributors: frasten
Donate link: http://polpoinodroidi.com/wordpress-plugins/wordpresscom-popular-posts/#donations
Tags: posts, widget, statistics, popular posts
Requires at least: 2.2.0
Tested up to: 2.5.1
Stable tag: 1.3.2

This plugin can show the most popular articles in your sidebar, using data collected by Wordpress.com Stats plugin.

== Description ==


Wordpress.com Popular Posts lists the most popular posts on a wordpress powered weblog.
This list can be used in the sidebar to show an indication of which are the most visited pages.

For further info and changelog visit [plugin homepage](http://polpoinodroidi.com/wordpress-plugins/wordpresscom-popular-posts/).

**Requires [Wordpress.com Stats](http://wordpress.org/extend/plugins/stats/) plugin, at least v1.2**

== Installation ==

Wordpress.com Popular Posts can be installed easily:

1. Install [Wordpress.com Stats](http://wordpress.org/extend/plugins/stats/) plugin.
1. Extract the files in the .zip archive, and upload them (including subfolders) to your /wp-content/plugins/ directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Inside the WordPress admin, go to Design > Widgets, and add the 'Popular Posts' widget where you want, then save the changes.
1. If you want, you can customize some settings in the widget, in that page.

== Frequently Asked Questions ==

= I added the widget, but nothing shows up =

Check whether the Wordpress.com Stats plugin is installed and active.
You must have at least version 1.2 of WP Stats.

= How can I integrate this plugin in my non-widget-ready theme? =
If your theme supports widgets, you can place the widget named 'Popular Posts' where you want.

If it doesn't, put this code inside the file sidebar.php, in your theme files:

`<?php if (function_exists('WPPP_show_popular_posts')) WPPP_show_popular_posts(); ?>`

Optionally you can add some parameters to the function, in this format:

`name=value&name=value etc.`

Possible names are:

* `title` (title of the widget, you can add tags (e.g. `<h3>Popular Posts</h3>`) default: Popular Posts)
* `number` (number of links shown, default: 5)
* `days` (length of the time frame of the stats, default 0, i.e. infinite)
* `show` (can be: both, posts, pages; default both)
* `format` (the format of the links shown, default: `<a href='%post_permalink%' title='%post_title%'>%post_title%</a>`)
* `excerpt_length` (the length of the excerpt, if `%post_excerpt%` is used in the format)
* `title_length` (the length of the title links, default 0, i.e. unlimited)

Example: if you want to show the widget without any title, the 3 most
viewed articles, in the last week, and in this format:
*My Article (123 views)* you will use this:

 `<?php WPPP_show_popular_posts( "title=&number=3&days=7&format=<a href='%post_permalink%' title='%post_title_attribute%'>%post_title% (%post_views% views)</a>" );?>`

You don't have to fill every field, you can insert only the values you
want to change from default values.

You can use these special markers in the `format` value:

* `%post_permalink%` the link to the post
* `%post_title%` the title the post
* `%post_title_attribute%` the title of the post; use this in attributes, e.g. `<a title='%post_title_attribute%'...`
* `%post_views%` number of views
* `%post_excerpt%` the first n characters of the content. Set n with *excerpt_length*.
