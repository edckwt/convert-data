<?php
/*
 Plugin Name: Convert Data
 Plugin URI: http://www.islam.com.kw
 Description: This plugin produce for you code is array of categories and posts.
 Version: 1.4
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
	add_option( 'convert_data_prefix', '', '', 'yes' );
	add_option( 'convert_data_custom_field_1', '', '', 'yes' );
	add_option( 'convert_data_custom_field_2', '', '', 'yes' );
	add_option( 'convert_data_custom_field_3', '', '', 'yes' );
	add_option( 'convert_data_custom_field_4', '', '', 'yes' );
	add_option( 'convert_data_custom_field_5', '', '', 'yes' );
	add_option( 'convert_data_view_source_categories', '1', '', 'yes' );
	add_option( 'convert_data_view_source_posts_ids', '1', '', 'yes' );
	add_option( 'convert_data_view_source_posts_info', '1', '', 'yes' );
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
	echo ".convert_data_ul li { border-bottom:1px solid #cccccc; padding-bottom:7px; }";
	echo ".convert_data_ul li ul { padding:0 10px; margin-top:7px; }";
	echo ".convert_data_ul li ul li { padding:0 10px; margin-bottom:7px; border-bottom:0px solid #cccccc;  }";
	echo ".convert_data_ul li ul li ul li { padding:0 10px; border-bottom:0px solid #cccccc;  }";
	
	do_action('convert_data_css');
	echo "</style>\n";
}
add_action('admin_head','convert_data_add_style');

add_action('admin_menu', 'convert_data_menu');
function convert_data_menu() {
	add_menu_page( 'Convert Data', 'Convert Data', 'manage_options', 'convert-data', 'convert_data_setting', ''.trailingslashit(plugins_url(null,__FILE__)).'/i/convert_data.png' );
	add_submenu_page( 'convert-data', 'Setting', 'Setting', 'manage_options', 'convert-data', 'convert_data_setting');
	add_submenu_page( 'convert-data', 'Output', 'Output', 'manage_options', 'convert-data-edit-setting', 'convert_data_code');
}

function convert_data_filter($text) {
$code = stripslashes($text);
//$code = htmlspecialchars($code);
$code = str_replace("'","&#39;",$code);
$code = str_replace("&","&amp;",$code);
$code = str_replace('"',"&quot;",$code);
return trim($code);
}

function convert_data_get_sub($parent_id=0, $cats=''){
	global $wpdb;
	
	$categories_child_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND parent='".$parent_id."'" );
	$categories_child = $wpdb->get_results("SELECT * FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND parent='".$parent_id."' order by term_id ASC");
	$get_sub = '';
	$ii = 0;
	foreach($categories_child as $sub){
		$sub_id = $sub->term_taxonomy_id;
		$sub_category_id = $sub->term_id;
		$sub_count = $sub->count;
		$sub_category_link = get_category_link( $sub_category_id );
		$sub_description = esc_attr($sub->description);
		
		if ( in_array( $sub_category_id , $cats ) ) {
			$sub_checked = ' checked="checked"';
		}else{
			$sub_checked = '';
		}
	
		++$ii;
		$get_sub_title = $wpdb->get_row( "SELECT * FROM $wpdb->terms WHERE term_id='".$sub_category_id."'" );
		if(function_exists('convert_data_filter')){
			$sub_category_title = convert_data_filter($get_sub_title->name);
			$sub_category_description = convert_data_filter($sub_description);
		}else{
			$sub_category_title = esc_attr($get_sub_title->name);
			$sub_category_description = esc_attr($sub_description);
		}
		
		$get_sub .= '<li><label class="selectit"><input value="'.$sub_category_id.'" type="checkbox" name="post_category[]" id="in-category-'.$sub_category_id.'"'.$sub_checked.' /> '.esc_attr($get_sub_title->name).'</label>'.convert_data_get_sub($sub_category_id, $cats).'</li>';
	}
	if($get_sub == ""){
		return $get_sub;
	}else{
		return '<ul>'.$get_sub.'</ul>';
	}
}

function convert_data_checkbox_categories($cats_id=''){
    global $wpdb;
	$cats = explode ( ',' , $cats_id );
	$text = '<ul class="convert_data_ul">';
	$categories = $wpdb->get_results("SELECT * FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND parent='0' order by term_id ASC");
	foreach($categories as $category){
		$id = $category->term_taxonomy_id;
		$category_id = $category->term_id;
		$count = $category->count;
		
		$category_link = get_category_link( $category_id );
		$url = esc_url($category_link);
		
		if(function_exists('convert_data_filter')){
			$description = convert_data_filter($category->description);
		}else{
			$description = esc_attr($category->description);
		}

		$get_sub = convert_data_get_sub($category_id, $cats);

		$get_title = $wpdb->get_row( "SELECT * FROM $wpdb->terms WHERE term_id='".$category_id."'" );
		if(function_exists('convert_data_filter')){
			$category_title = convert_data_filter($get_title->name);
		}else{
			$category_title = esc_attr($get_title->name);
		}
		
		if ( in_array( $category_id , $cats ) ) {
			$checked = ' checked="checked"';
		}else{
			$checked = '';
		}
		
		$text .= '<li><label class="selectit"><input value="'.$category_id.'" type="checkbox" name="post_category[]" id="in-category-'.$category_id.'"'.$checked.' /> '.esc_attr($get_title->name).'</label>';
		if($get_sub != ""){
			$text .= '<ul>'.$get_sub.'</ul>';
		}
		$text .= '</li>';
	}
	$text .= '</ul>';
	return $text;
}

function convert_data_code() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
$categories_exclude = strip_tags(get_option('convert_data_categories_exclude'));
$view_just_parent = intval(get_option('convert_data_view_just_parent'));
$data_type = intval(get_option('convert_data_type'));
$data_prefix = strip_tags(get_option('convert_data_prefix'));
$view_source_categories = intval(get_option('convert_data_view_source_categories'));
$view_source_posts_ids = intval(get_option('convert_data_view_source_posts_ids'));
$view_source_posts_info = intval(get_option('convert_data_view_source_posts_info'));
?>

<div class="wrap">
	<h2>Output</h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<form name="sytform" action="" method="post">
				<input type="hidden" name="submitted" value="1" />
	
					<?php if($view_source_categories == 1){ ?>
					<div class="stuffbox">
					<h3><label for="convert_data_get_categories">All categories:</label></h3>
					<div class="inside">
					<div><textarea id="convert_data_get_categories" rows="31" cols="30" style="width:100%;"><?php echo convert_data_get_categories($view_just_parent, $categories_exclude, $data_type, $data_prefix); ?></textarea></div>
					</div>
					</div>
					<?php } ?>
					
					<?php if($view_source_posts_ids == 1){ ?>
					<div class="stuffbox">
					<h3><label for="convert_data_get_categories_with_posts">All posts ID by categories:</label></h3>
					<div class="inside">
					<div><textarea id="convert_data_get_categories_with_posts" rows="31" cols="30" style="width:100%;"><?php echo convert_data_get_categories_with_posts($categories_exclude, $data_prefix); ?></textarea></div>
					</div>
					</div>
					<?php } ?>
					
					<?php if($view_source_posts_info == 1){ ?>
					<div class="stuffbox">
					<h3><label for="convert_data_get_posts_info">Posts INFO</label></h3>
					<div class="inside">
					<textarea id="convert_data_get_posts_info" rows="31" cols="30" style="width:100%;"><?php echo convert_data_get_posts_info($data_type, $data_prefix); ?></textarea>
					</div>
					</div>
					<?php } ?>

			</div>
		</div>
	</div>
</div>
<?php
}

function convert_data_setting() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

if(isset($_POST['submitted']) && $_POST['submitted'] == 1){
	/*
    if(preg_match('/^[0-9, ]+$/', strip_tags($_POST['convert_data_categories_exclude']))) {
		update_option( 'convert_data_categories_exclude', strip_tags($_POST['convert_data_categories_exclude']) );
    }else{
    	echo '<div id="message" class="error"><p><strong>Error</strong> Exclude categories is not match</p></div>';
    }
    */
    if(isset($_POST['post_category']) && count($_POST['post_category']) > 0){
    	$post_category = '';
    	for($i=0; $i < count($_POST['post_category']); ++$i){
    		$post_category .= intval($_POST['post_category'][$i]).',';
    	}
    	update_option( 'convert_data_categories_exclude', rtrim($post_category, ',') );
    }else{
    	echo '<div id="message" class="error"><p><strong>Error</strong> not found post_category!</p></div>';
    }
	
	update_option( 'convert_data_view_just_parent', intval($_POST['convert_data_view_just_parent']) );
	update_option( 'convert_data_type', intval($_POST['convert_data_type']) );
	update_option( 'convert_data_prefix', strip_tags($_POST['convert_data_prefix']) );
	update_option( 'convert_data_custom_field_1', strip_tags($_POST['convert_data_custom_field_1']) );
	update_option( 'convert_data_custom_field_2', strip_tags($_POST['convert_data_custom_field_2']) );
	update_option( 'convert_data_custom_field_3', strip_tags($_POST['convert_data_custom_field_3']) );
	update_option( 'convert_data_custom_field_4', strip_tags($_POST['convert_data_custom_field_4']) );
	update_option( 'convert_data_custom_field_5', strip_tags($_POST['convert_data_custom_field_5']) );
	update_option( 'convert_data_view_source_categories', intval($_POST['convert_data_view_source_categories']) );
	update_option( 'convert_data_view_source_posts_ids', intval($_POST['convert_data_view_source_posts_ids']) );
	update_option( 'convert_data_view_source_posts_info', intval($_POST['convert_data_view_source_posts_info']) );
}
$categories_exclude = strip_tags(get_option('convert_data_categories_exclude'));
$view_just_parent = intval(get_option('convert_data_view_just_parent'));
$data_type = intval(get_option('convert_data_type'));
$data_prefix = strip_tags(get_option('convert_data_prefix'));
$convert_data_custom_field_1 = strip_tags(get_option('convert_data_custom_field_1'));
$convert_data_custom_field_2 = strip_tags(get_option('convert_data_custom_field_2'));
$convert_data_custom_field_3 = strip_tags(get_option('convert_data_custom_field_3'));
$convert_data_custom_field_4 = strip_tags(get_option('convert_data_custom_field_4'));
$convert_data_custom_field_5 = strip_tags(get_option('convert_data_custom_field_5'));
$view_source_categories = intval(get_option('convert_data_view_source_categories'));
$view_source_posts_ids = intval(get_option('convert_data_view_source_posts_ids'));
$view_source_posts_info = intval(get_option('convert_data_view_source_posts_info'));
?>

<div class="wrap">
	<h2>Convert Data</h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<form name="sytform" action="" method="post">
				<input type="hidden" name="submitted" value="1" />

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

					<input type="text" name="convert_data_prefix" id="convert_data_prefix" value="<?php echo strip_tags(get_option('convert_data_prefix')); ?>" /> <label for="convert_data_prefix">Fuctions prefix</label><br />
						
					<input type="text" name="convert_data_custom_field_1" id="convert_data_custom_field_1" value="<?php echo strip_tags(get_option('convert_data_custom_field_1')); ?>" /> <label for="convert_data_custom_field_1">Custom Field 1</label><br />
					<input type="text" name="convert_data_custom_field_2" id="convert_data_custom_field_2" value="<?php echo strip_tags(get_option('convert_data_custom_field_2')); ?>" /> <label for="convert_data_custom_field_2">Custom Field 2</label><br />
					<input type="text" name="convert_data_custom_field_3" id="convert_data_custom_field_3" value="<?php echo strip_tags(get_option('convert_data_custom_field_3')); ?>" /> <label for="convert_data_custom_field_3">Custom Field 3</label><br />
					<input type="text" name="convert_data_custom_field_4" id="convert_data_custom_field_4" value="<?php echo strip_tags(get_option('convert_data_custom_field_4')); ?>" /> <label for="convert_data_custom_field_4">Custom Field 4</label><br />
					<input type="text" name="convert_data_custom_field_5" id="convert_data_custom_field_5" value="<?php echo strip_tags(get_option('convert_data_custom_field_5')); ?>" /> <label for="convert_data_custom_field_5">Custom Field 5</label><br />
						
					<select name="convert_data_view_source_categories" id="convert_data_view_source_categories">
					<?php
					if(get_option('convert_data_view_source_categories')==1){
					echo '<option value="1" selected="selected">View</option>';
					echo '<option value="0">Hidden</option>';
					}else{
					echo '<option value="1">View</option>';
					echo '<option value="0" selected="selected">Hidden</option>';
					}
					?>
					</select> <label for="convert_data_view_source_categories">Categories output</label><br />
						
					<select name="convert_data_view_source_posts_ids" id="convert_data_view_source_posts_ids">
					<?php
					if(get_option('convert_data_view_source_posts_ids')==1){
					echo '<option value="1" selected="selected">View</option>';
					echo '<option value="0">Hidden</option>';
					}else{
					echo '<option value="1">View</option>';
					echo '<option value="0" selected="selected">Hidden</option>';
					}
					?>
					</select> <label for="convert_data_view_source_posts_ids">Posts IDs output</label><br />
						
					<select name="convert_data_view_source_posts_info" id="convert_data_view_source_posts_info">
					<?php
					if(get_option('convert_data_view_source_posts_info')==1){
					echo '<option value="1" selected="selected">View</option>';
					echo '<option value="0">Hidden</option>';
					}else{
					echo '<option value="1">View</option>';
					echo '<option value="0" selected="selected">Hidden</option>';
					}
					?>
					</select> <label for="convert_data_view_source_posts_info">Posts info output</label><br />
						
					<h3><label>Exclude categories</label></h3>
					<?php echo convert_data_checkbox_categories($categories_exclude); ?>

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