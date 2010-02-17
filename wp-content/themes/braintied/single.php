<?php get_header(); ?>

<div class="page">

	<div class="post">

	<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
<? if ($country!='Indonesia' ) {?>

<p><script type="text/javascript"><!--
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

<?php }?>
		<div class="title-item" id="post-<?php the_ID(); ?>">
<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		</div>

		<div class="entry"><? if ($country!='Indonesia' ) { ?>
<p><script type="text/javascript"><!--
google_ad_client = "pub-0127266326569641";
/* 300x250, masbuchin */
google_ad_slot = "4856166736";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<script type="text/javascript" src="http://player.jambovideonetwork.com/js/player.php?pubsite_id=7092&pr=7092"></script>
</p>

<?php }?>

			<?php the_content(); ?>

			<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>

			<p class="postinfo">
<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a> is <?php _e('posted on'); ?> <span class="postdate"><?php the_time('F jS, Y') ?></span> <?php _e('by'); ?> <?php the_author() ?>. This post is <?php _e('filed under&#58;'); ?> <?php the_category(', '); the_tags(', ') ?> <?php edit_post_link('Edit', ' &#124; ', ''); ?>.

<?php
if(is_single()){
if (function_exists(bstat_refsforpost)){
echo '<p>Some people come to this post with this search term: ';
global $post;
$thePostID = $post->ID;
bstat_refsforpost($thePostID, "", ", ");
echo '</p>';
}
if (function_exists(similar_posts)){
echo '<p>And here is the related entries of this post: ';
similar_posts();
echo '</p>';
}

}

?>
			</p>

			<!-- <?php trackback_rdf(); ?> -->

		</div>

		<div class="comments-template">
			<?php comments_template(); ?>
		</div>

	<?php endwhile; ?>

	<?php include (TEMPLATEPATH . '/browse.php'); ?>

	<?php else : ?>

	<div class="title-item">
<?php _e('404 Error&#58; Not Found'); ?>
	</div>

	<?php endif; ?>

	</div>

</div>

<?php get_footer(); ?>
