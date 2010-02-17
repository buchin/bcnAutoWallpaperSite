<div id="footer">
<p>
<?php
if(function_exists(bstat_todayrefs) && !is_home()){
echo 'recenlty searched: ';
bstat_todayrefs(20,'',' ');
echo '<br/>random posts: ';
random_posts(10, 30, '', ', ', '', '</p><br/>',false, false); 
echo '<br/>popular today: ';
bstat_todaypop(20, '', ' ');

}
?>
</p>
<ul class="menu">
<li><a href="http://masbuchin.com">Proxy</a></li>
<?php wp_list_pages('title_li='); ?>
</ul>

<p style="clear:both;"><?php bloginfo('name'); ?> <?php _e('is proudly powered by <a href="http://www.wordpress.org/" title="WordPress" rel="nofollow">WordPress</a> and <a href="http://www.wpdesigner.com" title="WordPress Themes" rel="nofollow">WPDesigner</a>.'); ?></p>
<?php wp_footer(); ?>

</div></div>

<div style="text-align:center;"><?php if(!is_home()){?>

<?php } ?></div>

</div>


</body>
</html>