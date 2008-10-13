=== Featurific For Wordpress ===
Contributors: rinogo
Donate link: http://featurific.com/
Tags: slideshow, slide, show, gallery, flash, xml, dynamic, conversion, funnel, Post, posts, sidebar, images, links, photo, photos, statistics, stats, swf, plugin, admin
Requires at least: 2.3
Tested up to: 2.5
Stable tag: trunk

An effortless but powerful interface to Featurific Free, the featured story slideshow.  (Similar to the 'featured'
widget on time.com, msn.com, walmart.com, etc.)



== Description ==

An effortless interface to Featurific Free, the featured story slideshow.

Unlike traditional slideshows, Featurific imitates the behavior seen on the home pages of sites like time.com and msn.com,
displaying summaries of featured articles on the site.  The idea is to increase conversion and user satisfaction by
funneling your readers to your strongest, most engaging content.  If you believe that big budget companies like Time, MSN,
and Walmart might be on to something, then give this plugin a shot.

Installation is automatic and easy, while advanced users can customize every element of the Flash slideshow presentation.

Selected feature list:
* No configuration required (although you can tweak nearly any aspect of the plugin if you so desire)
* User-customizable templates
* Integrates with the Wordpress.com Stats Plugin to select most popular posts
* Customizable options include number of posts to display, post selection type, screen duration, auto-excerpt length, etc.



== Installation ==

Standard Wordpress installation (no fancy config steps required):

1. Extract all files from FeaturificForWordpress.zip.
1. Upload the entire `featurific` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You're done! :)

For basic configuration:
* Wordpress 2.3: visit the 'Options' menu and access the submenu named 'Featurific'.
* Wordpress 2.4+: visit the 'Settings' menu and access the submenu named 'Featurific'.



== Demo Sites ==
Once you get Featurific for Wordpress installed, please email me and I'll add you to this list of demo sites.
(Hey, free traffic! :) ) (rich at [please don't spam me] byu period net)

[Sox & Dawgs](http://soxanddawgs.com/) |
[Endorkins](http://endorkins.com/) |
[Passion for Poetry](http://p4poetry.com/) |
[Esperto Seo](http://www.espertoseo.it) |
[Bigg Success](http://biggsuccess.com) |
[Life Magick](http://www.lifemagick.net/) |
[Teknoblog](http://www.teknoblog.com/) |
[West Ham United](http://www.westhampoland.com) |
[The Couch Potato](http://myblogisonfire.com/couchpotato/) |
[Alofoke Music](http://alofokemusic.net/v2) |
[GameAttic.com](http://gameattic.com/) |
[The GPS Times](http://thegpstimes.com)





== Testers and Debuggers ==
Muchas gracias to the following individuals for their help with testing and debugging Featurific for Wordress:

Edward Prislac of [Semper Fried](http://www.semperfried.com/)

Ian Bethune of [Sox & Dawgs](http://soxanddawgs.com/)



== Changelog ==
**1.2.8 (7/3/08)**
Added an auto-upgrade warning to prevent users from overwriting changes/additions made to their templates upon auto-upgrading.

**1.2.7 (6/24/08)**
Fixed a bug that generated invalid input XML when HTML was used in a manual excerpt.

**1.2.6 (6/13/08)**
Moved the image cache table name from a global variable to a function because some systems seemed unable to access the global variable.  (Weird...)

**1.2.5 (6/12/08)**
Fixed some path issues that prevented Featurific for Wordpress from working on Windows machines.

**1.2.4 (6/11/08)**
Fixed an issue in which custom fields were not properly overriding original values.  Also fixed an issue in which the characters '&ldquo; &rdquo; &lsquo; &rsquo;' were not displaying correctly.

**1.2.3 (6/10/08)**
Fixed one instance of "&lt;?" in featurific.php that should have been "&lt;?php".  In systems on which PHP was compiled with short_open_tag set to Off, this would cause the PHP block in question to not be executed.  (More information: http://www.daaq.net/old/php/index.php?page=embedding+php&parent=php+basics)

**1.2.2 (6/9/08)**
featurific\_show\_admin\_message\_once($message) implemented (and one call to it added).  This function is to be called when a minor error occurs that we wish to show to the admin user one time only.

**1.2.1 (6/5/08)**
Added plugin version printout to HTML embedding code for debugging.

**1.2 (6/4/08)**
Changelog added.  Unicode Support (UTF-8 encoding) added to FeaturificFree.swf.



== Frequently Asked Questions ==

= I can't get the plugin to install.  Can you help me? =
I'm sorry if the plugin is causing you problems.  It has only recently been released (consider yourself an early
adopter! :) ), so I still need to work through the bugs that weren't manifest in my development environment.  Please
email me (rich at [please don't spam me] byu period net) and I'll be happy to help you work through the bugs.



= Something is broken, but I don't see any error messages.  How do I find the error messages? =
Wordpress sometimes has a nasty habit of suppressing errors, which is, needless to say, quite unhelpful when trying to
discover why Featurific isn't working.  However, there's a sneaky workaround we can perform to view errors that are
generated upon plugin activation.

Here are the steps:

1. Change the *Featurific* template to one that does not correctly display Featurific.  This causes the data*.xml file to
be regenerated, but errors are unfortunately suppressed.

1. With the nonfunctional template still selected, change your *Wordpress* theme to something other than your current
theme.  This causes the data*.xml file to be regenerated, but this time, errors are reported.

1. Finally, after you have viewed the errors (or verified that no errors are generated), you may of course revert
your Featurific template and Wordpress theme to their original state.



= I can't get the "User-Defined Posts" feature to work.  Any ideas? =
**Use the correct format**
The format for this field is a comma-separated list of post id's, such as '5, 14, 8, 23' (omit the quotes).

**Ensure that the posts actually exist**
When using the "User-Defined Posts" feature, posts won't appear if they are non-existent.  You can check to see
if the posts exist by accessing them in your web browser via the following URL:

http://&lt;location of your blog&gt;/?p=&lt;insert post id here&gt;

So, if your blog were located at mysuperblog.com/blog, and if you wanted to check to see if post 54 existed,
you would access:

http://mysuperblog.com/blog/?p=54

If you can't access the post via this URL, then you're trying to use an invalid post id.


= Where can I find more templates? =
[The Featurific website](http://featurific.com/ffw) ([http://featurific.com/ffw](http://featurific.com/ffw)).  No
extra templates have been released yet - please let me know if you'd like your template to be the first featured
template!


= What are the system requirements for using Featurific? =
Featurific has been successfully tested with Wordpress 2.3 to 2.5.1 on PHP4 and PHP5.  If you have problems on these
(or other) configurations, feel free to email me.  (rich at [please don't spam me] byu period net)  Support on Wordpress
2.3 seems to be limited, I'll post more information as it becomes available.


= How do I move Featurific to another location on my page? =

Many Featurific templates look better in a sidebar than in the main content area.  To move Featurific to the side bar or
any other location, just edit your theme.  Featurific for Wordpress automatically inserts itself into your index.php or
home.php theme file (whichever it detects is present).  Open up the file and look for the following code:

`<?php
//Code automatically inserted by Featurific for Wordpress plugin
if(is_home())                             //If we're generating the home page (remove this line to make Featurific appear on all pages)...
 if(function_exists('insert_featurific')) //If the Featurific plugin is activated...
  insert_featurific();                    //Insert the HTML code to embed Featurific
?>`

Move this code to wherever you'd like Featurific to appear (in any of your theme files).



= How do I include an image in Featurific without also including it in the Wordpress post itself? =
There are two solutions to this requirement:

1. *Sneaky HTML/CSS*: In your Wordpress posts, embed the images like you normally embed images, but add some CSS to hide
them when the post is displayed in Wordpress.  For example, you could add 'style="display: none"' to the <img> tags.
The images won't appear in Wordpress, but Featurific will still detect them and display them (since it does not process
CSS).

1. *Custom Fields*: Add a custom field to your posts with the url of the image in it.  For example, you could use a custom
field of 'image_1' which would cause the image to show up in the default location for images in most galleries.  (image_1
is a tag used within most Featurific templates that is replaced with the first detected image from the Wordpress post.
Likewise, image_2 is the second detected image, image_3 is the third, etc.)  If you want even more control, you could
use a custom field and tag with your own name, such as 'my_image'.  Then, you need to edit the template.xml file for
your template, adding in the 'my_image' tag where you want the image to appear.



= How do I make Featurific appear on pages other than the main page? =

**Theme file**
If you've got a *theme file that corresponds to the pages on which you want Featurific to appear*, just follow the
instructions under the FAQ entry, "How do I move Featurific to another location on my page?".  Move the Featurific code to
the desired location in the appropriate theme file.

**All pages**
If you want Featurific to appear on *all* pages and not just on the home page, find the following code in your theme's
index.php or home.php file:

`<?php
//Code automatically inserted by Featurific for Wordpress plugin
if(is_home())                             //If we're generating the home page (remove this line to make Featurific appear on all pages)...
 if(function_exists('insert_featurific')) //If the Featurific plugin is activated...
  insert_featurific();                    //Insert the HTML code to embed Featurific
?>`

Comment out the line that begins with `if(is_home())` by inserting '//' at the beginning of the line as follows:

`<?php
//Code automatically inserted by Featurific for Wordpress plugin
 //if(is_home())                             //If we're generating the home page (remove this line to make Featurific appear on all pages)...
 if(function_exists('insert_featurific')) //If the Featurific plugin is activated...
  insert_featurific();                    //Insert the HTML code to embed Featurific
?>`

**Specific pages**
If you want Featurific to appear on specific pages, you could try using `$_SERVER['REQUEST_URI'])` and either an if
statement or the `preg()`/`pregi()` functions (regular expressions).


= How do I use Featurific on a static page? / I activated Featurific, but nothing happened - what's up? =
Featurific can only insert itself into your template if you are using the traditional format of a Wordpress blog -
that is, a main page generated by Wordpress with a loop showing the most recent x posts.  If you are using a static
main page or have heavily customized your Wordpress theme, you will likely have to manually insert Featurific into your site's
code.

**Customized Theme**
If you have customized your theme, read the following sections of this FAQ for information on inserting Featurific:
* "How do I move Featurific to another location on my page?"
* "How do I make Featurific appear on pages other than the main page?"

**Static Page**
If the page you'd like Featurific to appear on is a static page, there's a bit more work involved.  This process will
likely be simplified in the future, but for the time being, the following log (provided by
[Shireen Jeejeebhoy](http://jeejeebhoy.ca)) will help you get Featurific working on a static page.

> Hi Rich,
> 
> I finally got a chance to download Exec-PHP and try it out with this code copied into my front static page:
> 
> <?php
>       if(function_exists('insert_featurific')) //If the Featurific plugin is activated...
>        insert_featurific();                    //Insert the HTML code to embed Featurific
> ?>
> 
> I discovered how true the following warning from Exec-PHP is:
> 
> "To successfully write PHP code in the content of an article, the WYSIWYG editor needs to be turned off through the ‘Users > Your Profile’ menu. It is not enough to simply keep the WYSIWYG editor on, switch to the ‘Code’ tab of the editor in the ‘Write’ menu and save the article. This will render all contained PHP code permanently unuseful." (http://bluesome.net/post/2005/08/18/50/#execute_php)
> 
> So I tried their suggestion of using another plugin that deactivates the visual editor just for the page I'm putting the (Featurific) php code on. It worked! (I don't like writing in the Code page and much prefer using the WYSIWYG editor, but for those who don't like the WYSIWYG editor, this would not be an issue.) The plugin says it's good up to 2.3.3, but I'm using 2.5.1 and it's working as far as I can see:
> 
> http://wordpress.org/extend/plugins/deactive-visual-editor/
> 
> I think there was another plugin I used a long time ago that required Exec-PHP but then the author got around it somehow and no longer required the use of this plugin. It would be great if in the future, you were able to develop the code to no longer require these two additional plugins. But for now it works. I've also left the code in the index.php so Featurific appears on my main page and my blog page. Pretty cool!


= I hate the logo that appears in the corner all the time.  How can I get rid of it? =
To get rid of the "Powered by Featurific" logo, you're going to have to splurge for the commercial version of Featurific.
[Visit the Featurific website](http://featurific.com) for details ([http://featurific.com](http://featurific.com)).


= I can't get this thing to work!  What's wrong? =
Drop me an email and I'll try to help you out.  (rich at [please don't spam me] byu period net)



== Screenshots ==

1. The options page for the Featurific for Wordpress plugin.
2. An example of the plugin in action.
3. An example of the plugin in action.
4. An example of the plugin in action.
5. An example of the plugin in action.
6. An example of the plugin in action.
7. An example of the plugin in action.
