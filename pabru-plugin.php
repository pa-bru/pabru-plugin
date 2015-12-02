<?php
/*
Plugin name: pabru plugin 
Description: Le fameux plugin de pabru ! web developper de l'espace !
Version: 0.1
Author: P-A BRU
Author URI: http://www.pa-bru.com/

*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//test en cours : filter + fonction translate
add_filter('the_content', 'my_function');
function my_function($content){
	return str_replace('test', 'coucou', $content);
}

add_action( 'plugins_loaded', 'my_plugin_load_plugin_textdomain' );
function my_plugin_load_plugin_textdomain() {
    load_plugin_textdomain( 'my-plugin', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}



//start plugin : 

	//requires :
		require( plugin_dir_path( __FILE__ ) . 'inc/cpt.inc.php');

	//create metaboxes :
		add_action('add_meta_boxes', 'pabru_meta_boxes');
		//add metas :
		function pabru_meta_boxes(){
			add_meta_box('pabru_meta_box_address', 'Address', 'pabru_meta_box_address', 'pabru_map', 'normal', 'high');
		}
		//meta boxes :
		function pabru_meta_box_address($post){
			$address =  esc_attr(get_post_meta($post->ID,'_pabru_address',true));
			?>
				<div class="meta-box-item-title">
					<h4>The shortcode to paste in the post you want</h4>
				</div>

				<div class="meta-box-item-content">
					<input style="width:100%" type="text" disabled="disabled" name="pabru_shortcode" id="pabru_shortcode" value="<?php echo '[pabru_map_shortcode id='. $post->ID . ']'; ?>"/>
				</div>

				<div class="meta-box-item-title">
					<h4>Address to mark on the map</h4>
				</div>

				<div class="meta-box-item-content">
					<input style="width:100%" type="text" name="pabru_address" id="pabru_address" value="<?php echo $address;?>"/>
				</div>
			<?php
		}

	// save pabru meta box with update :
		add_action('save_post','save_pabru_metaboxes');
		
		function save_pabru_metaboxes($post_ID){
		  if(isset($_POST['pabru_address'])){
		    update_post_meta($post_ID,'_pabru_address', esc_html($_POST['pabru_address']));
		  }
		}

	// Enqueued script with localized data.
		add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);
		function add_defer_attribute($tag, $handle) {
			if ( 'google_map_api' !== $handle )
				return $tag;
			return str_replace( ' src', ' async defer src', $tag );
		}

		add_action( 'wp_enqueue_scripts', 'add_plugin_scripts' );
		function add_plugin_scripts(){
			wp_enqueue_script('pabru_charge_map', plugins_url( '/js/pabru_charge_map.js' , __FILE__ ), array(), false, true);
			wp_enqueue_script('google_map_api','https://maps.googleapis.com/maps/api/js?signed_in=true&callback=initMap' , array( 'pabru_charge_map' ), false, true);
		}
	// create shortcode :
		add_shortcode('pabru_map_shortcode', 'pabru_map_shortcode');

		function pabru_map_shortcode($atts){
			$pabru_map_post = get_post($atts['id']);
			if($pabru_map_post->post_type == 'pabru_map' || $pabru_map_post !== null ){

				$pabru_map_post_title = $pabru_map_post->post_title;
				$pabru_map_address = esc_attr(get_post_meta($atts['id'],'_pabru_address',true));

				//use php variables in js :
				// Localize the script with new data
				$variables_array = array('pabru_map_address' => $pabru_map_address);
				wp_localize_script( 'pabru_charge_map', 'pabru_map', $variables_array );

				?>
					<h2 class="entry-title"><?php echo $pabru_map_post_title; ?></h2>
					<div id="pabru_map" style="height: 400px;"></div>
				<?php
			}
		}
