<?php
/*
Plugin Name: Post ReTweet
Plugin URI: http://archais.me/post-retweet
Description: Plug in to add a contribution box of twitter to last of the online posting text
Author: Yuji Yamabata
Version: 0.1
Author URI: http://archais.me/post-retweet

    Copyright 2010 Yuji Yamabata (email : yamabata@archais.me)

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

if ( ! defined( 'WPPRT_PLUGIN_BASENAME' ) )
	define( 'WPPRT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WPPRT_PLUGIN_NAME' ) )
	define( 'WPPRT_PLUGIN_NAME', trim( dirname( WPPRT_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'WPPRT_PLUGIN_DIR' ) )
	define( 'WPPRT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPPRT_PLUGIN_NAME );

require_once(dirname(__FILE__).'/post-retweet-admin.php');

/**
* twitter contribution box to the last of the article
*
* the_content filter
*
* @access public
* @params string $content
* @return string the online posting text
*/
function view_tweet_box( $content ){

	if ( ! get_option( 'wpprt_twitter_api_key' ) )  return $content;

	if ( ! check_valid_page() ) return $content;
	//if ( ! is_single() ) return $content;

	$tweetbox_label           =  get_option( 'wpprt_tweetbox_label' );
	$tweetbox_default_content =  get_option( 'wpprt_tweetbox_default_content' );
	$tweetbox_hash_tag        =  get_option( 'wpprt_tweetbox_hash_tag' );

	$content_title = get_the_title();
	$content_link  = get_permalink();

	if ( get_option( 'wpprt_bitly_login' ) && get_option( 'wpprt_birly_appkey' ) ) {
		$login  = get_option( 'wpprt_bitly_login' );
		$appkey = get_option( 'wpprt_birly_appkey' );
		$content_link = get_short_url( $content_link, $login, $appkey );
	}

	$tweet_code = file_get_contents( WPPRT_PLUGIN_DIR . '/template/tweetbox.html' );
	$tweet_code = preg_replace( '/#\{tweetbox_label\}/', $tweetbox_label , $tweet_code );
	
	$tweetbox_content = '';
	$tweetbox_content .= ( $tweetbox_default_content != '' ) ? ' '. $tweetbox_default_content : '';
	$tweetbox_content .= ( $content_title != '' ) ?            ' '. $content_title            : '';
	$tweetbox_content .= ( $content_link != '' ) ?             ' '. $content_link             : '';
	$tweetbox_content .= ( $tweetbox_hash_tag != '' ) ?        ' '. $tweetbox_hash_tag        : '';

	$tweet_code = preg_replace( '/#\{tweetbox_content\}/', $tweetbox_content, $tweet_code );

	$content .= '<div id="view_tweet_box">';
	$content .= $tweet_code;
	$content .= '</div>';

	return $content;

}

add_filter( 'the_content', 'view_tweet_box' );

if ( get_option( 'wpprt_twitter_api_key' ) ) {
	$twitter_api_key = get_option( 'wpprt_twitter_api_key' );
	wp_enqueue_script( 'twitterapi', 'http://platform.twitter.com/anywhere.js?id=' . $twitter_api_key . '&v=1', array(), false );
	wp_register_style( 'twitterapi', get_bloginfo( 'url' ) . '/wp-content/plugins/post-retweet/css/style.css', array(), '', 'all' );
	wp_enqueue_style ( 'twitterapi' );
}

/**
* get short url
*
* use bit.ly api
*
* @access public
* @params string $url
* @return string short url
*/
function get_short_url( $url, $login, $appkey ) {
	$short_url = rtrim( get_bitly_short_url( $url, $login, $appkey ) );
	if ( preg_match( '/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $short_url ) )
		return $short_url;
	else
		$url;
}

/* returns the shortened url */
function get_bitly_short_url( $url, $login, $appkey, $format='txt' ) {
	$connectURL = 'http://api.bit.ly/v3/shorten?login=' . $login . '&apiKey=' . $appkey . '&uri=' . urlencode( $url ) . '&format='.$format;
	return curl_get_result( $connectURL );
}

/* returns a result form url */
function curl_get_result($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	$data = curl_exec( $ch );
	curl_close( $ch );
	return $data;
}

/**
* check valid page
*
* 
*
* @access public
* @params 
* @return bool true/false
*/
function check_valid_page() {
	if( is_front_page() && ! get_option( 'wpprt_is_front_page' ) ) return FALSE;

	if( is_single()   && get_option( 'wpprt_is_single'   ) ) return TRUE;
	if( is_page()     && get_option( 'wpprt_is_page'     ) ) return TRUE;
	if( is_category() && get_option( 'wpprt_is_category' ) ) return TRUE;
	if( is_tag()      && get_option( 'wpprt_is_tag'      ) ) return TRUE;
	if( is_author()   && get_option( 'wpprt_is_author'   ) ) return TRUE;
	if( is_date()     && get_option( 'wpprt_is_date'     ) ) return TRUE;
	if( is_search()   && get_option( 'wpprt_is_search'   ) ) return TRUE;
	if( is_404()      && get_option( 'wpprt_is_404'      ) ) return TRUE;

	return FALSE;
}

?>