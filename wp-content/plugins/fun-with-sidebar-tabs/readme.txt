=== Plugin Name ===
Contributors: arickmann
Donate link: http://www.wp-fun.co.uk/
Tags: Sidebar, Tabbed, Tabs, Widget, Template
Requires at least: 2.5
Tested up to: 2.5
Stable tag: 0.5

Creates one or more tabbed dynamic sidebars for your sidebar

== Description ==

This plugin adds a new dynamic sidebar which displays the widgets in it as tabs, instead of as a list.
It adds a widget, and a template tag, so that it can be inserted wherever it needs to be.

== Installation ==

Unzip the plugin into the plugins directory and activate.
In wp_admin go to Widgets, under the presentation tab.
Here you can select the number of tabbed sidebars that you want using the panel at the bottom. 
You can them use either the Fun with Sidebars Widget (by adding each Tabbed Sidebar widget into your main sidebar) or the template tag - 

`<?php the_tabbed_sidebar( Tabbed Sidebar number ); ?>`

You can then add widgets to each Tabbed Sidebar on the same page.

== Styling the plugin ==

If you use the widget to add the tabbed sidebar to you main sidebar you can also re-style the CSS.
After you have added the widget to the sidebar open the tabbed sidebar widget options and you will be presented with boxes containing the default CSS. You can now tweak this to your satisfaction.

== Frequently Asked Questions ==

= I am using the theme tag not the widget, can I restyle it? =

At the moment there is no interface for that. 

There is a workaround however.

1. Drag the widget into any sidebar (even the tabbed sidebar itself)
2. Use the widget's settings to style the CSS
3. Save the widget settings
4. Remove the widget from the sidebar
5. Resave the settings

I't ain't elegent, but it works.

= The default styles are not working properly, what's going on? =

CSS is tricky to write for all situations so it is possible that it is being overidden by other, more specific settings.
If possible do not include the sidebar in multiple nested lists, that can cause problems.
To diagnose the problems try moving the sidebar so it is not inside any element except a div tag. If that works the problem is that the CSS where you want to place the sidebar is overriding the default.

== Changes ==

= 0.5 =

Note: There have been some changes to the CSS so you may need to make amendments to each sidebar's settings.

Moved the minimum requirement from 2.3 to 2.5.
Amended the CSS to use IDs instead of classes to try and overcome the CSS specifity issues.
Switched the order of the CSS and Javascript loading for Safari and FF3 to make sure the CSS loads before the Javascript runs.
Updated the Javascript to cope with the way PCSafari and FF3 trigger document ready earlier than expected.
Updated the calculation for tab levels to prevent wrapping.

= 0.4.2 =

Amended to try and cope with Jquery 1.1.4

= 0.4 =

This version now uses JQuery instead of Prototype, and can deal with more tabs than the width of the box allows.