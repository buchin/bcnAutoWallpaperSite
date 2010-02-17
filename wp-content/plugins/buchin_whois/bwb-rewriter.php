<?php
/*
Plugin Name: Buchin's iPhone Wallpaper Rewrite Rule
Plugin URI: http://masbuchin.com
Description: Custom Rewrite Rule for iPhone.masbuchin.com
Version: 0.1
Author: Mochammad Masbuchin
Author URI: http://masbuchin.com
*/

/*
License:  GPL
Note:  This plugin was compiled after much anguish on Google, the final answer was
in a WP support topic... http://wordpress.org/support/topic/145456
Thanks to Gamerz & Zoom4 for putting the pieces together.

Modified from:
Plugin Name: BWB ReWriter
Plugin URI: http://www.whypad.com/posts/wordpress-plugin-url-rewrite/194/
Description: Plugin allows you to set up custom URL rewrite rule.
Version: 0.1
Author: Byron Bennett
Author URI: http://www.whypad.com
*/

// runs the function in the init hook
add_action('init', 'bwb_flush_rules');

add_action('generate_rewrite_rules', 'bwb_add_rewrite_rules');

add_filter('query_vars', 'bwb_query_vars');


//Flush rules so WP will recalculate rewrite rules
function bwb_flush_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();	
}

/*	Add your custom rules in the array below...the first part (the key, 
	to left of =>) is the regular expression to match, the second part 
	is the new value
*/
function bwb_add_rewrite_rules( $wp_rewrite ) 
{

	/*  Unsurprisingly by its name, this array contains your new rule.
		The array uses your Regular Express (the express tests the raw 
		URL for matches) as the key.  The value (the string to the right
		of the "=>" is the new URL.  In the function below, you will add 
		your parameter names to $public_query_vars[].
		Separate Key + Value pairs with a comma.  Do not put a comma after
		the last pair...that always gets me b/c I copy/paste a lot.
		
		
		Change the page (regions) to your page name, change/add/remove 
		variables and their corresponding regex matches "(.+)" to match 
		your needs
	*/	
		
	$new_rules = array(
		'wallpaper/(.*)' => 'index.php?pagename=wallpaper&keyword='. $wp_rewrite->preg_index(1)
	);
	
	//Add the rules to the rules array..wanna add them to the TOP, like so
	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}


function bwb_query_vars($public_query_vars) {
	/*	ADD YOUR PARAMETERS or QUERY VARIABLES BELOW
		Uncomment the lines and change the variable names to the ones 
		you want to use. Add more lines as needed
	*/
	
	$public_query_vars[] = "keyword";
	
	/*	Note: you do not want to add a variable multiple times.  As in
		the example above, multiple rules can use the same variables 
	*/
	
	return $public_query_vars;
}
?>