<?php get_header(); ?>

<div class="page">

	<div class="post">

	<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>

		<div class="title-item" id="post-<?php the_ID(); ?>">
<?php the_title(); ?>
		</div>

		<div class="entry">

			<?php the_content(); ?>

			<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>

			<!-- <?php trackback_rdf(); ?> -->

		</div>

		<div class="comments-template">
			<?php comments_template(); ?>
		</div>

	<?php endwhile; ?>

	<?php else : ?>

	<div class="title-item">
<?php _e('404 Error&#58; Not Found'); ?>
	</div>

	<?php endif; ?>

	</div>

</div>

<?php get_footer(); ?>
