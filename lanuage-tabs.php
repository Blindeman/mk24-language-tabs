<?php 
/*
 * Plugin Name: MK24 Meertalige tabbladen
 * Plugin URI: http://www.mk24.nl/
 * GitHub Plugin URI: https://github.com/Blindeman/mk24-language-tabs
 * Description: To add simple tabs to posts and pages for different languages. Comes with this shortcode: [ltabjes talen="Taal Language"][ltab taal="Taal"]inhoud[/ltab][ltab taal="Language"]content[/ltab][/ltabjes]. See README.md for more info on implementation.
 * Version: 1.1
 * Author: Naomi Blindeman
 * Author URI: http://www.blindemanwebsites.com
*/

//preventing direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//AUB geen wijzigingen maken aan dit document op mk24.nl, zie hierboven voor github adres en neem contact op via naomi@blindeman.com zodat ik je toe kan voegen om wijzigingen te maken.

//with credit for this function to Drew Baker: https://stackoverflow.com/questions/13510131/remove-empty-p-tags-from-wordpress-shortcodes-via-a-php-functon#answer-49019912
if( !function_exists( 'custom_filter_shortcode_text' ) ){
	function custom_filter_shortcode_text( $text = '' ) {

		// Replace all the poorly formatted P tags that WP adds by default.
		$tags = array("<p>", "</p>");
		$text = str_replace($tags, "\n", $text);

		// Remove any BR tags
		$tags = array("<br>", "<br/>", "<br />");
		$text = str_replace($tags, "", $text);

		// Add back in the P and BR tags, remove empty ones
		return apply_filters('the_content', $text);

	} //end function custom_filter_shortcode_text
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
			
			$divs = "";

			//filter out extra paragraphs and linebreaks
			$content = custom_filter_shortcode_text( $content );

			foreach( $language as $lang ){
				//using do_shortcode in case there are more shortcodes in the text
				$divs .= "<div id=\"".$lang."\">". do_shortcode( $content ) ."</div>";
			}

			//the tabs don't work well on archive pages because some of the formatting is cut off in summaries
			if( is_singular() ){
				return $divs;
			} else {
				return do_shortcode( $content );
			}
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

			//create a tab for each language
			$tabs= "";

			foreach( $languages as $language ){
				$tabs .= "<li><a href=\"#" . $language . "\">" . $language . "</a></li>"; 
			}

			//put those tabs inside a tabbar
			$tabbar = "<ul class=\"tabjes\">" . $tabs . "</ul>";

			//filter out extra paragraphs and linebreaks
			$content = custom_filter_shortcode_text( $content );

			//and then for each language show content in a separate div
			$all = $style . $tabbar . do_shortcode( $content );
			
			//tabbar isn't needed on archive pages
			if( is_singular ){
				return $all;
			} else {
				do_shortcode( $content );
			}
		} //end function shortcode
		
		
		protected function _enqueue(){
			// Define the URL path to the plugin...
			$plugin_path = plugin_dir_url( __FILE__ );
			
			 // Enqueue the styles if they are not already...
			 //I might want to use this after all, even if I don't like linking to a stylesheet in the footer of the page, this way I can check if the style has already been added
			 //in case someone wants to use the shortcode more than once per page
			/* if ( !wp_style_is( $this->tag, 'enqueued' ) ) {
				wp_enqueue_style(
					$this->tag,
					$plugin_path . 'ltab.css',
					array(),
					$this->version
				);
			} */// end check to see if styles have already been loaded
			
			//Enqueue script if it hasn't been loaded yet
			if ( !wp_script_is( $this->tag, 'enqueued' ) ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script(
					'jquery-' . $this->tag,
					$plugin_path . 'ltab.js',
					array( 'jquery' ),
					'1.0' // Current version of the Plugin.
				);
			}//end check to see if js has already been loaded

		}// end function _enqueue()
		
	} //end class Ltab
	
	new Ltab;
	
 } //end check if class Ltab exists

?>
