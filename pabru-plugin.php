<?php

/*
Plugin name: pabru plugin 
Description: Le fameux plugin de pabru ! web developper de l'espace !
Version: 0.1
Author: P-A BRU
Author URI: http://www.pa-bru.com/

*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_filter('the_content', 'my_function');
function my_function($content){
	return str_replace('test', 'coucou', $content);
}
