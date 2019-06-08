<?php 
/*
 * Plugin Name: MK24 Meertalige tabbladen
 * Plugin URI: http://www.mk24.nl/
 * GitHub Plugin URI: https://github.com/Blindeman/mk24-language-tabs
 * Description: To add simple tabs for different languages. Komt met de shortcode: [ltabjes talen="Taal Language"][ltab taal="Taal"]inhoud[/ltab][ltab taal="Language"]content[/ltab][/ltabjes].
 * Version: 0.2
 * Author: Naomi Blindeman
 * Author URI: http://www.blindemanwebsites.com
*/

//preventing direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Ltab' ) ) {
	class Ltab {
		/**
		 * Tag identifier used by file includes and selector attributes.
		 * @var string
		 */
		protected $tag = 'ltab';
	
		/**
		 * Userfriendly name used to identify the plugin.
		 * @var string
		 */
		protected $name = 'Ltab';
	
		/**
		 * Current version of the plugin.
		 * @var string
		 */
		protected $version = '0.1';
		
		public function __construct() {
			/**
			 * add shortcode
			 */
			add_shortcode( $this->tag.'jes', array( &$this, 'shortcode_talen' ) );
			add_shortcode( $this->tag, array( $this, 'shortcode_taal' ) );
		} //end function __construct()
		
		public function shortcode_taal( $atts, $content = null ){
			
			extract( shortcode_atts( array(
				'taal' => false
			), $atts ) );
			
			$language = array();
			
			if( !empty( $taal ) ){
				$language[] = esc_attr( $taal );
			}
			
			ob_start();
			
			foreach( $language as $lang ){
				echo "<div id=\"".$lang."\">".do_shortcode( $content )."</div>";
			}
			
			return ob_get_clean();			
		
		}//end function shortcode_taal
		
		public function shortcode_talen( $atts, $content = null ) {
			extract( shortcode_atts( array(
				'talen' => false
			), $atts ) );
			
			if( is_single() ){
				// Enqueue the required styles and scripts...
				$this->_enqueue();	
			}

			$languages = array();
			
			if ( !empty( $talen ) ) {
				$languages = explode( ' ', esc_attr( $talen ) );
			}
			
			if( is_single() ){
				ob_start();
				
				//here I do what I need with the shortcodes for each title		
				//create a tab for each language ?>
				<ul class="tabjes"><?php
					//for each language a li
					foreach( $languages as $language ){
						echo "<li><a href=\"#".$language."\">".$language."</a></li>";
					}
					?>
				</ul><?php
				//and then for each language show content in a separate div
				echo do_shortcode( $content );
				
				return ob_get_clean();
				
			}//end check if single
		} //end function shortcode
		
		
		protected function _enqueue(){
			// Define the URL path to the plugin...
			$plugin_path = plugin_dir_url( __FILE__ );
			
			 // Enqueue the styles if they are not already...
			if ( !wp_style_is( $this->tag, 'enqueued' ) ) {
				wp_enqueue_style(
					$this->tag,
					$plugin_path . 'ltab.css',
					array(),
					$this->version
				);
			}// end check to see if styles have already been loaded
			
			//Enqueue scrip if it hasn't been loaded yet
			if ( !wp_script_is( $this->tag, 'enqueued' ) ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script(
					'jquery-' . $this->tag,
					$plugin_path . 'ltab.js',
					array( 'jquery' ),
					'0.1' // Current version of the Plugin.
				);
			}//end check to see if js has already been loaded

		}// end function _enqueue()
		
	} //end class Ltab
	
	new Ltab;
	
 } //end check if class Ltab exists

?>