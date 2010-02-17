<?php
/*
Template Name: Wallpaper View for iPhone.masbuchin.com
Author: Mochammad Masbuchin
URL: http://masbuchin.com
*/


?><?php get_header(); ?>

<div class="page">

	<div class="post">

	<?php if(have_posts()) : ?>

		<h2 class="title-item" id="post-<?php the_ID(); ?>">
<?php echo ucwords(str_replace('-',' ',clearQuery(get_query_var('keyword'))));?>
		</h2>

		<div class="entry">

			<?php the_content(); ?>
			<?php
			$keyword = str_replace('-',',',clearQuery(get_query_var('keyword')));
			$url = "http://api.flickr.com/services/feeds/photos_public.gne?tags=" . $keyword . "&tagmode=all&format=rss2";
			$result = generateAutoContentCustom($url);
			/*echo $keyword . "<br/>";*/
			$viewcnt = 1;
			foreach ($result as $item){
				//if ($viewcnt == 1){
				$viewcnt++;
				$count = 1;
				$title = $item->get_title();
				if(stristr($title, "iPhone Wallpaper")=== FALSE){
					$title .= " iPhone Wallpaper";
				}
				foreach($item->get_enclosures() as $enc){
					if ($count == 1){
					///echo  '<h3  class="title-item"><a href="/wallpaper/' . str_replace('+', '-',clearTitle(clearQuery(str_ireplace("wallpaper","", str_ireplace("iPhone","", $title))))) . '-iphone-wallpaper/">' . $title . "</a></h3>";
					echo  '<h3  class="title-item">' . $title . "</h3>";
					$count++;
						foreach ($enc->get_credits() as $cred){
							$name = $cred->get_name();
							echo '<p><img src="' .$enc->get_link() . '" alt="' . $title . '" align="left"/>';
							echo 'Uploader: <a href="http://iphone.masbuchin.com/wallpaper/' . str_replace('+', '-',clearTitle(clearQuery($name))) . '-iphone-wallpaper/">' . $name . "</a>\n";
						}
					echo "<br/>Description: This is an iPhone Wallpaper uploaded by ". $name . ' with given title "' . $title . '". The original description from this author is ' . clearDescription($enc->get_description()) . ". Click the link below to see this wallpaper in actual size. You may download " . $title . " by click the link below, after the image displayed in full size:</p>
					<blockquote><p>How To Install Wallpaper on Your iPhone:</p>
<p>1. In Safari, go to the screen that gives you the option for &#8216;medium&#8217; or &#8216;original image&#8217; sizes. Click &#8216;Original Size&#8217;</p>

<p>2. Tap and hold your finger on the image. When you see a pop-up box, choose &#8216;Save Image&#8217;</p>
<p>3. Go to your iPhone&#8217;s Camera Roll, choose the photo, tap the Action icon, then tap &#8216;Use As Wallpaper&#8217;</p>			
					";
					echo  '<a href="' . $enc->get_link() . '">View and Download ' . $title . "</a></blockquote><br/><br/>";
					echo 'Another iPhone Wallpapers Tagged With: ';
					$exp_title = explode(' ', $title); 
					foreach($exp_title as $tag){
						if(stristr($tag, "iphone")===FALSE && stristr($tag, "wallpaper")===FALSE){
							echo '<a href="/wallpaper/' . str_replace('+', '-',clearTitle(clearQuery(str_ireplace("wallpaper","", str_ireplace("iPhone","", $tag))))) . '-iphone-wallpaper/">' . $tag . "</a> ";
						}
					}
					}
					
				}
				//echo  '<h3  class="title-item"><a href="/wallpaper/' . str_replace('+', '-',clearTitle(clearQuery(str_ireplace("---","-",str_ireplace("wallpaper","", str_ireplace("iPhone","", $title)))))) . '-iphone-wallpaper/">' . $title . "</a></h3>";
			//}
			/*else{
				$cnt = 1;
				while($cnt==1){
					$cnt++;
					$title = $item->get_title();
					echo  '<h3  class="title-item"><a href="/wallpaper/' . str_replace('+', '-',clearTitle(clearQuery($title))) . '-iphone-wallpaper/">' . $title . "</a></h3>";
				}
			}*/
				
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
