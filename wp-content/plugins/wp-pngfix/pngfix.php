<?
/*
 * Plugin Name: WP Pngfix
 * Plugin URI: http://www.tahapaksu.com/wordpress/pngfix-wordpress-plugin
 * Description: Fixes the png transparency on IE for wordpress blogs
 * Version: 0.2
 * License: Free to Use
 * Author: Taha Paksu
 * Author URI: http://www.tahapaksu.com/
 */

add_action('wp_head', 'pngfix_apply');
function pngfix_apply(){
$plugindir = get_option('siteurl') . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__));
	$css = '
	.png {
		background-image: expression(
		this.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=" + (this.tagName==\'IMG\' ? this[\'src\'] : this.currentStyle[\'backgroundImage\'].split(\'\"\')[1]) + ")",
		this.runtimeStyle.backgroundImage = "none",
		this.src = "'.$plugindir.'/pixel.gif",
		this.width = this.style.width | this.clientWidth,
		this.height = this.style.height | this.clientHeight
		);
	}

	.pngbg {
			behavior : url('.$plugindir.'/iepngfix.htc);
		}
	';
	echo '<!--[if lt IE 7]><style type="text/css"  type="text/css" media="screen, projection">'.$css.'</style><![endif]-->
	<script type="text/javascript" src="'.$plugindir.'/jquery.js"></script>';
?>
	<script type="text/javascript">
		jQuery(function($){
			$(document).ready(function(){
				if(($.browser.msie)&(parseInt($.browser.version)<7)){
					$("img[src$='.png']").each(function(){$(this).addClass("png");});
				}
			});
		});
	</script>
<?}?>