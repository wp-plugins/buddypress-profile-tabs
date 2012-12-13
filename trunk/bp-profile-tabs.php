<?php
/*
Plugin Name: BuddyPress Profile Tabs
Plugin URI: http://jacobschweitzer.com/
Description: Makes the profile groups BuddyPress provides into tabs to better organize and consolidate the profile page. 
Version: 1.1
Author: Jacob Schweitzer
Author URI: http://jacobschweitzer.com/
License: GPL2
*/
function bp_profile_tabs_actions() {
	add_options_page("BP Profile Tabs", "BP Profile Tabs", 'manage_options', "BPProfileTabs", "bp_profile_tabs_admin");
}
add_action('admin_menu', 'bp_profile_tabs_actions');
		
	
		
function bp_profile_tabs_admin() {
$jquery_theme_option = get_option("bp_profile_tabs_theme_option");
	?>
	<h2>BuddyPress Profile Tabs Admin</h2>
	<form id="bp_profile_tabs_theme_form" class="bp_profile_tabs_theme_form_class">
		<select name="bp_profile_theme_option">
			<option value="base">Base</option>
			<option value="black-tie">Black Tie</option>
			<option value="blitzer">Blitzer</option>
			<option value="cupertino">Cupertino</option>
			<option value="dark-hive">Dark Hive</option>
			<option value="dot-luv">Dot Luv</option>
			<option value="eggplant">Eggplant</option>
			<option value="excite-bike">Excite Bike</option>
			<option value="flick">Flick</option>
			<option value="hot-sneaks">Hot Sneaks</option>
			<option value="humanity">Humanity</option>
			<option value="le-frog">Le Frog</option>
			<option value="mint-choc">Mint Choc</option>
			<option value="overcast">Overcast</option>
			<option value="pepper-grinder">Pepper Grinder</option>
			<option value="redmond">Redmond</option>
			<option value="smoothness">Smoothness</option>
			<option value="south-street">South Street</option>
			<option value="start">Start</option>
			<option value="sunny">Sunny</option>
			<option value="swanky-purse">Swanky Purse</option>
			<option value="trontastic">Trontastic</option>
			<option value="ui-darkness">Ui Darkness</option>
			<option value="ui-lightness">Ui lightness</option>
			<option value="vader">Vader</option>
		</select>
		<input type="submit" value="Submit" id="submit">
	</form>
	<div id="bp_profile_tabs_change_result">
		<span></span>
	</div>
	<script type="text/javascript">		
		jQuery(document).ready(function($) {
			$("#bp_profile_tabs_theme_form select").val("<?php echo $jquery_theme_option; ?>");
			$("form#bp_profile_tabs_theme_form input#submit").click(function(){
				var themeoption = $("#bp_profile_tabs_theme_form select").val();
				var data = {
					action: 'bp_profile_tabs_ajax_save',
					themeoption: themeoption
				};
				$.post(ajaxurl, data, function(response) {
					$("div#bp_profile_tabs_change_result span").replaceWith("<span>"+response+"</span>");
					$("div#bp_profile_tabs_change_result span").contents().fadeOut(3000);
				});
			return false;
			});
		});
	</script>
			<?php
}
		
function bp_profile_tabs_ajax_save() {
	$data = $_POST['themeoption'];
	update_option("bp_profile_tabs_theme_option", $data);
	echo '<p style="font-size:24px;color:#00FF00;">Theme Updated</p>';
	die();
}
add_action('wp_ajax_bp_profile_tabs_ajax_save', 'bp_profile_tabs_ajax_save');

function bp_profile_tabs_top() {
	echo '<div id="tabs"><ul id="bp-profile-tabs-list"></ul>';
}
add_action('bp_before_profile_loop_content','bp_profile_tabs_top');


function bp_profile_tabs_div() {
?>
<div id="tabs-<?php global $looper;$looper++;echo $looper; ?>">
<script type="text/javascript">
(function($){
	$("div#tabs ul#bp-profile-tabs-list").append("<li><a href='#tabs-<?php echo $looper; ?>'><?php
 bp_the_profile_group_name(); ?></a>");
 }(jQuery));
</script>
<?php
}
add_action('bp_before_profile_field_content','bp_profile_tabs_div');


function bp_profile_tabs_end_content() { echo "</div>"; }
add_action('bp_after_profile_field_content','bp_profile_tabs_end_content');

function bp_profile_tabs_end() { echo "</div>"; }
add_action('bp_after_profile_loop_content','bp_profile_tabs_end',10);


function bp_profile_tabs() {
	$jquery_theme_option = get_option("bp_profile_tabs_theme_option");
	echo '<link rel="stylesheet" href="';
	if ( $jquery_theme_option != '' ) {
		echo 'http://jquery-ui.googlecode.com/svn/tags/latest/themes/'.$jquery_theme_option.'/jquery-ui.css" />';
	}
	else {
		echo 'http://jquery-ui.googlecode.com/svn/tags/latest/themes/cupertino/jquery-ui.css" />';
	}

    wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	?>
	
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			$( "#tabs" ).tabs();
		});
    </script>
	<?php
}
add_action('bp_after_profile_loop_content','bp_profile_tabs',11);

?>