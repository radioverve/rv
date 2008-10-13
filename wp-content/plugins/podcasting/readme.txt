=== Podcasting ===
Contributors: cavemonkey50
Tags: podcast, itunes, podcasting, rss, feed, enclosure
Requires at least: 2.6
Tested up to: 2.6
Stable tag: 1.65

Adds full podcasting support to WordPress.

== Description ==

Created for the Google Summer of Code 2007, Podcasting brings complete podcasting support to WordPress. Taking advantage of the latest and greatest in WordPress 2.6, WordPress podcasting has never been so easy.

= Features =

- Full iTunes support (both feed and item tags).
- A dedicated podcasting feed that can stand alone or be applied to any archive, category, or tag page.
- Support for multiple formats (or podcasts) with each format receiving their own dedicated feed.
- Offers a podcast player that can be included in any post.
- A simple, easy to use interface.
- Fully integrates with WordPress' existing enclosure support and takes advantage of WordPress 2.6's new plugin API.

= Usage =

Feed, iTunes, and format options can be configured in WordPress' Options > Podcasting page.

Episodes can be added, edited, and deleted via the Podcasting options box displayed on posts' edit screen. The box appears below the image uploading section and can be rearranged with the other options boxes.

== Installation ==

1. Upload the `podcasting` folder to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Configure your podcasting feed through the 'Settings' > 'Podcasting' menu in WordPress.
1. Begin adding new episodes to posts!

== Frequently Asked Questions ==

= Help, the podcasting feed is resulting in a 404! =

WordPress 2.5 slightly changed the upgrade procedure. Stored rewrite rules (such as Podcasting's) are dumped after an upgrade to prevent issues. From now on, [rewrite rules must be refreshed](http://codex.wordpress.org/Upgrading_WordPress_Extended#Step_10:_Update_Permalinks_and_.htaccess) as part of the upgrade procedure.

Alternatively, if the pretty feed URLs aren't working period, you can also use the following URL structure:

`http://example.com/?feed=podcast`
`http://example.com/?feed=podcast&format=x`

= My m4a/m4b files disappear after saving the page! =

Most web servers do not recognize m4a/m4b as an audio file by default, therefore causing Podcasting to ignore the URL. Thankfully, this can be corrected easily. Open up the .htaccess file in your WordPress installation's root directory and add the following two lines:

`AddType audio/x-m4a .m4a`
`AddType audio/x-m4b .m4b`

== Changelog ==

**1.65** - Bug Fix

* Corrects saved draft issue brought on by WordPress 2.6.

**1.64** - Bug Fix

* Adds missing image showing the audio player's colors.
* Fixes a bug where changing a format's slug would forget the format's explicit setting.

**1.63** - Critical Bug Fix

* Corrects typo preventing 1.62's fix from working.

**1.62** - Critical Bug Fix

* Resolves an issue where an episode would not be saved once navigating away from the page.

**1.61** - Bug Fix

* Resolves an issue where certain URL characters such as spaces would cause a failure creating an enclosure.
* Resolves validation issues with the RSS feed.

**1.6** - Minor Update

* Adds options to configure the audio player's colors.

**1.52** - Minor Update

* The player is no longer replaced with the text "Download Podcast" in feeds to prevent that text from showing up in iTunes descriptions when the player is inserted first in a post.

**1.51** - Bug Fix

* Fixes the Send to Editor button when the visual editor is disabled.

**1.5** - Major Update

* Fixes compatibility issues with WordPress 2.5.
* Updates to the user interface to reflect the changes in 2.5.
* Episode addition interface is now fully AJAX. Add and delete episodes without having to refresh the page.
* Converts [podcast] tag to new shortcode API.
* Fixes Send to Editor button not working on the visual editor.
* **Note**: Version 1.5 requires WordPress 2.5.

**1.02** - Critical Bug Fix

* Fixes a critical Javascript error affecting Internet Explorer and possibly other browsers.
* It is recommended to install this update as soon as possible.

**1.01** - Bug Fix

* Fixes a conflict with the Feedburner Feedsmith plugin.
Resolves AJAX errors when managing formats.

**1.0** - Initial Release