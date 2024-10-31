<?php

add_action('admin_menu', 'wpprt_add_page');

/**
* Post ReTweet admin menu
*
*
*
* @access public
* @params 
* @return 
*/
function wpprt_add_page() {
	add_menu_page( 'Post ReTweet', 'Post ReTweet', 8, __FILE__, 'wpprt_options_page' );
}

/**
* Post ReTweet admin page
*
*
*
* @access public
* @params 
* @return 
*/
function wpprt_options_page() {

	$hidden_field_name = 'wpprt_submit_hidden';

	$opt_twitter = array( 
								 'wpprt_twitter_api_key'           => 'Twitter Api Key'
								,'wpprt_tweetbox_label'            => 'Tweetbox Label'
								,'wpprt_tweetbox_default_content'  => 'Tweetbox Default Content'
								,'wpprt_tweetbox_hash_tag'         => 'Tweetbox Hash Tag'
							);

	$opt_bit = array( 
								 'wpprt_bitly_login'               => 'Bit.ly Login'
								,'wpprt_birly_appkey'              => 'Bit.ly AppKey'
							);

	$opt_conditional = array( 
														 'wpprt_is_front_page'   => 'is_front_page'
														,'wpprt_is_single'       => 'is_single'
														,'wpprt_is_page'         => 'is_page'
														,'wpprt_is_category'     => 'is_category'
														,'wpprt_is_tag'          => 'is_tag'
														,'wpprt_is_author'       => 'is_author'
														,'wpprt_is_date'         => 'is_date'
														,'wpprt_is_search'       => 'is_search'
														,'wpprt_is_404'          => 'is_404'
													);

	if ( $_POST[ $hidden_field_name ] == 'Y' ) {
		foreach ( $opt_twitter as $key => $value ) {
			update_option( $key, $_POST[ $key ] );
		}
		foreach ( $opt_bit as $key => $value ) {
			update_option( $key, $_POST[ $key ] );
		}
		foreach ( $opt_conditional as $key => $value ) {
			if ( $_POST[$key] ) update_option( $key, $value );
			else                update_option( $key, '' );
		}

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'wpprt_trans_domain' ); ?></strong></p></div>
<?php

	}

	$opt_input = array();
	foreach ( $opt_twitter as $key => $value ) {
		$opt_input[$key] = get_option( $key );
	}
	foreach ( $opt_bit as $key => $value ) {
		$opt_input[$key] = get_option( $key );
	}

	echo '<div class="wrap">';
	echo "<h2>" . __( 'Post ReTweet Plugin Options', 'wpprt_trans_domain' ) . "</h2>";

?>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<h3><?php _e( 'twitter info', 'wpprt_trans_domain' ); ?></h3>
<table class="form-table">
<?php foreach ( $opt_twitter as $key => $value ) { ?>
	<tr>
		<th><label><?php _e( $value, 'wpprt_trans_domain' ) . ":"; ?></label></th>
		<td><input type="text" name="<?php echo $key; ?>" value="<?php echo $opt_input[$key]; ?>" size="40"></td>
	</tr>
<?php } ?>
</table>

<h3><?php _e( 'bit.ly info', 'wpprt_trans_domain' ); ?></h3>
<table class="form-table">
<?php foreach ( $opt_bit as $key => $value ) { ?>
	<tr>
		<th><label><?php _e( $value, 'wpprt_trans_domain' ) . ":"; ?></label></th>
		<td><input type="text" name="<?php echo $key; ?>" value="<?php echo $opt_input[$key]; ?>" size="40"></td>
	</tr>
<?php } ?>
</table>

<h3><?php _e( 'wrodpress conditional', 'wpprt_trans_domain' ); ?></h3>
<table class="form-table">
	<tr>
		<th><label><?php _e( 'conditional', 'wpprt_trans_domain' ) . ":"; ?></label></th>
		<td>
			<?php foreach ( $opt_conditional as $key => $value ) { ?>
			<input type="checkbox" name="<?php echo $key; ?>" value="<?php echo $value; ?>" <?php checked( ( get_option( $key ) ) ? TRUE : FALSE ); ?> /><?php _e( $value , 'wpprt_trans_domain' ) ?><br />
			<?php } ?>
		</td>
	</tr>
</table>

<p class="submit">
<input type="submit" name="submit" value="<?php _e('Update Options', 'wpprt_trans_domain' ) ?>" />
</p>

</form>
</div>

<?php
 
}
