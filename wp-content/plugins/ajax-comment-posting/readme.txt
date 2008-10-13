=== Ajax Comment Posting ===
Contributors: regua
Donate link: http://regua.biz/donate
Tags: comments, ajax, post, comment, edit
Requires at least: 1.5.2
Tested up to: 2.5
Stable tag: 1.2.3

Posts comments without refreshing the page and validates the comment form using Ajax.

== Description ==

There are many comment-related plugins in Wordpress plugin directory. However, if you'd like to find just a simple comment-posting Ajax plugin, you won't find any. That's why I developed a simple and small (4kB) yet functional Ajax Comment Posting (ACP) plugin. Not only will it post your comment without refreshing the page, but it will also make sure that you've filled all the form fields correctly.

The plugin works well in all major Web browsers, and switches to the traditional comment posting if JavaScript is disabled.

ACP works well with [WP AJAX Edit Comments](http://wordpress.org/extend/plugins/wp-ajax-edit-comments/ "WP AJAX Edit Comments") plugin allowing you to edit and manage comments in an Ajax way, and the users to edit their own comments for a specified amount of time.

ACP should work with all CAPTCHA word-verification plugins, but I personally suggest using [Akismet](http://codex.wordpress.org/Akismet "The Akismet anti-spam plugin").

You can easily add some more functionality to your comment form using [jQuery](http://jquery.com "The jQuery JavaScript framework"), the best JavaScript framework, which is used by ACP to handle the Ajax requests and all JavaScript-related operations.

If you're interested, you can [see the demo](http://demo.regua.biz "The ACP demo").

*NOTE*: plugin and FAQ translations are needed. If you could translate the FAQ along with the Installation section and all messages inside the `lang.js` file, please [send me your translations](http://regua.biz/contact "My contact page") and you will be rewarded. I currently have English, French and Polish translations, so they are not needed.

== Installation ==

1. Upload the plugin directory `ajax-comment-posting` to the `wp-content/plugins` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. That's it!

== Frequently Asked Questions ==

Visit the [plugin page](http://acp.regua.biz/#faq) for FAQ in different languages.

= Why isn't my plugin working or it works differently than it should? =

It's probably your WordPress theme's fault. ACP needs several things to be present in your comments.php file (in the theme's default directory). The submit button has to have a `submit` id, the comment form has to have a `commentform` id, the ol (list) with the comments has to have a `commentlist` class. Also, make sure that the following code is present somewhere in the head (header.php) section of your theme: `&lt;?php wp_head(); ?&gt;`
Most WordPress themes meet these requirements. If yours doesn't - either correct it by yourself or let me know.

= How can I customise the look of the error and success messages? =

You can either change the `acp.css` file in the plugin's directory, or just delete the file and add `error` and `success` classes to your CSS stylesheet.

= The loading icon doesn't show. What can I do? =

You can manually set the direct path to the loading image in the `acp.js` file (line 12).

= How can I change or remove the loading icon? =

The loading icon is the file `loading.gif` inside ACP's directory. If you want to remove the icon, just delete the icon image file and you'll get a 'Loading...' message instead. Also, you can edit the `acp.js` file (line 12).

= Why does the comment form disappear after a comment has been posted? =

For security reasons. Usually users don't want to post two comments in a row, so what's the reason in leaving the form there? If you still want to prevent it from being removed, delete the line 80 in the `acp.js` file.

= I don't want the email address field to be validated. How do I do that? =

Just delete or comment the lines 29-37 in the `acp.js` file.

= Some other plugin installed on my WordPress uses jQuery as well. Can I make ACP use the jQuery library provided with the other plugin so that I didn't need to waste additional 20kB? =

Yes, just edit the `ajax-comment-posting.php` file (line 13) and change the jQuery path. Just make sure it's jQuery 1.2.2 or higher version.

= Is the plugin available in different languages? =

Yes, and all you have to do to change the plugin's language is to download one small file from [the plugin's page](http://acp.regua.biz/#langs) and upload it to your plugin's main directory.

= How does the plugin work? =

Firstly, it validates the form - checks if you've enter a name, (valid) email address and the comment (if you're a logged-in user, you don't have to enter the name and email, of course). Then it submits the form using Ajax (Asynchronous JavaScript and XML), checks if server returned an error and adjusts the display method to the server response. Also, after a successful submission, it appends your newly posted comment to the comment list (or creates one if not present), removes the comment form (see above if you want to prevent this from happening) and displays a nice, green-coloured message.

= Can you help me with it? =

Of course. [Contact me](http://regua.biz/contact "Contact the author of the plugin") if you have any questions, bug reports or suggestions. In case of a bug report or help request, please include your comments.php file from your theme's directory as an attachment to the email / message, and explain your problem thoroughly giving all needed details: your WordPress and ACP version, other Ajax-based plugins you are using, etc.

== Screenshots ==

Visit [the plugin's page](http://acp.regua.biz/#shots "The ACP page") to see the plugin screenshots.

== Changelog ==

The plugin's changelog is available on [the plugin's page](http://acp.regua.biz/#log "The ACP page").

== Demo ==

The demo of Ajax Comment Posting working along with WP Ajax Edit Comments is available at [demo.regua.biz](http://demo.regua.biz "The ACP demo").

== Thanks ==

HUGE thanks to [Aen Tan](http://aendirect.com "Aen's homepage") for solving a WP 2.3.1 bug, correcting my mistakes and preventing the plugin from conflicting with Prototype.

Also, I'd like to thank [Annand Ramsahai](http://baiganchoka.com), [Max Karreth](http://guimkie.com "Max's homepage"), [Gene Steinberg](http://macnightowl.com/ "Gene's homepage"), [Rayne Bair](http://www.wifetalks.com/knits/ "Rayne's homepage") and [Dave Anderson](http://cv.67design.com/ "Dave's homepage") for pointing some errors and suggesting the further development of the plugin.

Special thanks to the translators: [Olivier](http://www.lautre-monde.fr/ "Olivier's homepage") (French) and [SciFi](http://i-tyres.ru/) (Russian).

== Donate ==

Ajax Comment Posting is completely free for both personal and commercial use. If you, however, appreciate my work, you can donate a few bucks to me via PayPal in my [tip jar](http://regua.biz/donate "Donations to the plugin's author"). Thank you very much!
