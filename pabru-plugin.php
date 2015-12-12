<?php
/*
Plugin name: pabru plugin 
Description: This plugin enables you to create and manage maps. You can also customize yours maps (location, title, content, context...).
Version: 1.0
Author: P-A BRU
Author URI: http://www.pa-bru.com/
*/

	
//blocking direct access to the plugin PHP files	
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//apply translation of the plugin :
	add_action( 'plugins_loaded', 'pabru_map_load_textdomain' );
	function pabru_map_load_textdomain() {
		load_plugin_textdomain('pabru-plugin', false, plugin_basename(dirname(__FILE__)) . '/languages');
	}

//globals :
	$description_maxlength = 100;
	$marker_title_maxlength = 50;
	$marker_content_maxlength = 255;


//requires :
	require( plugin_dir_path( __FILE__ ) . 'inc/cpt.inc.php');


//add meta box :
	add_action('add_meta_boxes', 'pabru_map_meta_boxes', 10, 2);
	function pabru_map_meta_boxes($post_type, $post){
		if('pabru_map' == $post_type){
			add_meta_box('pabru_map_meta_box', __( 'Informations of the map', 'pabru-plugin' ), 'pabru_map_meta_box', $post_type, 'normal', 'high');
		}
	}
//write meta box :
	function pabru_map_meta_box($post){
		$address =  get_post_meta($post->ID,'_pabru_map_address',true);
		$description =  get_post_meta($post->ID,'_pabru_map_description',true);
		$marker_title =  get_post_meta($post->ID,'_pabru_map_marker_title',true);
		$marker_content =  get_post_meta($post->ID,'_pabru_map_marker_content',true);
		$marker_link =  get_post_meta($post->ID,'_pabru_map_marker_link',true);
		global $description_maxlength;
		global $marker_title_maxlength;
		global $marker_content_maxlength;
		?>
			<div class="meta-box-item-title">
				<h4><?php _e('Shortcode to paste in the post you want', 'pabru-plugin'); ?></h4>
			</div>

			<div class="meta-box-item-content">
				<input style="width:100%" type="text" disabled="disabled" name="pabru_map_shortcode" id="pabru_map_shortcode" value="<?php echo '[pabru_map_shortcode id='. $post->ID . ']'; ?>"/>
			</div>

			<div class="meta-box-item-title">
				<h4><?php _e('Address to mark on the map', 'pabru-plugin'); ?></h4>
			</div>

			<div class="meta-box-item-content">
				<input style="width:100%" type="text" name="pabru_map_address" id="pabru_map_address" value="<?php echo $address;?>" required/>
			</div>

			<div class="meta-box-item-title">
				<h4>
					<?php 
					printf(esc_html__( 'The description you want : (%d characters max)', 'pabru-plugin' ), $description_maxlength);
					?>
				</h4>
			</div>

			<div class="meta-box-item-content">
				<input maxlength="<?php echo $description_maxlength;?>" style="width:100%" type="text" name="pabru_map_description" id="pabru_map_description" value="<?php echo $description;?>"/>
			</div>

			<div class="meta-box-item-title">
				<h4>
					<?php 
					printf(esc_html__( 'Title of the marker on the map : (%d characters max)', 'pabru-plugin' ), $marker_title_maxlength);
					?>
				</h4>
			</div>

			<div class="meta-box-item-content">
				<input maxlength="<?php echo $marker_title_maxlength;?>" style="width:100%" type="text" name="pabru_map_marker_title" id="pabru_map_marker_title" value="<?php echo $marker_title;?>"/>
			</div>

			<div class="meta-box-item-title">
				<h4>
					<?php 
					printf(esc_html__( 'Content text to put in the context box : (%d characters max)', 'pabru-plugin' ), $marker_content_maxlength);
					?>
				</h4>
			</div>

			<div class="meta-box-item-content">
				<textarea maxlength="<?php echo $marker_content_maxlength;?>" style="width:100%" name="pabru_map_marker_content" id="pabru_map_marker_content"><?php echo $marker_content; ?></textarea>
			</div>

			<div class="meta-box-item-title">
				<h4><?php _e('Link in the marker box', 'pabru-plugin'); ?></h4>
			</div>

			<div class="meta-box-item-content">
				<input style="width:100%" type="text" name="pabru_map_marker_link" id="pabru_map_marker_link" value="<?php echo $marker_link;?>"/>
			</div>
		<?php
		// Add a nonce field :
		wp_nonce_field( 'save_metabox_data', 'pabru_map_meta_box_nonce' );
	}

//save pabru_map meta box with update :
	add_action('save_post','save_pabru_map_metabox_data');
	function save_pabru_map_metabox_data($post_ID){
		//verify if nonce is valid  and if the request referred from an administration screen :
		if(!wp_verify_nonce($_POST['pabru_map_meta_box_nonce'], 'save_metabox_data' )){
			return $post_ID;
		}
		//just the address is necessary to display a map :
		if(!isset($_POST['pabru_map_address']) || empty($_POST['pabru_map_address'])){
			return $post_ID;
		}

		update_post_meta($post_ID,'_pabru_map_address', sanitize_text_field($_POST['pabru_map_address']));
		update_post_meta($post_ID,'_pabru_map_description', sanitize_text_field($_POST['pabru_map_description']));
		update_post_meta($post_ID,'_pabru_map_marker_title', sanitize_text_field($_POST['pabru_map_marker_title']));
		update_post_meta($post_ID,'_pabru_map_marker_content', esc_textarea($_POST['pabru_map_marker_content']));
		update_post_meta($post_ID,'_pabru_map_marker_link', esc_url($_POST['pabru_map_marker_link']));
	}

//Enqueued script with localized data.
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
		//globals :
			global $description_maxlength;
			global $marker_title_maxlength;
			global $marker_content_maxlength;
		
		//verifying if id parameter in shortcode is an int :
			$atts['id'] = intval($atts['id']);
			if ( !$atts['id'] )
				$atts['id'] = '';
		
		//verifying if post is a map and if it exists :
			$pabru_map_post = get_post($atts['id']);
			if(!$pabru_map_post->post_type == 'pabru_map' || $pabru_map_post === null ){
				return false;
			}
		
		//values of the pabru_map post : 
			$pabru_map_post_title = $pabru_map_post->post_title;
			$pabru_map_address = get_post_meta($atts['id'],'_pabru_map_address',true);

			$pabru_map_description = get_post_meta($atts['id'],'_pabru_map_description',true);
				if ( strlen( $pabru_map_description ) > $description_maxlength ){
					$pabru_map_description = substr( $pabru_map_description, 0, $description_maxlength );
				}
			
			$pabru_map_marker_title =  get_post_meta($atts['id'],'_pabru_map_marker_title',true);
				if ( strlen( $pabru_map_marker_title ) > $marker_title_maxlength ){
					$pabru_map_marker_title = substr( $pabru_map_marker_title, 0, $marker_title_maxlength );
				}

			$pabru_map_marker_content =  get_post_meta($atts['id'],'_pabru_map_marker_content',true);
				if ( strlen( $pabru_map_marker_content ) > $marker_content_maxlength ){
					$pabru_map_marker_content = substr( $pabru_map_marker_content, 0, $marker_content_maxlength );
				}
				
			$pabru_map_marker_link =  get_post_meta($atts['id'],'_pabru_map_marker_link',true);

		//Localize the script with new data (use php variables in js) :
			$variables_array = array(
				'pabru_map_id' => $atts['id'],
				'pabru_map_address' => $pabru_map_address,
				'pabru_map_marker_title' => $pabru_map_marker_title,
				'pabru_map_marker_content' => $pabru_map_marker_content,
				'pabru_map_marker_link' => $pabru_map_marker_link
			);
			wp_localize_script( 'pabru_charge_map', 'pabru_map', $variables_array );
		
		//Display the pabru_map post :
			$display_pabru_map = '<h2>'.$pabru_map_post_title.'</h2>'
								.'<div id="pabru_map_'.$atts['id'].'" style="height: 400px;"></div>'
								.'<p style="text-align:center;font-style:italic;">'.$pabru_map_description.'</p>';

			return $display_pabru_map;
	}
