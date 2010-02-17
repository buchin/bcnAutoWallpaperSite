<?php get_header(); ?>

<div class="page">

	<div class="post"><?php
	$okedehbanget = 1;
if ($okedehbanget == 1){ ?>

<?php
$linktopost = '';
$query = wp_specialchars(stripslashes($s), 1) ;
$feedurl = 'http://blogsearch.google.com/blogsearch_feeds?hl=en&q=' . str_replace(' ', '+', $query) . '&lr=&ie=utf-8&num=10&output=rss';
//begin simplepie
$feed = new SimplePie();
$feed->set_feed_url($feedurl);
$feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/simplepie-core/cache');
$feed->init();
$feed->handle_content_type();
?>
<p>
	<script type="text/javascript"><!--
google_ad_client = "pub-0127266326569641";
/* 468x15, masbuchin */
google_ad_slot = "6894667665";
google_ad_width = 468;
google_ad_height = 15;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></p><br/>
<?php }?><!-- google_ad_section_start -->
	<h2 class="highlight"><?php
if (is_search()){ echo wp_specialchars($s, 1);} else { ?><?php _e('Latest Entries'); }?></h2>
<?php
if (is_search()){ ?>

<p>
<script type="text/javascript"><!--
google_ad_client = "pub-0127266326569641";
/* 300x250, created 7/26/08 */
google_ad_slot = "8585679027";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></p>

<?php if ($okedehbanget == 1){

	/*
	Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
	*/
	
	foreach ($feed->get_items() as $item):
	?>
 

			<?php 
$description = str_replace('...', '', $item->get_description());
$descmini = strip_tags($description);

$linktopost .= '<a href="' . get_settings('home') . '/search/' . str_replace(' ', '+', strip_tags(htmlspecialchars(str_replace('<b>','',str_replace('</b>','',$item->get_title()))))) . '">' . str_replace('...', '', $item->get_title()) . '</a>'; 
$linktopost .= '<a href="' . $item->get_permalink() . '" rel="nofollow">[url]</a>, ';
?>
			<?php

$random = (rand()%9);
switch ($random) {
case 0:
    echo '<blockquote>' . $description . '</blockquote>';
    break;
case 1:
    echo '<p>' . $description . '</p>';
    break;
case 2:
echo '<div style="float: left"><script type="text/javascript"><!--
google_ad_client = "pub-0127266326569641";
google_alternate_color = "FFFFFF";
google_ad_width = 336;
google_ad_height = 280;
google_ad_format = "336x280_as";
google_ad_type = "text_image";
google_ad_channel ="";
google_color_border = "FFFFFF";
google_color_link = "0000ff";
google_color_bg = "FFFFFF";
google_color_text = "000000";
google_color_url = "000000";
//--></script>

<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>' . $description ;
default:
   echo '<p>' . $descmini . '</p>'; 
}
?>
		
 
	<?php endforeach; ?>	<!-- google_ad_section_end -->

<?php }}?>

<?php function post_style() {
	static $post_count;
	$post_count++;
		if ($post_count % 2) {
			echo "title-item";
		}
		else {
			echo "title-item-alt";
		}
}
?>

	<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>

	<div class="<?php post_style(); ?>" id="post-<?php the_ID(); ?>">
<span class="postdate"><?php the_time('m.d.y') ?></span> - <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
	</div>

	<?php endwhile; ?>

	<?php include (TEMPLATEPATH . '/browse.php'); ?>

	<?php else : ?>

	<div class="title-item">
<?php _e('404 Error&#58; Not Found'); ?>
	</div>

	<?php endif; ?>

<?php //if (is_search()){echo $linktopost;}?>

	</div>

</div>

<?php get_footer(); ?>