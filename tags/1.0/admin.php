<?php
/*
 Plugin Name: Convert Data
 Plugin URI: http://www.islam.com.kw
 Description: This plugin produce for you code is array of categories and posts.
 Version: 1.0
 Author: EDC Team (E-Da`wah Committee)
 Author URI: http://www.islam.com.kw
 License: It is Free -_-
*/
include(plugin_dir_path( __FILE__ )."functions/categories.php");
include(plugin_dir_path( __FILE__ )."functions/posts_ids.php");
include(plugin_dir_path( __FILE__ )."functions/posts_info.php");

function convert_data_install(){
	add_option( 'convert_data_categories_exclude', '1', '', 'yes' ); 
	add_option( 'convert_data_view_just_parent', '0', '', 'yes' );
	add_option( 'convert_data_type', '0', '', 'yes' );
}
register_activation_hook(__FILE__,'convert_data_install'); 

function convert_data_admin_style() {
	wp_register_style( 'convert-data-styles', plugin_dir_url( __FILE__ ).'style.css' );
	wp_enqueue_style( 'convert-data-styles' );
	
}
add_action('wp_enqueue_scripts', 'convert_data_admin_style');

function convert_data_add_style() {
	echo "<style type=\"text/css\" media=\"screen\">\n";
	echo ".convert_data { margin:0; padding:10px; background-color:#ffffff; border:0px solid #cccccc; }";
	do_action('convert_data_css');
	echo "</style>\n";
}
add_action('admin_head','convert_data_add_style');

add_action('admin_menu', 'convert_data_menu');
function convert_data_menu() {
	add_menu_page( 'Convert Data', 'Convert Data', 'manage_options', 'convert-data', 'convert_data_options', ''.trailingslashit(plugins_url(null,__FILE__)).'/i/convert_data.png' );
}

function convert_data_filter($text) {
$code = stripslashes($text);
//$code = htmlspecialchars($code);
$code = str_replace("'","&#39;",$code);
$code = str_replace("&","&amp;",$code);
$code = str_replace('"',"&quot;",$code);
return trim($code);
}

function convert_data_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

if(isset($_POST['submitted']) && $_POST['submitted'] == 1){
    if(preg_match('/^[0-9, ]+$/', strip_tags($_POST['convert_data_categories_exclude']))) {
		update_option( 'convert_data_categories_exclude', strip_tags($_POST['convert_data_categories_exclude']) );
    }else{
    	echo '<div id="message" class="error"><p><strong>Error</strong> Exclude categories is not match</p></div>';
    }
	
	update_option( 'convert_data_view_just_parent', intval($_POST['convert_data_view_just_parent']) );
	update_option( 'convert_data_type', intval($_POST['convert_data_type']) );
}
$categories_exclude = strip_tags(get_option('convert_data_categories_exclude'));
$view_just_parent = intval(get_option('convert_data_view_just_parent'));
$data_type = intval(get_option('convert_data_type'));
?>

<div class="wrap">
	<h2>Convert Data</h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<form name="sytform" action="" method="post">
				<input type="hidden" name="submitted" value="1" />

					<div class="stuffbox">
					<h3><label for="convert_data_get_categories">All categories:</label></h3>
					<div class="inside">
					<div><textarea id="convert_data_get_categories" rows="31" cols="30" style="width:100%;"><?php echo convert_data_get_categories($view_just_parent, $categories_exclude, $data_type); ?></textarea></div>
					</div>
					</div>
						
					<div class="stuffbox">
					<h3><label for="convert_data_get_categories_with_posts">All posts ID by categories:</label></h3>
					<div class="inside">
					<div><textarea id="convert_data_get_categories_with_posts" rows="31" cols="30" style="width:100%;"><?php echo convert_data_get_categories_with_posts($categories_exclude); ?></textarea></div>
					</div>
					</div>
						
					<div class="stuffbox">
					<h3><label for="convert_data_get_posts_info">Posts INFO</label></h3>
					<div class="inside">
					<textarea id="convert_data_get_posts_info" rows="31" cols="30" style="width:100%;"><?php echo convert_data_get_posts_info($data_type); ?></textarea>
					</div>
					</div>
						
					<div class="stuffbox">
					<h3><label for="convert_data_id">Setting</label></h3>
					<div class="inside">

					<select name="convert_data_view_just_parent" id="convert_data_view_just_parent">
					<?php
					if(get_option('convert_data_view_just_parent')==1){
					echo '<option value="1" selected="selected">Just Parent</option>';
					echo '<option value="0">All Categories</option>';
					}else{
					echo '<option value="1">Just Parent</option>';
					echo '<option value="0" selected="selected">All Categories</option>';
					}
					?>
					</select> <label for="convert_data_view_just_parent">View Parent</label><br />
						
					<select name="convert_data_type" id="convert_data_type">
					<?php
					if(get_option('convert_data_type')==1){
					echo '<option value="1" selected="selected">Array</option>';
					echo '<option value="0">List</option>';
					}else{
					echo '<option value="1">Array</option>';
					echo '<option value="0" selected="selected">List</option>';
					}
					?>
					</select> <label for="convert_data_type">Data type</label><br />
					

					<input type="text" name="convert_data_categories_exclude" id="convert_data_categories_exclude" value="<?php echo strip_tags(get_option('convert_data_categories_exclude')); ?>" /> <label for="convert_data_categories_exclude">Exclude categories</label><br />

					</div>
					</div>

					<div id="publishing-action">
						<input name="Submit" type="submit" class="button-large button-primary" id="publish" value="Update options" />
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<?php
}