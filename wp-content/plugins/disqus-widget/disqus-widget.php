<?php
/*
Plugin Name: Disqus Widget
Plugin URI: http://i-nz.net/projects/wordpress/
Description: This plugin adds a new widget which can show statistics for your blog's/forum's Disqus comments: most popular discussions, top commenters, recent comments.
Author: Ivan N. Zlatev
Version: 0.3
Author URI: http://i-nz.net
License: MIT/X11
*/

$PLUGIN_NAME = "Disqus Widget";
$WIDGET_TITLE = "Disqus";
$COLORS = array ("Blue" => "blue",
		 "Grey" => "grey",
		 "Green" => "green",
		 "Red" => "red",
		 "Orange" => "orange");
$TABS = array ("People" => "people",
               "Recent" => "recent",
               "Popular" => "popular");

add_action("plugins_loaded", "disqus_widget_init");

function disqus_widget_init()
{
	global $WIDGET_TITLE;
    disqus_widget_setup ();
    register_sidebar_widget ($WIDGET_TITLE, 'disqus_widget_render');
    register_widget_control ($WIDGET_TITLE, 'disqus_widget_preferences');
}

function disqus_widget_render ($args)
{
	global $PLUGIN_NAME;
    disqus_widget_setup ();
    $options = get_option ($PLUGIN_NAME);
    extract($args);
    echo $before_widget;
    if ($options['titlebar-visible']) {
        echo $before_title;
        echo $options['title'];
        echo $after_title;
    }
    echo disqus_widget_get_content ();
    echo $after_widget;
}

function disqus_widget_get_content ()
{
	global $PLUGIN_NAME;
    $options = get_option ($PLUGIN_NAME);
    if (empty ($options["blog-id"]))
        return "The Disqus blog identifier is either not correct or is not set at all.";

    $template = '<script type="text/javascript" src="http://disqus.com/forums/{{{blog-id}}}/combination_widget.js?num_items={{{items}}}&color={{{color}}}&default_tab={{{tab}}}"></script>';
    $settings = array (
        "{{{items}}}" => $options["items"],
        "{{{color}}}" => $options["color"],
        "{{{tab}}}" => $options["tab"],
        "{{{blog-id}}}" => $options["blog-id"],
    );
    foreach ($settings as $search => $replace)
        $template = str_replace ($search, $replace, $template);
    return $template;
}

function disqus_widget_setup ()
{
    global $PLUGIN_NAME;

    $options = get_option ($PLUGIN_NAME);
    if (!is_array ($options) || empty ($options["title"])) {
        $options = array ("title" => "Comments",
                          "items" => 5,
                          "color" => "blue",
                          "tab" => "people",
						  "titlebar-visible" => true,
						  "blog-id" => NULL
                          );
        update_option ($PLUGIN_NAME, $options);
    }
}

function disqus_widget_preferences ()
{
    global $PLUGIN_NAME, $COLORS, $TABS;
    $ITEMS_MAX = 20;
    $ITEMS_MIN = 1;

    $options = get_option ($PLUGIN_NAME);
    if ($_POST["submit-settings"]) {
        $options['title'] = htmlspecialchars ($_POST['title']);
        $options["color"] = $_POST['color'];
        $options["tab"] = $_POST['tab'];
        $items = (int)$_POST['items'];
        if ($items > $ITEMS_MAX)
            $items = $ITEMS_MAX;
        else if ($items < $ITEMS_MIN)
            $items = $ITEMS_MIN;
        $options["items"] = $items;
        if ($_POST["titlebar-visible"])
            $options["titlebar-visible"] = true;
        else
            $options["titlebar-visible"] = false;
        if (!empty ($_POST['blog-id']))
            $options["blog-id"] = $_POST['blog-id'];
    }
    update_option($PLUGIN_NAME, $options);
?>

<p><label for="titlebar-visible">
    <strong>Disqus Forum/Blog ID (<u>ID</u>.disqus.com):</strong>
</label>
<input type="text" id="blog-id" name="blog-id" value="<?php echo $options['blog-id'];?>"/></p>
<p><label for="color">
    Color Theme:
</label>
<select name="color">
    <option value="blue" <?php if($options['color'] == "blue") { echo "selected"; }?>>Blue</option>
    <option value="grey" <?php if($options['color'] == "grey") { echo "selected"; }?>>Grey</option>
    <option value="green" <?php if($options['color'] == "green") { echo "selected"; }?>>Green</option>
    <option value="red" <?php if($options['color'] == "red") { echo "selected"; }?>>Red</option>
    <option value="orange" <?php if($options['color'] == "orange") { echo "selected"; }?>>Orange</option>
</select></p>
<p><label for="tab">
    Default Tab View:
</label>
<select name="tab">
    <option value="people" <?php if($options['color'] == "people") { echo "selected"; }?>>People</option>
    <option value="recent" <?php if($options['color'] == "recent") { echo "selected"; }?>>Recent</option>
    <option value="popular" <?php if($options['color'] == "popular") { echo "selected"; }?>>Popular</option>
</select></p>
<p><label for="items">
    Number of items to show (up to 20):
</label>
<input type="items" id="" name="items" size="2" maxlength="2" value="<?php echo $options['items'];?>" /></p>
<p><input type="checkbox" id="titlebar-visible" name="titlebar-visible" value="titlebar-visible" <?php if($options['titlebar-visible']) { echo "checked"; }?>/>
<label for="titlebar-visible">
    Display Widget Title Bar:
</label></p>
<p><label for="title">
    Title:
</label>
<input type="text" id="" name="title" value="<?php echo $options['title'];?>" /></p>
<input type="hidden" id="submit-settings" name="submit-settings" value="1" />


<?php
}
?>
