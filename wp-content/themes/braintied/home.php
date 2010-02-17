<?php
/*
Template Name: Home for iPhone.masbuchin.com
Author: Mochammad Masbuchin
URL: http://masbuchin.com
*/


?><?php get_header(); ?>

<div class="page">

	<div class="post">

	<?php if(have_posts()) : ?>

		<h2 class="title-item" id="post-<?php the_ID(); ?>">
<?php the_title(); ?>
		</h2>

		<div class="entry">

			<?php the_content(); ?>
			<?php
			$keyword = "iphone+wallpaper";//str_replace('+',',',clearQuery(get_query_var('keyword')));
			$url = "http://api.flickr.com/services/feeds/photos_public.gne?tags=iphone,wallpaper&tagmode=all&format=rss2";
			$result = generateAutoContentCustom($url);
			/*echo $keyword . "<br/>";*/
			foreach ($result as $item){
				$count = 1;
				$title = $item->get_title();
				if(str_word_count($title, 0)<=3){
					if(stristr($title, "iPhone Wallpaper")=== FALSE){
						$title .= " iPhone Wallpaper";
					}
				foreach($item->get_enclosures() as $enc){
					if ($count == 1){
					$count++;
						foreach ($enc->get_credits() as $cred){
							$name = $cred->get_name();
							echo '<p><img src="' . $enc->get_thumbnail() . '" alt="' . $title . '" align="left"/>';
							echo "Uploader: " . $name . "\n";
						}
					echo "<br/>Desc: " . clearDescription($enc->get_description()) . "</p>";
					}
				}
			echo  '<h3  class="title-item"><a href="/wallpaper/' . str_replace('--', '-',clearTitle(clearQuery(str_ireplace("wallpaper","", str_ireplace("iPhone","", $title))))) . 'iphone-wallpaper/">' . $title . "</a></h3>";
			}
			}
			?>

			<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>

			<!-- <?php trackback_rdf(); ?> -->

		</div>


	<?php else : ?>

	<div class="title-item">
<?php _e('404 Error&#58; Not Found'); ?>
	</div>

	<?php endif; ?>

	</div>

</div>

<?php get_footer(); ?>
