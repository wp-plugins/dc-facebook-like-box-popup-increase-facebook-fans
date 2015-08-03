<?php
/*
 Contributors: dattardwp-21
 Donate link: http://www.dart-creations.com/joomla-25-and-joomla-3-modules/75-donate-a-beer
 Tags: wordpress, facebook, like, popin, responsive
 Requires at least: 3.0.1
 Tested up to: 4.3
 Stable tag: 4.3
 License: GPLv2 or later
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
add_action( 'wp_enqueue_scripts', 'wp_facebook_popup_wp_enqueue_scripts' );
function wp_facebook_popup_wp_enqueue_scripts() {
	wp_enqueue_script('jQuery');
}

add_action('init', 'wp_facebook_popup_init');
function wp_facebook_popup_init() {
	global $hidefbpopup;
	$hidefbpopup= false;
}

add_shortcode('hidefbpopup', 'wp_facebook_popup_shortcode_hidefbpopup');
function wp_facebook_popup_shortcode_hidefbpopup() {
	global $hidefbpopup;
	if(is_singular()) {	
		$hidefbpopup= true;
	}
}

add_action('wp_footer', 'wp_facebook_popup_show');
function wp_facebook_popup_show() {		
	global $hidefbpopup;
	$data = get_option('wp_facebook_popup_settings');
	if(isset($data['enable_disable_plugin']) && (!$hidefbpopup)) {
		$displayPopup = false;
		
		/*Begin Where to Show*/
		if((is_home() || is_front_page()) && isset($data['show_in_home'])) { //Show in Home Page
			$displayPopup = true;
		} else if(is_single() && isset($data['show_in_post'])) { //Show in Single Post
			$displayPopup = true;
		} else if(is_page() && isset($data['show_in_page'])) { //Show in Single Page
			$displayPopup = true;
		} else {
			if(isset($data['show_everywhere'])) {
				$displayPopup = true;
			}
		}
		/*End Where to Show*/
		
		/*Begin When to Show*/
		if(is_user_logged_in()) {
			if(isset($data['show_loggedin_users'])) {
				$displayPopup = true;
			} else {
				$displayPopup = false;
			}
		} else {
			if(isset($data['show_loggedout_users'])) {
				$displayPopup = true;
			} else {
				$displayPopup = false;
			}
		}
		/*End When to Show*/
		if($displayPopup) {
			echo '<script type="text/javascript" src="'.WP_FACEBOOK_POPUP_URL.'/js/jquery.colorbox-min.js"></script>';
			echo '<link rel="stylesheet" href="'.WP_FACEBOOK_POPUP_URL.'/css/style.css" />';
			echo '<script type="text/javascript">'."\r\n";
			echo 'jQuery(document).ready(function() {'."\r\n";
				echo 'if('.((isset($data['appear_always']))?'1':'document.cookie.indexOf("visited=true") == -1').') {'."\r\n";
					echo 'var expires = new Date((new Date()).valueOf() + 1000*60*60*24*'.(isset($data['days_until_popup_shows_again'])?$data['days_until_popup_shows_again']:'1').');'."\r\n";
					echo 'document.cookie = "visited=true;expires=" + expires.toUTCString();'."\r\n";
					echo 'setTimeout(function() {'."\r\n";
						echo 'jQuery.colorbox({width:"400px", inline:true, href:"#subscribe", '.((isset($data['lock_scroll']))?'onOpen: function() { jQuery("body").css("overflow", "hidden"); }, onClosed: function() { jQuery("body").css("overflow", "auto"); }':'').'});'."\r\n";
					echo '}, '.(isset($data['seconds_popup_appear'])?$data['seconds_popup_appear']:'0').' * 1000);'."\r\n";					
				echo '}'."\r\n";
			echo '});'."\r\n";
			echo '</script>'."\r\n";
			echo '<div style="display:none">';
				echo '<div id="subscribe" style="padding: 10px; background: #fff;">';
					echo '<h3 class="box-title">'.(isset($data['title_text'])?$data['title_text']:'Follow us on Facebook!').'</h3>';
					echo '<center>';
						echo '<iframe src="//www.facebook.com/plugins/likebox.php?href='.(isset($data['facebook_fanpage_url'])?$data['facebook_fanpage_url']:'https://www.facebook.com/DARTCreationsFans').'&amp;width=300&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23ffffff&amp;stream='.(isset($data['show_post'])?'true':'false').'&amp;header=false&amp;height=258" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:270px;" allowtransparency="true"></iframe>';
					echo '</center>';
					if(isset($data['show_author_link'])) {
						echo '<div align=right>';
							echo '<a href="http://www.dart-creations.com/" style="font-size:8px;">Wordpress Facebook Like Popup</a>';
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
		}
	}
}
?>