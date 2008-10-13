<?php
/* Photopress album template. Designed to work with the Wordpress default theme. If it doesn't work with your theme, try modifying it to look like your theme's index.php (replace the Loop with <?php pp_album(); ?> See pp_album_css.php for the CSS, which can also be customized. The pp_wrap id can be used to customize the headings and whatnot, with something like:  #pp_wrap h2 { font-family: Arial; } */ 
?>

<?php get_header(); ?>

<div id="content" class="narrowcolumn">

		<div id="pp_wrap">
		<h2><a href="<?php global $pp_options; echo $pp_options['albumaddress'] . '">' . __('Photos','photopress'); ?></a></h2>
		<?php pp_album(); ?>
		</div>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
