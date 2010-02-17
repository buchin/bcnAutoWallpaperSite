<?php
//require_once('/home/buchin/masbuchin.com/Geo_IP/stat_functions.php');
//Get User countery
$country = 'US';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">


	<title><?php if(is_page('wallpaper')){ echo ucwords(str_replace('-',' ',clearQuery(get_query_var('keyword')))); echo " | ";}?><?php if(!is_page('wallpaper')){wp_title();} ?> <?php bloginfo('name'); ?></title>

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>

</head>
<body>

<div id=EchoTopic>
<div id="container">

<div id="header">
	<div class="site-title"><h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?> <?php _e('home page'); ?>"><?php bloginfo('name'); ?></a></h1></div>
	<div class="topmenu">

	</div>
</div>