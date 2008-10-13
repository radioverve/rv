= Photopress =

Tags: images, upload, album

Contributors: isaacwedin

Photopress is a plugin that adds some image-related features to Wordpress. A new "Photos" button on the Quicktags toolbar launches a pop-up image uploader and browser which can insert code for tagged thumbnails or full images into the post editor. In addition, the plugin adds a random image template function, a simple photo album that image links can point to, and some photo management tools.

== Credits ==

Photopress uses code from Florian Jung's Image Browser (not anymore, but that certainly inspired the present code), Alex King's JS Quicktags (via the ContactForm plugin), the built-in Wordpress upload functions, and much from codex.wordpress.org. Many people have contributed bug reports, fixes and suggestions, including Randy, RobotDan, Alex, Jono, Paula, Frank, Roge, SHRIKEE, Hans, and Claus. I appreciate your help!

== Installation ==

1. Extract the archive into your plugins folder. Optionally, make a folder called "photopress" in your plugins folder and place the files from the archive there.
2. Create a folder called "photos" under wp-content and make sure it's writable by your server. (You can try to make the folder writable at Manage:Photopress-->Maintain-->CHMOD Folder. You can use a different folder by changing the settings at Options:Photopress.)
3. Activate the plugin. See the options page at Options:Photopress to customize many settings. If you enable permalinks make sure to update your permalink structure at Options:Permalinks.
4. Once you upload some images, you can enter information about them at Manage:Photopress.
5. The appearance of the album can be customized by editing pp_album.php and pp_album_css.php in the photopress folder. You can store these files in your theme's folder.

== Upgrading ==

1. Deactivate the plugin.
2. Remove all of the old plugin files, retaining your album template files if you modified them. (Note that you can store pp_album.php and pp_album_css.php in your active theme's folder to protect them from overwriting during an upgrade.)
3. Follow the installation instructions.

== Usage ==

Configure options at Options:Photopress. Upload images via the Photos button on the Quicktags toolbar or at Manage:Photopress. Manage uploaded images at Manage:Photopress: enter information about images, delete images, and perform a few maintenance tasks.

To use the random image template function add it where you want the random image(s) in your template. The function contains an echo - there's no need to echo it yourself. Here's the function and its options:

pp_random_image_bare($number_of_images,$before,$after,$class,$category);

Here are the default options, which will be used if you don't specify anything:

pp_random_image_bare(1,'','<br />','random');

'random' isn't a class but a keyword that will use the random image class from Options. If you leave out the category the function will use all of your images.

== Localization ==

The localization template (the POT file) and any contributed translations (MO files) are available at familypress.net. If you make a translation, please share it. To use a translation, place the MO file in your plugins folder. It should be named photopress-<your locale>.mo, and <your locale> should match what you've got for WPLANG in wp-config.php. If there are untranslated things, it's probably because your MO file is older than your Photopress version. To fix that, get the current PO file for your language from familypress.net and update it.

== Frequently Asked Questions ==

=== Why doesn't the button work!? ===

This is a mystery. The button works fine in various browsers on Linux, and both Firefox and IE in Windows XP. Check your pop-up blocking settings. Let me know if you figure out why it doesn't work! I've added an option to enable a "failsafe" button, which isn't placed well but is more likely to work (for example, I've heard that it works in IE on MacOS 9).

=== The album breaks my template/theme!? ===

It's designed to work well with the default theme, so if your theme is creative it may break. If so, edit pp_album.php in the photopress folder to suit. It's got a couple of notes about what you'll probably need to do -- basically, make pp_album.php resemble your theme's index.php. The style for the album is in pp_album_css.php, so look there to change colors, add borders to photos, etc. Both files can be stored in your active theme's folder to help you avoid overwriting them when you upgrade.

=== Why add the random image to the Meta list? ===

Because there's a plugin hook in Wordpress to do that. If you don't like the default random image, disable it on the Options:Photopress page and add <?php pp_random_image_bare(); ?> where you want to display a random image in your template. Again, the default random image feature is designed to work with the default Wordpress theme so YMMV. It uses the "centered" class from the default theme, so if your theme doesn't have that it might not look right. You can switch to a custom class at Options:Photopress.

=== I've already got a bunch of images in a folder, how can I add them to Photopress? ===

If your old folder is under wp-content you can just point Photopress at that, assuming you've got thumbnails and they conform to the naming setup and sizes in Options:Photopress (by default "thumb_" is added to the start of the image name, but you can change that to suit). Use the import tool at Manage:Photopress to import the photos into the database. As of 0.8 you can upload up to 10 images at once with the uploader.

=== The Options page isn't there, is that a joke? ===

It's not a joke, it's a bug! See http://mosquito.wordpress.org/view.php?id=1196 for details. Basically, an Options page will only appear for the last-loaded plugin. Upgrading to WP 1.5.1 or better fixes the problem. It's possible to work around the bug by disabling plugins, editing the Options, then re-enabling, but you really should just upgrade.

=== How do I delete an image? ===

Go to Manage:Photopress and click through to an image. Depending on how you've set up Options:Photopress, there could be a delete button or a list of posts that use the image, or both. You can delete multiple images in the Mass View at Manage:Photopress (if you've set the option to do so).

=== Why aren't the Photo Album link and random thumb showing up in Meta? ===

You probably haven't uploaded any images yet, or you need to re-activate the plugin (or use the maintenance tools at Manage:Photopress) to import your existing images into the DB.
