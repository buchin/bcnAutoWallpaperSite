<?php
	/*
	Plugin Name: WP Review Site
	Plugin URI: http://www.wpreviewsite.com
	Description: Allows you to build a review site with WordPress by adding star ratings and affiliate marketing tools. Use settings menu to configure. This is a single blog license. To use this plugin on another blog, you must purchase a new license from <a href="http://www.wpreviewsite.com">wpreviewsite.com</a>.
	Author: Dan Grossman
	Author URI: http://www.dangrossman.info
	Version: 2.0
	*/ 
	
	
	/** Set Up Constants **/
	global $wpdb;
	$wpdb->ratings = $wpdb->prefix . 'rs_ratings';
	
	if (!defined('WP_CONTENT_DIR')) {
		define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
	}
	if (!defined('WP_CONTENT_URL')) {
		define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
	}
	if (!defined('WP_PLUGIN_DIR')) {
		define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
	}
	if (!defined('WP_PLUGIN_URL')) {
		define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	}

	/** Hook Into WordPress **/

	add_action('init', 'rs_init');

	function rs_init() {
		add_action('admin_menu', 'rs_config_page');
		add_action('comment_post', 'rs_comment_posted');
		add_action('wp_head', 'rs_css');
		wp_register_script('rs_js', get_bloginfo('wpurl') . '/wp-content/plugins/review-site/review-site.js');
		wp_enqueue_script('rs_js');
		
		if (is_admin())
			add_filter('get_comment_text', 'rs_comment_text');
			
		add_filter('the_content', 'rs_affiliate_markup');
				
		$reorder = get_option('rs_reorder');
		if ($reorder == true) {
			add_filter('posts_fields', 'rs_fields');
			add_filter('posts_join', 'rs_join');
			add_filter('posts_groupby', 'rs_groupby');
			add_filter('posts_orderby', 'rs_orderby');
		}
	}
		
	/*
	 * Returns a keyed array of average ratings for a specified post. If used within The Loop with 
	 * no arguments, it will return the ratings for the post being displayed. The post ID can be 
	 * overridden with the $custom_id parameter. The format of the array:
	 * array( [Category 1] => 2.5, [Category 2] => 3.2, [Category 3] => 4.5 )
	 *
	 * Use num_to_stars to convert numeric values to star images.
	 */
	function get_ratings($custom_id = null) {
		global $id, $wpdb;
		$pid = $id;
		if (is_numeric($custom_id))
			$pid = $custom_id;
		
		$categories = get_option('rs_categories');
		
		$query = "SELECT rating_id, SUM(rating_value) / COUNT(rating_value) AS `rating_value` 
				  FROM {$wpdb->ratings} 
				  INNER JOIN {$wpdb->comments} 
				  	ON {$wpdb->comments}.comment_ID = {$wpdb->ratings}.comment_id 
				  WHERE {$wpdb->comments}.comment_post_ID = $pid 
				  	AND {$wpdb->comments}.comment_approved = 1
				  GROUP BY rating_id
				  ORDER BY rating_id";
				  	
		$result = $wpdb->get_results($query);
		
		$ratings = array();
		foreach ($categories as $cid => $cat)
			$ratings[$cat] = 0;
		
		if (count($result) > 0)
			foreach ($result as $rating)
				$ratings[$categories[$rating->rating_id]] = $rating->rating_value;

		return $ratings;
	}
		
	/*
	 * Outputs an unordered list with average ratings for a specified post. If used within 
	 * The Loop with no arguments, it will display the ratings for the post being displayed. 
	 * The post ID can be overridden with the $custom_id parameter. The output format will be:
	 * 
	 * <ul class="ratings">
	 *  <li><label class="rating_label">Category 1</label> <span class="rating_value"><img src="star.png">...</span></li>
	 *  <li><label class="rating_label">Category 2</label> <span class="rating_value"><img src="star.png">...</span></li>
	 *  <li><label class="rating_label">Category 3</label> <span class="rating_value"><img src="star.png">...</span></li>
	 * </ul>
	 * 
	 */

	function ratings_list($custom_id = null) {
		global $id, $wpdb;
		$pid = $id;
		if (is_numeric($custom_id))
			$pid = $custom_id;
					
		$categories = get_option('rs_categories');
		
		$query = "SELECT rating_id, SUM(rating_value) / COUNT(rating_value) AS `rating_value` 
				  FROM {$wpdb->ratings} 
				  INNER JOIN {$wpdb->comments} 
				  	ON {$wpdb->comments}.comment_ID = {$wpdb->ratings}.comment_id 
				  WHERE {$wpdb->comments}.comment_post_ID = $pid 
				  	AND {$wpdb->comments}.comment_approved = 1
				  GROUP BY rating_id
				  ORDER BY rating_id";
				  	
		$result = $wpdb->get_results($query);
		
		$ratings = array();
		foreach ($result as $row)
			$ratings[$row->rating_id] = $row->rating_value;
			
		echo '<ul class="ratings"><li>';
		$i = 0;
		foreach ($categories as $cid => $cat) {
			echo '<label class="rating_label">' . $cat . '</label> ';
			echo '<span class="rating_value">';
			
			if (isset($ratings[$cid]))
				echo num_to_stars($ratings[$cid]);
			else
				echo 'No Ratings';
			
			echo '</span>';
			if ($i < count($categories) - 1)
				echo "</li><li>";
			$i++;
		}
		echo "</li></ul>";
		
	}
		
	/*
	 * Outputs a table with average ratings for a specified post. If used within 
	 * The Loop with no arguments, it will display the ratings for the post being displayed. 
	 * The post ID can be overridden with the $custom_id parameter. The output format will be:
	 * 
	 * <table class="ratings">
	 *  <tr><td class="rating_label">Category 1</td><td class="rating_value"><img src="star.png">...</td></tr>
	 *  <tr><td class="rating_label">Category 2</td><td class="rating_value"><img src="star.png">...</td></tr>
	 *  <tr><td class="rating_label">Category 3</td><td class="rating_value"><img src="star.png">...</td></tr>
	 * </table>
	 * 
	 */
	function ratings_table($custom_id = null) {
		global $id, $wpdb;
		$pid = $id;
		if (is_numeric($custom_id))
			$pid = $custom_id;
		
		$categories = get_option('rs_categories');
		
		$query = "SELECT rating_id, SUM(rating_value) / COUNT(rating_value) AS `rating_value` 
				  FROM {$wpdb->ratings} 
				  INNER JOIN {$wpdb->comments} 
				  	ON {$wpdb->comments}.comment_ID = {$wpdb->ratings}.comment_id 
				  WHERE {$wpdb->comments}.comment_post_ID = $pid 
				  	AND {$wpdb->comments}.comment_approved = 1
				  GROUP BY rating_id
				  ORDER BY rating_id";
				  	
		$result = $wpdb->get_results($query);
		
		$ratings = array();
		foreach ($result as $row)
			$ratings[$row->rating_id] = $row->rating_value;
			
		echo '<table class="ratings"><tr>';
		$i = 0;
		foreach ($categories as $cid => $cat) {
			echo '<td class="rating_label">' . $cat . '</td>';
			echo '<td class="rating_value">';
			
			if (isset($ratings[$cid]))
				echo num_to_stars($ratings[$cid]);
			else
				echo 'No Ratings';
			
			echo '</td>';
			if ($i < count($categories) - 1)
				echo "</tr><tr>";
			$i++;
		}
		echo "</tr></table>";

	}
	
	/*
	 * Outputs an unordered list with ratings given with a specified comment. If used within 
	 * the comment loop with no arguments, it will display the ratings for the comment being displayed. 
	 * The comment ID can be overridden with the $custom_id parameter. The output format will be:
	 * 
	 * <ul class="ratings">
	 *  <li><label class="rating_label">Category 1</label> <span class="rating_value"><img src="star.png">...</span></li>
	 *  <li><label class="rating_label">Category 2</label> <span class="rating_value"><img src="star.png">...</span></li>
	 *  <li><label class="rating_label">Category 3</label> <span class="rating_value"><img src="star.png">...</span></li>
	 * </ul>
	 * 
	 */
	function comment_ratings_list($custom_id = null) {
		global $comment, $wpdb;
		$id = $comment->comment_ID;
		if (is_numeric($custom_id))
			$id = $custom_id;
				
		$categories = get_option('rs_categories');
		
		$query = "SELECT rating_id, SUM(rating_value) / COUNT(rating_value) AS `rating_value` 
				  FROM {$wpdb->ratings} 
				  INNER JOIN {$wpdb->comments} 
				  	ON {$wpdb->comments}.comment_ID = {$wpdb->ratings}.comment_id 
				  WHERE {$wpdb->comments}.comment_ID = $id 
				  	AND {$wpdb->comments}.comment_approved = 1
				  GROUP BY rating_id
				  ORDER BY rating_id";
				  	
		$result = $wpdb->get_results($query);
		
		$ratings = array();
		foreach ($result as $row)
			$ratings[$row->rating_id] = $row->rating_value;
			
		echo '<ul class="ratings"><li>';
		$i = 0;
		foreach ($categories as $id => $cat) {
			echo '<label class="rating_label">' . $cat . '</label> ';
			echo '<span class="rating_value">';
			
			if (isset($ratings[$id]))
				echo num_to_stars($ratings[$id]);
			else
				echo 'Not Rated';
			
			echo '</span>';
			if ($i < count($categories) - 1)
				echo "</li><li>";
			$i++;
		}
		echo "</li></ul>";

	}
	
	/*
	 * Outputs a table with ratings given with a specified comment. If used within 
	 * the comment loop with no arguments, it will display the ratings for the comment being displayed. 
	 * The comment ID can be overridden with the $custom_id parameter. The output format will be:
	 * 
	 * <table class="ratings">
	 *  <tr><td class="rating_label">Category 1</td><td class="rating_value"><img src="star.png">...</td></tr>
	 *  <tr><td class="rating_label">Category 2</td><td class="rating_value"><img src="star.png">...</td></tr>
	 *  <tr><td class="rating_label">Category 3</td><td class="rating_value"><img src="star.png">...</td></tr>
	 * </table>
	 * 
	 */
	function comment_ratings_table($custom_id = null) {
		global $comment, $wpdb;
		$id = $comment->comment_ID;
		if (is_numeric($custom_id))
			$id = $custom_id;
				
		$categories = get_option('rs_categories');
		
		$query = "SELECT rating_id, SUM(rating_value) / COUNT(rating_value) AS `rating_value` 
				  FROM {$wpdb->ratings} 
				  INNER JOIN {$wpdb->comments} 
				  	ON {$wpdb->comments}.comment_ID = {$wpdb->ratings}.comment_id 
				  WHERE {$wpdb->comments}.comment_ID = $id 
				  	AND {$wpdb->comments}.comment_approved = 1
				  GROUP BY rating_id
				  ORDER BY rating_id";
				  	
		$result = $wpdb->get_results($query);
		
		$ratings = array();
		foreach ($result as $row)
			$ratings[$row->rating_id] = $row->rating_value;
			
		echo '<table class="ratings"><tr>';
		$i = 0;
		foreach ($categories as $id => $cat) {
			echo '<td class="rating_label">' . $cat . '</td>';
			echo '<td class="rating_value">';
			
			if (isset($ratings[$id]))
				echo num_to_stars($ratings[$id]);
			else
				echo 'Not Rated';
			
			echo '</td>';
			if ($i < count($categories) - 1)
				echo "</tr><tr>";
			$i++;
		}
		echo "</tr></table>";

	}
	
	/* 
	 * Displays the HTML and JavaScript to collect star ratings within the comment form.
	 * Styled with an unordered list.
	 */
	function ratings_input_list() {
	
		$categories = get_option('rs_categories');

		$i = 0;
		echo '<ul class="ratings"><li>';
		foreach ($categories as $id => $cat) {
			echo '<label class="rating_label" style="float: left">' . $cat . '</label> ';
			echo '<div class="rating_value">';
			echo '<a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_1" title="1" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_2" title="2" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_3" title="3" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_4" title="4" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_5" title="5" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <input type="hidden" id="' . $id . '_rating" name="' . $id . '_rating" value="0" />';
			echo '</div>';
			if ($i < count($categories) - 1)
				echo "</li><li>";
			$i++;
		}
		echo "</li></ul>";
	
	}
	
	/* 
	 * Displays the HTML and JavaScript to collect star ratings within the comment form.
	 * Styled with a table.
	 */
	function ratings_input_table() {
	
		$categories = get_option('rs_categories');

		$i = 0;
		echo '<table class="ratings"><tr>';
		foreach ($categories as $id => $cat) {
			echo '<td class="rating_label">' . $cat . '</td>';
			echo '<td class="rating_value">';
			echo '<a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_1" title="1" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_2" title="2" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_3" title="3" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_4" title="4" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <a onclick="rateIt(this, ' . $id . ')" id="' . $id . '_5" title="5" onmouseover="rating(this, ' . $id . ')" onmouseout="rolloff(this, ' . $id . ')"></a>
                  <input type="hidden" id="' . $id . '_rating" name="' . $id . '_rating" value="0" />';
			echo '</td>';
			if ($i < count($categories) - 1)
				echo "</tr><tr>";
			$i++;
		}
		echo "</tr></table>";	
	
	}
	
	/** Utility Functions **/
	
	function round_to_half($num = 0) {
		return floor($num * 2) / 2;
	}
	
	function num_to_stars($num) {
	
		$stars = round_to_half($num);
		$num = round($num, 2);
	
		$html = "";
		for ($i = 0; $i < floor($stars); $i++)
			$html .= '<img src="' . WP_PLUGIN_URL . '/review-site/star.gif" alt="' . $num . '" />';

		if (floor($stars) != $stars)
			$html .= '<img src="' . WP_PLUGIN_URL . '/review-site/star-half.gif" alt="' . $num . '" />';
	
		if (ceil($stars) < 5)
			for ($i = ceil($stars); $i < 5; $i++)
				$html .= '<img src="' . WP_PLUGIN_URL . '/review-site/star-empty.gif" alt="' . $num . '" />';
		
		return $html;
	}
		
	function rs_fields($content) {
		global $wpdb;
		$content .= ", (SUM(" . $wpdb->ratings . ".rating_value) / COUNT(" . $wpdb->ratings . ".rating_id)) AS `rs_rating`, ";
		$content .= "(COUNT(" . $wpdb->comments . ".comment_ID) / (COUNT(" . $wpdb->comments . ".comment_ID) + 10)) * ";
		$content .= "(SUM(" . $wpdb->ratings . ".rating_value) / COUNT(" . $wpdb->ratings . ".rating_id)) ";
		$content .= "+ (5 / (COUNT(" . $wpdb->comments . ".comment_ID) + 10)) * 3 AS `rs_weighted`";
		return $content;
	}
	
	function rs_join($content) {
		global $wpdb;
		$content .= " LEFT OUTER JOIN " . $wpdb->comments . " ON " . $wpdb->posts . ".ID = " . $wpdb->comments . ".comment_post_ID "
					. "AND " . $wpdb->comments . ".comment_approved = 1 "
					. "LEFT OUTER JOIN " . $wpdb->ratings . " ON " . $wpdb->comments . ".comment_ID = " . $wpdb->ratings . ".comment_id ";
		return $content;
	}
		
	function rs_groupby($content) {
		global $wpdb;
		if (!empty($content))
			return $content . ", " . $wpdb->posts . ".ID";
		return $wpdb->posts . ".ID";
	}
	
	function rs_orderby($content) {
		return "`rs_weighted` DESC";
	}
	
	function rs_config_page() {
		if (function_exists('add_submenu_page')) {
			add_submenu_page('options-general.php', 'WP Review Site', 'WP Review Site', 'manage_options', 'review-site-config', 'rs_conf');
			add_submenu_page('options-general.php', 'WP Review Site', 'Affiliate Links', 'manage_options', 'review-site-aff-config', 'rs_aff_conf');
		}
	}
	
	function rs_conf() {
		include('review-site-config.php');
	}
	
	function rs_aff_conf() {
		include('review-site-aff.php');
	}
	
	function rs_css() {
		echo "<!-- WP Review Site CSS -->\n";
		$css = get_option('rs_css');
		echo '<style type="text/css">' . "\n";
		if (!empty($css)) echo $css . "\n\n";
		
		$plugin_url = WP_PLUGIN_URL;
		
		$css = <<<EOD
.rating_value a {
	background: url($plugin_url/review-site/star-empty.gif) no-repeat;
	width: 12px;
	height: 12px;
	display: block;
	float: left;
}

.rating_value .on {
	background: url($plugin_url/review-site/star.gif) no-repeat;
}
EOD;

		echo $css;
		echo "\n</style>\n";
	}
	
	function rs_comment_posted($comment_ID, $status = null) {
		global $wpdb;		
		$categories = get_option('rs_categories');
		
		foreach ($categories as $id => $cat) {
			if (isset($_POST[$id . '_rating']) && $_POST[$id . '_rating'] > 0 && $_POST[$id . '_rating'] <= 5) {
				$query = "INSERT INTO " . $wpdb->ratings . " (comment_id, rating_id, rating_value) VALUES (" . $comment_ID . ", " . $id . ", " . $_POST[$id . '_rating'] . ")";
				$wpdb->query($query);
			}
		}
		
	}

	function rs_comment_text($content) {
		ob_start();
		comment_ratings_table();
		$table = ob_get_contents();
		ob_end_clean();
		
		return $content . "<br />" . $table;
	}
	
	function rs_affiliate_markup($content) {
		$keywords = get_option('rs_keywords');
		if (!empty($keywords)) {
			foreach ($keywords as $id => $arr) {
				$keyword = $arr[0];
				$url = $arr[1];
				$content = str_replace($keyword, "<a href=\"" . $url . "\">" . $keyword . "</a>", $content);	
			}
		}
		return $content;
	}
	
	//Creates database table and sets default option values
	register_activation_hook(__FILE__, 'rs_install');
	function rs_install() {
	
		global $wpdb;
		
		if(@is_file(ABSPATH.'/wp-admin/upgrade-functions.php')) {
			include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
		} elseif(@is_file(ABSPATH.'/wp-admin/includes/upgrade.php')) {
			include_once(ABSPATH.'/wp-admin/includes/upgrade.php');
		} else {
			die('Problem finding \'/wp-admin/upgrade-functions.php\' and \'/wp-admin/includes/upgrade.php\'');
		}
		
		update_option('rs_reorder', true);
		
		$categories = get_option('rs_categories');
		if (empty($categories))
			update_option('rs_categories', array(0 => 'Overall Rating'));
		
		$css = <<<EOD
table.ratings {
	margin: 0;
	padding: 0;
	border: 0;
	border-collapse: collapse;
}

ul.ratings {
	margin: 0;
	padding: 0;
}

ul.ratings li {
	display: inline;
	list-style: none;
}

.rating_label {
	white-space: nowrap;
	background: #eee;
	font-family: Arial;
	font-size: 8pt;
	padding: 1px 4px;
}

.rating_value {
	white-space: nowrap;
	padding: 1px 3px;
	font-family: Arial;
	font-size: 8pt;
}

.rating_value .no_ratings {
	color: #666;
}
EOD;
		
		update_option('rs_css', $css);
		
		$create_table_sql = "CREATE TABLE " . $wpdb->ratings . " (".
				"comment_id INT, ".
				"rating_id INT, ".
				"rating_value DOUBLE, ".
				"PRIMARY KEY (comment_id, rating_id))";
		maybe_create_table($wpdb->ratings, $create_table_sql);
		
		//Check for WP Review Site 1.1 Tables and Upgrade
		$table_name = $wpdb->prefix . 'dgrs_cats';
    	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    	
			//Restore rating categories
	
    		$query = "SELECT * FROM $table_name ORDER BY display_order";
    		$result = $wpdb->get_results($query);
    		$categories = array();
    		$scale_max = 0;
			foreach ($result as $row) {
				$categories[$row->id] = $row->cat;
				$scale_max = $row->scale_max;
			}
			
			update_option('rs_categories', $categories);
			
			//Restore rating values
			
			$table_name = $wpdb->prefix . 'dgrs_ratings';

			$query = "INSERT IGNORE INTO " . $wpdb->ratings . " (comment_id, rating_id, rating_value) ";
			$query .= "SELECT comment_id, rating_cat_id, ((rating_value / $scale_max) * 5) FROM $table_name";

			$wpdb->query($query);			
    		
    	}
	
	}
		
?>
