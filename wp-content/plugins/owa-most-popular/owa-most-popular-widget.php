<?PHP
/*
Plugin Name: OWA Most Popular
Plugin URI: http://www.kvaes.be/scripts/owa-most-popular/
Description: Adds a sidebar widget to display the most popular posts.  The stats are generated on the data provided by the OWA plugin.
Author: Karim Vaes
Version: 1.7.0
Author URI: http://kvaes.be/
*/
 
/*
* Copyright (c) 2007, Karim Vaes
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*     * Neither the name of the kvaes.be nor the
*       names of its contributors may be used to endorse or promote products
*       derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY KARIM VAES ``AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL KARIM VAES BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

function owa_live_traffic_feed($max_owa_popular=10)
{
  global $wpdb;
  $uri=get_template_directory_uri()."/owa-images/";
  $sql.="SELECT host.city, host.country, doc.page_title, doc.url as ourl, req.year, req.month, req.day, req.hour, req.minute, ref.url as rurl";
  $sql.=" FROM `owa_request` AS req";
  $sql.=" LEFT JOIN `owa_referer` AS ref ON ref.id = req.referer_id";
  $sql.=" AND ref.is_searchengine !=1";
  $sql.=" INNER JOIN `owa_document` AS doc ON doc.id = req.document_id";
  $sql.=" AND doc.page_type IN ('Home', 'Post', 'Page')";
  $sql.=" INNER JOIN `owa_host` AS host ON host.id = req.host_id";
  $sql.=" ORDER BY req.timestamp DESC";
  $sql.=" LIMIT 0,".$max_owa_popular;
  $results = $wpdb->get_results($sql, OBJECT);
  if ($results) {
    $output.='<ul>';
    foreach ($results as $result) {
      $title = $result->page_title;
      $url = $result->ourl;
      $ref = $result->rurl;
      preg_match('@^(?:http://)?([^/]+)@i',$ref, $matches);
      $rurl = $matches[1];
      $city = $result->city;
      $country = $result->country;
      $iso=strtolower(substr($country,(strrpos($country,"(")+1),(strrpos($country,")")-1-strrpos($country,"("))));
      switch (TRUE) {
        case ((strlen(trim($country)) < 2) && ($country <> "xx")):
          $img="";
          $refer='';
          break;
        case ($ref <> ""):
          $img='<img src="'.$uri.$iso.'.png"></a>';
	  $refer='<a href="'.$ref.'">(Refered by '.$rurl.')</a>';
          break;
        default:
          $img='<img src="'.$uri.$iso.'.png">';
          $refer='';
          break;
      }
      $output.='<li>';
      $output.=$img.' <a href="'.$url.'">'.$title.'</a> '.$refer;
      $output.='</li>';
    }
    $output.='</ul>';
  }
  return $output;
}

function owa_category_popular($category="all",$max_owa_popular=10)
{
  // CheckCache
  $output=wp_cache_get('owa_category_popular');
  if ($output === false) {
    global $wpdb;
    $sql="SELECT doc.page_title as title, doc.url as url";
    $sql.=" FROM ".$wpdb->prefix."terms as term";
    $sql.=" INNER JOIN ".$wpdb->prefix."term_taxonomy as tax ON tax.term_id = term.term_id";
    $sql.=" INNER JOIN ".$wpdb->prefix."term_relationships as rel ON tax.term_taxonomy_id =rel.term_taxonomy_id";
    $sql.=" INNER JOIN ".$wpdb->prefix."posts as post ON rel.object_id = post.ID";
    $sql.=" INNER JOIN owa_document as doc ON doc.url = post.guid";
    $sql.=" INNER JOIN owa_request as req ON doc.id = req.document_id";
    if (is_numeric($category)) {
      $sql.=" WHERE term.term_id = ".$category;
    }
    $sql.=" GROUP BY doc.id";
    $sql.=" ORDER BY count(doc.id) DESC";
    $sql.=" LIMIT 0,".$max_owa_popular;
    $results = $wpdb->get_results($sql, OBJECT);
    if ($results) {
      $output.='<ul>';
      foreach ($results as $result) {
        $title = $result->title;
        $url = $result->url;
        $output.='<li>';
        $output.='<a href="'.$url.'">'.$title.'</a>';
        $output.='</li>';
      }
      $output.='</ul>';
      wp_cache_set('owa_category_popular', $output, 'owa_popular', '60');
    }
  }
  return $output;
}
 
function owa_weighted_popular($max_owa_popular=10) 
{
  // CheckCache
  $output=wp_cache_get('owa_weighted_popular');
  if ($output === false) {
    global $wpdb;
    // The Magic
    $sql.="SELECT count(doc.id) as count, post.post_date_gmt as postdate, post.post_title as name,";
    $sql.=" DATEDIFF(CURDATE(),post.post_date_gmt) as ddate,";
    $sql.=" count(doc.id)/(DATEDIFF(CURDATE(),post.post_date_gmt)+1) as factor, doc.url as url";
    $sql.=" FROM owa_request as req";
    $sql.=" INNER JOIN owa_document as doc ON req.document_id = doc.id";
    $sql.=" INNER JOIN ".$wpdb->prefix."posts as post ON doc.url = post.guid";
    $sql.=" GROUP BY doc.id";
    $sql.=" ORDER BY factor DESC, count(doc.id) DESC";
    $sql.=" LIMIT 0,".$max_owa_popular;

    // Fetch
    $results = $wpdb->get_results($sql, OBJECT);
    if ($results) {
      $output.='<ul>';
      foreach ($results as $result) {
        $title = $result->name;
        $url = $result->url;
        $factor = $result->factor;
        $output.='<li>';
        $output.='<a href="'.$url.'">'.$title.'</a>';
        $output.='</li>';
      }
      $output.='</ul>';
      wp_cache_set('owa_weighted_popular', $output, 'owa_popular', '60');
    }
  }
  return $output;
} 

function owa_api_popular($max_owa_popular=10,$date_range_owa_popular="last_seven_days",$post_only_owa_popular=TRUE)
{
  $output=wp_cache_get('owa_api_popular');  

  if ($output == false) {
    global $wpdb;
    global $owa_wp; // OWA must be an active plugin for this to work

    $owa_params = array();
    $owa_params['site_id'] = $owa_wp->config['site_id']; // the current site id
    $owa_params['period'] = $date_range_owa_popular;
    $owa_params['limit'] = $max_owa_popular; // the number of top pages you want

    if ($post_only_owa_popular) { $owa_params['constraints']['page_type'] = 'Post'; }

    //$top_pages_data = $owa_wp->api->getMetric('base.topPages', $owa_params);
    $api = &owa_coreAPI::singleton($owa_params);
    $top_pages_data = $api->getMetric('base.topPages', $owa_params);

    if (count($top_pages_data) > 0) {
      $output.='<ul>';
      foreach ($top_pages_data as $k => $v) {
        $title = $v["page_title"];
        $url = $v["url"];
        $output.='<li>';
        $output.='<a href="'.$url.'">'.$title.'</a>';
        $output.='</li>';
      }
      $output.='</ul>';
    }
    wp_cache_set('owa_api_popular', $output, 'owa_popular', 60);
  }
  return $output;
}
 
function widget_owa_popular_init()
{
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// This saves options and prints the widget's config form.
	function widget_owa_popular_control()
	{
		$options = $newoptions = get_option('widget_owa_popular');
		if ( $_POST['owapopular-submit'] )
		{
			$newoptions['limit'] = strip_tags(stripslashes($_POST['owapopular-limit']));
			$newoptions['postonly'] = strip_tags(stripslashes($_POST['owapopular-postonly']));
			$newoptions['style'] = strip_tags(stripslashes($_POST['owapopular-style']));
			$newoptions['range'] = strip_tags(stripslashes($_POST['owapopular-range']));
			$newoptions['category'] = strip_tags(stripslashes($_POST['owapopular-category']));
		}
		if ( $options != $newoptions )
		{
			$options = $newoptions;
			update_option('widget_owa_popular', $options);
		}
		if ($options['limit'] == "") { $options['limit']=10; }
		if ($options['range'] == "") { $options['range']='last_seven_days'; }
		switch ($options['postonly']) {
			case "FALSE":
				$options['postonly']="";
				break;
			default:
				$options['postonly']="CHECKED";
				break;
		}
	?>
			<div style="text-align:right">
				<label for="owapopular-style" style="line-height:35px;display:block;">Algorithm: 
					<select id="owapopular-style" name="owapopular-style">
						<option value="api" <?php selected('api',$options['style']); ?>>Regular</option>
                                                <option value="weighted" <?php selected('weighted',$options['style']); ?>>Weighted (Resource Intensive!)</option>
						<option value="live" <?php selected('live',$options['style']); ?>>Live Feed</option>
						<option value="category" <?php selected('weighted',$options['style']); ?>>Category (Mildly Resource Intensive!)</option>
					</select>
				</label>
				<label for="owapopular-range" style="line-height:35px;display:block;">Date range:
                                        <select id="owapopular-range" name="owapopular-range">
                                                <option value="today" <?php selected('today',$options['range']); ?>>Today</option>
						<option value="last_24_hours" <?php selected('last_24_hours',$options['range']); ?>>Last 24 Hours</option>
						<option value="last_hour" <?php selected('last_hour',$options['range']); ?>>Last Hour</option>
						<option value="last_half_hour" <?php selected('last_half_hour',$options['range']); ?>>Last Half Hour</option>
						<option value="last_seven_days" <?php selected('last_seven_days',$options['range']); ?>>Last Seven Days</option>
						<option value="this_week" <?php selected('this_week',$options['range']); ?>>This Week</option>
						<option value="this_month" <?php selected('this_month',$options['range']); ?>>This Month</option>
						<option value="this_year" <?php selected('this_year',$options['range']); ?>>This Year</option>
						<option value="yesterday" <?php selected('yesterday',$options['range']); ?>>Yesterday</option>
						<option value="last_week" <?php selected('last_week',$options['range']); ?>>Last Week</option>
						<option value="last_month" <?php selected('last_month',$options['range']); ?>>Last Month</option>
						<option value="last_year" <?php selected('last_year',$options['range']); ?>>Last Year</option>
						<option value="same_day_last_week" <?php selected('same_day_last_week',$options['range']); ?>>Same Day Last Week</option>
						<option value="same_week_last_year" <?php selected('same_week_last_year',$options['range']); ?>>Same Week Last Year</option>
						<option value="same_month_last_year" <?php selected('same_month_last_year',$options['range']); ?>>Same Month Last Year</option>
						<option value="all_time" <?php selected('all_time',$options['range']); ?>>All Time</option>
                                                <option value="last_tuesday" <?php selected('last_tuesday',$options['range']); ?>>Last Tuesday</option>
                                                <option value="last_thirty_days" <?php selected('last_thirty_days',$options['range']); ?>>Last Thirty Days</option>

                                        </select>
                                </label>

				<label for="owapopular-limit" style="line-height:35px;display:block;">Maximum posts: <input type="text" id="owapopular-limit" name="owapopular-limit" value="<?php echo htmlspecialchars($options['limit']); ?>" /></label>
				<label for="owapopular-postonly" style="line-height:35px;display:block;">Posts only? <input class="checkbox" type="checkbox" <?php echo htmlspecialchars($options['postonly']); ?> id="owapopular-postonly" name="owapopular-postonly" /></label>
				<label for="owapopular-category" style="line-height:35px;display:block;">Category ID: <input type="text" id="owapopular-category" name="owapopular-category" value="<?php echo htmlspecialchars($options['category']); ?>" /></label>
				<input type="hidden" name="owapopular-submit" id="owapopular-submit" value="1" />
			</div>
	<?php
	}
        function widget_owa_live_feed($args=dummy)
	{
		extract($args);
		$widgettitle=_('Live Traffic Feed');
		$output=owa_live_traffic_feed();
		print $before_widget.$before_title.$widgettitle.$after_title.$output.$after_widget;
	}
 
	function widget_owa_popular($args=dummy)
	{
		extract($args);
		$defaults = array('limit' => 10, 'postonly' => 'TRUE', 'style' => 'api', 'range' => 'last_seven_days', 'category' => '');
		$options = (array) get_option('widget_owa_popular');
	
		foreach ( $defaults as $key => $value )
			if ( !isset($options[$key]) )
				$options[$key] = $defaults[$key];
		$max_owa_popular=$options['limit'];
		
                $style_owa_popular=$options['style'];
		switch ($style_owa_popular) {
                  case "weighted":
			$widgettitle=_('Most Popular Posts');
			$output=owa_weighted_popular($max_owa_popular);
			break;
		  case "live":
                        $widgettitle=_('Live Traffic Feed');
			$output=owa_live_traffic_feed($max_owa_popular);
			break;
		  case "category":
			$widgettitle=_('Most Popular Posts');
			$owa_category=$options['category'];
			$output=owa_category_popular($owa_category,$max_owa_popular);
			break;
		  default:
                        $widgettitle=_('Most Popular Posts');
			$date_range_owa_popular=$options['range'];
			$post_only_owa_popular=$options['postonly'];
			$output=owa_api_popular($max_owa_popular,$date_range_owa_popular,$post_only_owa_popular);
			break;
                }
		print $before_widget.$before_title.$widgettitle.$after_title.$output.$after_widget;
	}
	register_widget_control('OWA Most Popular', 'widget_owa_popular_control', 315, 420); 
	register_sidebar_widget('OWA Most Popular', 'widget_owa_popular');
	register_sidebar_widget('OWA Live Feed', 'widget_owa_live_feed');
}
 
add_action('init', 'widget_owa_popular_init');
?>
