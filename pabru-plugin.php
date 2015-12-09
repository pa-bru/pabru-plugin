<?php
/*
Plugin name: pabru plugin 
Description: Le fameux plugin de pabru ! web developper de l'espace !
Version: 0.1
Author: P-A BRU
Author URI: http://www.pa-bru.com/

*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

load_plugin_textdomain('pabru-plugin', false, plugin_basename(dirname(__FILE__)) . '/languages');

//test en cours : filter + fonction translate
//add_filter('the_content', 'my_function');
//function my_function($content){
//	return str_replace('test', 'coucou', $content);
//}

//start plugin : 

	//requires :
		require( plugin_dir_path( __FILE__ ) . 'inc/cpt.inc.php');

	//create metaboxes :
		//add metas :
			add_action('add_meta_boxes', 'pabru_meta_boxes');
			function pabru_meta_boxes(){
				add_meta_box('pabru_meta_box_address', 'Address', 'pabru_meta_box_address', 'pabru_map', 'advanced', 'high');
				add_meta_box('pabru_meta_box_marker', 'Marker of the map', 'pabru_meta_box_marker', 'pabru_map', 'side', 'low');
			}
		//meta boxes :
			function pabru_meta_box_address($post){
				$address =  esc_attr(get_post_meta($post->ID,'_pabru_map_address',true));
				$description =  esc_attr(get_post_meta($post->ID,'_pabru_map_description',true));
				?>
					<div class="meta-box-item-title">
						<h4><?php  _e('The shortcode to paste in the post you want', 'pabru-plugin'); ?></h4>
					</div>

					<div class="meta-box-item-content">
						<input style="width:100%" type="text" disabled="disabled" name="pabru_map_shortcode" id="pabru_map_shortcode" value="<?php echo '[pabru_map_shortcode id='. $post->ID . ']'; ?>"/>
					</div>

					<div class="meta-box-item-title">
						<h4><?php _e('Address to mark on the map', 'pabru-plugin'); ?></h4>
					</div>

					<div class="meta-box-item-content">
						<input style="width:100%" type="text" name="pabru_map_address" id="pabru_map_address" value="<?php echo $address;?>"/>
					</div>

					<div class="meta-box-item-title">
						<h4><?php _e('The description you want ', 'pabru-plugin'); ?></h4>
					</div>

					<div class="meta-box-item-content">
						<input style="width:100%" type="text" name="pabru_map_description" id="pabru_map_description" value="<?php echo $description;?>"/>
					</div>
				<?php
			}

			function pabru_meta_box_marker($post){
				$marker_title =  esc_attr(get_post_meta($post->ID,'_pabru_map_marker_title',true));
				$marker_content =  esc_attr(get_post_meta($post->ID,'_pabru_map_marker_content',true));
				$marker_link =  esc_attr(get_post_meta($post->ID,'_pabru_map_marker_link',true));
				?>
					<div class="meta-box-item-title">
						<h4><?php _e('Title of the marker on the map', 'pabru-plugin'); ?></h4>
					</div>

					<div class="meta-box-item-content">
						<input style="width:100%" type="text" name="pabru_map_marker_title" id="pabru_map_marker_title" value="<?php echo $marker_title;?>"/>
					</div>

					<div class="meta-box-item-title">
						<h4><?php _e('The content text in the marker box ', 'pabru-plugin'); ?></h4>
					</div>

					<div class="meta-box-item-content">
						<input style="width:100%" type="text" name="pabru_map_marker_content" id="pabru_map_marker_content" value="<?php echo $marker_content;?>"/>
					</div>

					<div class="meta-box-item-title">
						<h4><?php _e('The link in the marker box ', 'pabru-plugin'); ?></h4>
					</div>

					<div class="meta-box-item-content">
						<input style="width:100%" type="text" name="pabru_map_marker_link" id="pabru_map_marker_link" value="<?php echo $marker_link;?>"/>
					</div>
				<?php
			}

		//save pabru meta box with update :
			add_action('save_post','save_pabru_metaboxes');

			function save_pabru_metaboxes($post_ID){
				if(isset($_POST['pabru_map_address'])){
					update_post_meta($post_ID,'_pabru_map_address', esc_html($_POST['pabru_map_address']));
				}
				if(isset($_POST['pabru_map_description'])){
					update_post_meta($post_ID,'_pabru_map_description', esc_html($_POST['pabru_map_description']));
				}

				if(isset($_POST['pabru_map_marker_title'])){
					update_post_meta($post_ID,'_pabru_map_marker_title', esc_html($_POST['pabru_map_marker_title']));
				}
				if(isset($_POST['pabru_map_marker_content'])){
					update_post_meta($post_ID,'_pabru_map_marker_content', esc_html($_POST['pabru_map_marker_content']));
				}
				if(isset($_POST['pabru_map_marker_link'])){
					update_post_meta($post_ID,'_pabru_map_marker_link', esc_html($_POST['pabru_map_marker_link']));
				}
			}


	// Enqueued script with localized data.
		//add async and defer for google map api link :
			add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);
			function add_defer_attribute($tag, $handle) {
				if ( 'google_map_api' !== $handle )
					return $tag;
				return str_replace( ' src', ' async defer src', $tag );
			}
		//add the scripts :
			add_action( 'wp_enqueue_scripts', 'add_plugin_scripts' );
			function add_plugin_scripts(){
				wp_enqueue_script('pabru_charge_map', plugins_url( '/js/pabru_charge_map.js' , __FILE__ ), array(), false, true);
				wp_enqueue_script('google_map_api','https://maps.googleapis.com/maps/api/js?signed_in=true&callback=initMap' , array( 'pabru_charge_map' ), false, true);
			}


	// create shortcode :
		add_shortcode('pabru_map_shortcode', 'pabru_map_shortcode');

		function pabru_map_shortcode($atts){
			$pabru_map_post = get_post($atts['id']);

			if(!$pabru_map_post->post_type == 'pabru_map' || $pabru_map_post === null ){
				return false;
			}

			$pabru_map_post_title = $pabru_map_post->post_title;
			$pabru_map_address = esc_attr(get_post_meta($atts['id'],'_pabru_map_address',true));
			$pabru_map_description = esc_attr(get_post_meta($atts['id'],'_pabru_map_description',true));
			$pabru_map_marker_title =  esc_attr(get_post_meta($atts['id'],'_pabru_map_marker_title',true));
			$pabru_map_marker_content =  esc_attr(get_post_meta($atts['id'],'_pabru_map_marker_content',true));
			$pabru_map_marker_link =  esc_attr(get_post_meta($atts['id'],'_pabru_map_marker_link',true));

			//use php variables in js :
			// Localize the script with new data
			$variables_array = array(
				'pabru_map_id' => $atts['id'],
				'pabru_map_address' => $pabru_map_address,
				'pabru_map_marker_title' => $pabru_map_marker_title,
				'pabru_map_marker_content' => $pabru_map_marker_content,
				'pabru_map_marker_link' => $pabru_map_marker_link
			);
			wp_localize_script( 'pabru_charge_map', 'pabru_map', $variables_array );
			?>
				<h2><?php echo $pabru_map_post_title; ?></h2>
				<div id="pabru_map_<?php echo $atts['id']; ?>" style="height: 400px;"></div>
				<p style="text-align:center;font-style:italic;">
					<?php echo $pabru_map_description; ?>
				</p>
			<?php
		}
