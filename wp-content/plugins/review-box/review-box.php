<?php
/*
Plugin Name: Review Box
Version: 1.5
Plugin URI: http://www.paradoxdgn.com/archives/622
Description: Creates a box for reviewers, with pros, cons, and a score bar. Use <code>[review pros="pros" cons="cons"  score=85]</code> to insert. <em>score must be a percentage value, without the % sign</em>
Author: Paradox
Author URI: http://paradoxdgn.com
*/
/*  Copyright 2009  Paradox Designs  (email : paradox460@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function addCSS() {
	echo '<link type="text/css" rel="stylesheet" href="' . plugins_url ( plugin_basename ( dirname ( __FILE__ ) ) ) .'/review-box.css" />';

}
function review_prettify($atts) {
	extract( shortcode_atts( array(
		'title' => 'Review',
		'title_url' => '',
		'score' => '100',
		'pros' => '<em>None</em>',
		'cons' => '<em>None</em>',
		'verdict' => '',
		), $atts ));
	if ( $score > 100 ) {
		$score = 100;
	}
	elseif ( $score < 0 ) {
		$score = 0;
	}
	$width = 450;
	/*
	The width of the review box is controlled programattically this time, instead of via CSS. This should produce a better result. If you want to change this value, set it above. THIS MUST BE IN PIXEL (PX) VALUES.

In newer versions, this should be an admin page option
*/
	#title system.
	if ( $title == 'none' ) {
		$title_bar = '';
	} else {
		$title_bar = '<h2>'. do_shortcode($title) .'</h2>';
	}
	#Verdict box
	if ( $verdict == null ) {
		$verdict_box = '';
	} else {
		$verdict_box = '<tr><th>Verdict</th><td>'. $verdict .'</td></tr>';
	}
	$result = '<a name="review"></a><div class="review">
		'. $title_bar .'
		<div class="mainbox">
			<div class="procons">
	<table>
			<tr><th>Pros</th><th>Cons</th></tr>
			<tr><td>' . do_shortcode($pros) . '</td><td>' . do_shortcode($cons) . '</td></tr>
			' . $verdict_box . '
			</table>
			</div>
                        <table class="review_grid">
                                <tr><td class="review_label">Rating</td><td><div class="rating_bg" style="width: ' . $width . 'px"><span class="rating_bar" style="width: ' . $width * ($score/100) . 'px;"><span class="rating_bar_content">' . $score . '%</span></span></div></td></tr>
                        </table>
		</div></div>';
	return $result;

}

add_action('wp_head', 'addCSS');
add_shortcode('review','review_prettify');
?>
