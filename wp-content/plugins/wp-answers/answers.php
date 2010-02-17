<?php
/*
Plugin Name: Worpdress Answers!
Plugin URI: http://www.anieto2k.com
Description: Turn your Wordpress into a StackOverFlow or Yahoo! Answers with a voting system based on the sort of comments and better valued.
Author: Andrés Nieto
Version: 0.1c
Author URI: http://www.anieto2k.com

USAGE: 
	Activate and enjoy it :D
	
	You can use some options for completo your theme.
	
		- get_usermeta($user_ID, "karma");
			Return the user karma.
			
	And you can user a GET param for change comments sort. You can add links to change for use it.
	
		- ?sort=old
			Sort by older comments (Wordpress default)
			
		- ?sort=new
			Sort by the newer comments
			
*/

require_once('functions.php');
require_once('widget.php');

function SOF_set_initial_comment_karma($comment_id) {
	global $wpdb;
	$query = $wpdb->prepare("UPDATE $wpdb->comments SET comment_karma = 1 WHERE comment_ID = %d LIMIT 1", $comment_id);
	$wpdb->query($query);
	return $comment_id;	
}

function SOF_getIp(){
 	$ip = (isset( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))? $_SERVER ['HTTP_X_FORWARDED_FOR']:$_SERVER ['REMOTE_ADDR'];
	return ($ip == '::1')?"127.0.0.1":$ip;
	
}

function SOF_set_comment_karma(){
	global $wpdb, $current_user;
	
	if (!$_POST || !isset($_POST["commentID"]) || empty($_POST["commentID"])) return;
	$comment_id = $_POST["commentID"];

	// Cogemos datos del usuario
	get_currentuserinfo();
	$user_ID = $current_user->ID;
	if ($user_ID == 0 && get_option('SOF_register') == 'true') die("Debes registrarte para poder votar");
	else if ($user_ID == 0) $user_ID = str_replace(array('.', ':', ' '), "", SOF_getIp());

	$karma =get_usermeta($user_ID, "karma");

	// No puedes votarte a ti mismo.
	$sql = $wpdb->prepare("SELECT user_id FROM $wpdb->comments WHERE comment_ID = %d LIMIT 1", $comment_id);
	$commentUser = $wpdb->get_var($sql);
	if ($user_ID == (int)$commentUser) die("No te puede votar a ti mismo");
	
	// No puedes votar 2 veces
	if (get_usermeta($user_ID, "vote_".$comment_id) == true) die("No puedes votar dos veces el mismo comentario");
	
	// Añadimos voto
	$newKarma =  1; // + $karma;
	if ($_POST["vote"] === '+')
		$query = $wpdb->prepare("UPDATE $wpdb->comments SET comment_karma = comment_karma + $newKarma WHERE comment_ID = %d LIMIT 1", $comment_id);
	else if ($_POST["vote"] == '-')
		$query = $wpdb->prepare("UPDATE $wpdb->comments SET comment_karma = comment_karma - $newKarma WHERE comment_ID = %d LIMIT 1", $comment_id);
	else return;
	$wpdb->query($query);
	
	// KARMA COMMENT
	if ($commentUser) {
		$karmaCommentUser = get_usermeta($commentUser, "karma");
		if ($_POST["vote"] === '+')
			update_usermeta( $commentUser, "karma", ($karmaCommentUser + get_option('SOF_bonoOK')) );
		else if ($_POST["vote"] === '-')
			update_usermeta( $commentUser, "karma", ($karmaCommentUser + get_option('SOF_bonoKO')) );
	}
	
	// Karma Voto
	if ($user_ID){
		update_usermeta( $user_ID, "karma", ($karma + get_option('SOF_bonoVOTE')) );
		update_usermeta( $user_ID, "vote_".$comment_id, true );
	}

	wp_redirect('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

function SOF_show_buttons($text = ''){
	global $comment, $current_user;
	get_currentuserinfo();

	 // Comprobamos categoría
	$cat = get_option('SOF_category');
        if ($cat != -1)
                if (!in_category($cat)) return $text;

	if ($comment->comment_parent) return $text;
	$form = '
			 <div class="wp_answers_votes">
			 <span class="wp_answers_total_votes">'.$comment->comment_karma.'</span>';
	if (get_option('SOF_register') != 'true' || $current_user->ID != 0){
		$form .='	<form action="" method="post" class="wp_answers_votes_form">
					<input class="vote mas" type="submit" name="vote" value="+" />';
		$form .='<input class="vote menos" type="submit" name="vote" value="-" />';
			
		$form .='<input type="hidden" name="commentID" value="'.$comment->comment_ID.'" />
				</form>';
		}
	$form .= '</div>';
	return $form.$text;
}

function SOF_sort_comments_by_karma($comments = array(), $postID = 0){
	global $wpdb, $user_ID, $post;
	
	// Comprobamos categoría
	$cat = get_option('SOF_category');
	if ($cat != -1)
		if (!in_category($cat)) return $comments;
	
	$commenter = wp_get_current_commenter();
	extract($commenter, EXTR_SKIP);
	
	// Ordenación
	$sort = 'comment_karma DESC';
	if (isset($_GET["sort"]) && $_GET["sort"] == 'old') $sort = 'comment_date ASC';
	else if (isset($_GET["sort"]) && $_GET["sort"] == 'new')  $sort = 'comment_date DESC';
	
	if ( $user_ID) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY $sort", $post->ID, $user_ID));
	} else if ( empty($comment_author) ) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' ORDER BY $sort", $post->ID));
	} else {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY $sort", $post->ID, $comment_author, $comment_author_email));
	}
	return $comments;
}


// Action
add_action("comment_post", "SOF_set_initial_comment_karma");
add_action("init", "SOF_set_comment_karma");

// Filtros
add_filter("comments_array", "SOF_sort_comments_by_karma");
add_filter("comment_text", "SOF_show_buttons");
?>
