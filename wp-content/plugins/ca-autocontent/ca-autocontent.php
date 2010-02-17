<?php
/*
Plugin Name: CA-AutoContent
Plugin URI: http://panduandasar.com/
Description: Plugin untuk men-generate autocontent. Spesial untuk pelanggan SEO Complete Guide for Wordpress
Version: 0.2
Author: Cosa Aranda
Author URI: http://cosaaranda.com/
*/

/*
-= Version History =-

v0.2:
- function generateAutoContentCustom()
- parameter forcelimit @ function outputAutoContent

v0.1:
- initial release

-= Upcoming Features =-

- search/replace words in description
- more feeds
- custom feeds
- default template
- more flexible template (before + after feed)
- user interface?

/*/

//daftar karakter di title yang dibuang
$caactkb = array('<b>','...',':','&amp;','!',',','<','>','(',')','[',']','|','/','\\');

//daftar karakter di title yang diganti spasi 
$caactks = array('   ','.','---', 'wallpaper','iphone');

//daftar karakter di description yang dibuang
$caacdkb = array('...', '&lt;p&gt;', '&lt;/p&gt;');

//daftar situs yang diblokir
$caacblokir = array();

//clear query
function clearQuery($query) {
  return str_replace(' ', '-', wp_specialchars(stripslashes($query)));
}

//generate feed google
function googleFeed($query) {
  return 'http://blogsearch.google.com/blogsearch_feeds?hl=en&q=' . clearQuery($query) . '&lr=&ie=utf-8&num=10&output=rss';
}

//generate feed bing
function bingFeed($query) { 
  return 'http://www.bing.com/search?q=' . clearQuery($query) . '&go=&form=QBLH&filt=all&format=rss';
}

//generate feed yahoo
function yahooFeed($query) {
  return 'http://api.search.yahoo.com/WebSearchService/rss/webSearch.xml?appid=yahoosearchwebrss&query=' . clearQuery($query) . '&adult_ok=1';
}

//clear feed description - remove unwanted chars
function clearDescription($dd) {
  global $caacdkb;
  foreach ($caacdkb as $c):
    $dd = str_replace($c, '', $dd);
  endforeach;
  return $dd;
}

//clear feed title - remove/replace unwanted chars
function clearTitle($vurl) {
  global $caactkb;
  global $caactks;
  $vurl = strtolower(htmlspecialchars(strip_tags($vurl)));
  foreach ($caactkb as $c):
    $vurl = str_replace($c, '', $vurl);
  endforeach;
  foreach ($caactks as $c):
    $vurl = str_ireplace($c, ' ', $vurl);
  endforeach;
  return $vurl;
}

//transform keyword into query
function clearKeyword($surl) {
  $surl = str_replace(' ', '+', $surl);
  $surl = str_replace('++', '+', $surl);  
  return $surl;
}

//generate permalink -- currently not working
function generatePermalink($vurl) {
  return get_settings('home') . '/search/' . $vurl;
}

//check blocked domain
function isBlokir($permalink) {
  global $caacblokir;  
  if (empty($caacblokir)) return false;
  foreach ($caacblokir as $d):
    if (strpos($permalink,$d)===FALSE) { } else return true;
  endforeach;
  return false;
}

//generate autocontent array for custom feed
function generateAutoContentCustom($feed_url, $limit = 5, $duration = 36000, $cache = '/wp-content/cache') {
  //prepare simplepie
  $feed = new SimplePie();
  $feed->set_feed_url($feed_url);
  $feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . $cache);
  $feed->set_item_limit($limit);
  $feed->set_cache_duration($duration);
  $feed->init();
  $feed->handle_content_type();
  $ac = array();
  //generate
  /*foreach ($feed->get_items() as $item):
  	//print_r($item);
  	$enclosure = $item->get_enclosures();
    $fSource = $item->get_permalink();
    if (isBlokir($fSource)) continue;
    $fDescription = clearDescription($item->get_description());
    $fKeyword = clearTitle($item->get_title());
    $fPermalink = clearKeyword($fKeyword);
    //$fPermalink = generatePermalink($Permalink);
    $ac[] = array("keyword" => ucwords($fKeyword), "permalink" => $fPermalink, "description" => $fDescription, "source" => $fSource, "enclosure"=>$enclosure);
  endforeach; */
  return $feed->get_items();
}

//generate autocontent array
function generateAutoContent($query, $google = 1, $yahoo = 1, $bing = 1, $limit = 5, $duration = 7776000, $cache = '/wp-content/cache') {
  //prepare simplepie
  $feed = new SimplePie();
  $f = $google + $yahoo + $bing;
  if ($f == 1) {
    if ($google) $feed->set_feed_url(googleFeed($query));
    else if ($yahoo) $feed->set_feed_url(yahooFeed($query));
    else if ($bing) $feed->set_feed_url(bingFeed($query));
  } else {
    if ($google && $yahoo && $bing) $feed->set_feed_url(array(googleFeed($query), yahooFeed($query), bingFeed($query)));
    else if ($google && $yahoo) $feed->set_feed_url(array(googleFeed($query), yahooFeed($query)));
    else if ($google && $bing) $feed->set_feed_url(array(googleFeed($query), bingFeed($query)));
    else if ($yahoo && $bing) $feed->set_feed_url(array(yahooFeed($query), bingFeed($query)));
  }
  $feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . $cache);
  $feed->set_item_limit($limit);
  $feed->set_cache_duration($duration);
  $feed->init();
  $feed->handle_content_type();
  $ac = array();
  //generate
  foreach ($feed->get_items() as $item):
    $fSource = $item->get_permalink();
    if (isBlokir($fSource)) continue;
    $fDescription = clearDescription($item->get_description());
    $fKeyword = clearTitle($item->get_title());
    $fPermalink = clearKeyword($fKeyword);
    //$fPermalink = generatePermalink($Permalink);
    $ac[] = array("keyword" => ucwords($fKeyword)	, "permalink" => $fPermalink, "description" => $fDescription, "source" => $fSource);
  endforeach;
  return $ac;
}

//generate output based on template
function outputAutoContent($gac, $template, $forcelimit = 0) {
  $output = '';
  $ii = 0;
  foreach ($gac as $item):
    $t = $template;
    $t = str_replace('[[keyword]]', $item["keyword"], $t);
    $t = str_replace('[[permalink]]', $item["permalink"], $t);
    $t = str_replace('[[description]]', $item["description"], $t);
    $t = str_replace('[[source]]', $item["source"], $t);
    $output .= $t;
    $ii++;
    if ($ii == $forcelimit) break;
  endforeach; 
  return $output;
}

?>