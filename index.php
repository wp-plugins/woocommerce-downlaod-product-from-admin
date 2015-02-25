<?php
/*
Plugin name: Woocommerce Downlaod Product From Admin
Plugin URI : http://wp-master.ir
Description: Easily download products from admin , by <a href="http://wp-master.ir">Wp master</a>
Author: wp-master.ir
Author URI: http://wp-master.ir
Version: 0.1
*/

/**
 * Check if WooCommerce is active
 **/
add_action( 'plugins_loaded', 'wdpfa_counter_widget_lang');
function wdpfa_counter_widget_lang()
{
  load_plugin_textdomain( 'wdpfa', false, dirname( plugin_basename( __FILE__ ) ));
}
__('Woocommerce Downlaod Product From Admin' , 'wdpfa');
__('Easily download products from admin , by <a href="http://wp-master.ir">Wp master</a>' , 'wdpfa');
	
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


	define('wdpfa_url' , plugin_dir_url(__FILE__ ));
	define('wdpfa_dir' , dirname(__FILE__).DIRECTORY_SEPARATOR);

    function wdpfa_get_dll_link($post_id){
    	global $wpdb;
    	$q = "select ID from {$wpdb->prefix}posts where post_parent=$post_id and post_type='attachment' AND guid like '%woocommerce_uploads%'"; //woocommerce_uploads
    	$r = $wpdb->get_results($q);
    	if($r){
    			$r2 = get_post_meta( $r[0]->ID, '_wp_attached_file', true );
    			if($r2){ 
    					$r3 =  ABSPATH .'wp-content'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$r2;
    					return $r3;
    				 }else{ 
    				 	return false;
    				 	 }
    	}
    	return false;

    }

    function wdpfa_fetch_file($url){
		 if(!function_exists('wp_get_current_user')) {
		    include(ABSPATH . "wp-includes/pluggable.php"); 
		}
    	if(!current_user_can('manage_options' )) return;
		$file_url = $url;
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
		readfile($file_url); // (dirty but worky)
    }

	
	function wdpfa_columns_head($defaults) {
	    $defaults['dll_file'] = __('Dll' , 'wdpfa');
	    return $defaults;
	}

	function wdpfa_columns_content($column_name, $post_ID) {
		$s= get_post_status( $post_ID );
		if($s != 'publish') return;
	    if ($column_name == 'dll_file') {
	    	if(wdpfa_get_dll_link($post_ID)){
	    	?>
	    	<form action="edit.php" method="post">
	    		<input class="wdpfa_post_id" type="hidden" name="post_ID" value="<?php echo $post_ID ?>">
	    		<button class="button-primary dll-link-btn"><?php _e('Dll' , 'wdpfa'); ?></button>
	    		<img class="ajax-loader" src="<?php echo get_bloginfo('siteurl' ).'/wp-admin/images/wpspin_light.gif'; ?>">
	    	</form>
	    	<?php
	    	}
	    }
	}

	add_filter('manage_product_posts_columns', 'wdpfa_columns_head');
	add_action('manage_product_posts_custom_column', 'wdpfa_columns_content', 10, 2);

	/*--------------- Admin scripts ---------------*/
	add_action('admin_enqueue_scripts' , 'wdpfa_scripts');
	function wdpfa_scripts($hook){
		if($hook == 'edit.php' Or $hook == 'toplevel_page_wdpfa_davarha'){
			wp_enqueue_script( 'wdpfa_admin_js', wdpfa_url.'admin.js', array('jquery'), '0.1', false );
			wp_enqueue_style( 'wdpfa_admin_css', wdpfa_url.'admin.css', false, '0.1', false );
		}
	}


	add_action('wp_ajax_wdpfa_dll_file' , 'wdpfa_ajax');
	function wdpfa_ajax(){
		if(!empty($_POST['post_id'])){
			$post_id = (int)$_POST['post_id'];
			wdpfa_fetch_file(wdpfa_get_dll_link($post_id));
			echo json_encode(array('ok' => 'yes' , 'txt' => 'ok'));
			die();
		}
	}


}
