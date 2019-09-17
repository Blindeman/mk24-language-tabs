<?php 
/*
 * Plugin Name: MK24 Meertalige tabbladen
 * Plugin URI: http://www.mk24.nl/
 * GitHub Plugin URI: https://github.com/Blindeman/mk24-language-tabs
 * Description: To add simple tabs to posts and pages for different languages. Comes with this shortcode: 
 * [ltabjes talen="Taal Language"][ltab taal="Taal"]
 * inhoud
 * [/ltab][ltab taal="Language"]
 * content
 * [/ltab][/ltabjes].
 * Version: 1.0
 * Author: Naomi Blindeman
 * Author URI: http://www.blindemanwebsites.com
*/

//preventing direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//AUB geen wijzigingen maken aan dit document op mk24.nl, zie hierboven voor github adres en neem contact op via naomi@blindeman.com zodat ik je toe kan voegen om wijzigingen te maken.

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
			
			$divs = "";
			foreach( $language as $lang ){
				$divs .= "<div id=\"".$lang."\">". do_shortcode( $content ) ."</div>";
			}

			return $divs;
		}//end function shortcode_taal
		
		public function shortcode_talen( $atts, $content = null ) {
			extract( shortcode_atts( array(
				'talen' => false
			), $atts ) );
			
			$this->_enqueue();

			$languages = array();
			
			if ( !empty( $talen ) ) {
				$languages = explode( ' ', esc_attr( $talen ) );
			}
						
			//here I do what I need with the shortcodes for each title		
			//add some styles
			$style ="<style type=\"text/css\">
				ul.tabjes{border-bottom:1px solid #DCDCDC;display:-webkit-box;display:-ms-flexbox;display:flex;margin:0 0 20px;padding:0}ul.tabjes li{height:30px;list-style-type:none;margin-bottom:0;margin-left:0;padding:0;width:auto}ul.tabjes li a{background-color:#EEE;border-color:#DCDCDC;border-style:solid;border-width:1px 1px 0 0;-webkit-box-sizing:content-box;box-sizing:content-box;color:#555;display:block;font-size:13px;height:29px;line-height:30px;padding:0 20px;text-decoration:none;width:auto}ul.tabjes li:first-child a{border-width:1px 1px 0}ul.tabjes li a.active{background-color:#FFF;border-left-width:1px;height:30px;margin:0 0 0 -1px;padding-top:4px;position:relative;top:-4px}
			</style>";

			//create a tab for each language, for each language a li
			$tabs= "";
			foreach( $languages as $language ){
				$tabs .= "<li><a href=\"#" . $language . "\">" . $language . "</a></li>"; 
			}
			//put those li's inside an ul
			$tabbar = "<ul class=\"tabjes\">" . $tabs . "</ul>";

			//and then for each language show content in a separate div
			$all = $style . $tabbar . do_shortcode( $content );
			
			return $all;
		} //end function shortcode
		
		
		protected function _enqueue(){
			// Define the URL path to the plugin...
			$plugin_path = plugin_dir_url( __FILE__ );
			
			 // Enqueue the styles if they are not already...
			 //I might want to use this after all, even if I don't like linking to stylesheet in the footer of the page, but this way I can check if the style has already been added
			 //in case someone wants to use the shortcode more than once per page
			/* if ( !wp_style_is( $this->tag, 'enqueued' ) ) {
				wp_enqueue_style(
					$this->tag,
					$plugin_path . 'ltab.css',
					array(),
					$this->version
				);
			} */// end check to see if styles have already been loaded
			
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

	//clean up the amount of extra paragraph tags added
	remove_filter( 'the_content', 'wpautop' );
	add_filter( 'the_content', 'wpautop' , 99);
	add_filter( 'the_content', 'shortcode_unautop',100 );
	
	new Ltab;
	
 } //end check if class Ltab exists

?>
