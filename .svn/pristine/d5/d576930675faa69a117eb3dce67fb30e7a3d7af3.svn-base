<?php
/*
Plugin Name: Convert Data
Plugin URI: http://www.islam.com.kw
Description: This plugin produce for you code is array of categories and posts.
Version: 2
Author: EDC Team (E-Da`wah Committee)
Author URI: http://www.islam.com.kw
Text Domain: edc-convert-data
Domain Path: /languages
*/

function convert_data_filter($text='') {
	$code = stripslashes($text);
	$code = str_replace("'","&#39;",$code);
	$code = str_replace("&","&amp;",$code);
	$code = str_replace('"',"&quot;",$code);
	return trim($code);
}

include(plugin_dir_path( __FILE__ )."functions/categories.php");
include(plugin_dir_path( __FILE__ )."functions/posts_ids.php");
include(plugin_dir_path( __FILE__ )."functions/posts_info.php");

add_action('plugins_loaded', 'edc_covert_data_load_textdomain');
function edc_covert_data_load_textdomain() {
	load_plugin_textdomain( 'edc-convert-data', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

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

function convert_data_add_style() {
	echo "<style type=\"text/css\" media=\"screen\">\n";
	echo ".convert_data { margin:0; padding:10px; background-color:#ffffff; border:0px solid #cccccc; }";
	echo ".convert_data_ul li { border-bottom:1px solid #cccccc; padding-bottom:7px; }";
	echo ".convert_data_ul li ul { padding:0 10px; margin-top:7px; }";
	echo ".convert_data_ul li ul li { padding:0 10px; margin-bottom:7px; border-bottom:0px solid #cccccc;  }";
	echo ".convert_data_ul li ul li ul li { padding:0 10px; border-bottom:0px solid #cccccc; }";
	echo ".pagination { margin: 30px 0px; }";
    echo ".pagination ul { display:block; list-style-type:none; margin:0 auto; padding: 0px; }";
	echo ".pagination ul li { display:inline-block; list-style-type:none; margin:5px; padding: 5px 10px; border: 1px solid #ccc; }";
	echo ".pagination ul li a { display:block; text-decoration: none; }";
	do_action('convert_data_css');
	echo "</style>\n";
}
add_action('admin_head','convert_data_add_style');

add_action('admin_menu', 'convert_data_menu');
function convert_data_menu() {
	add_menu_page( __( 'Convert Data', 'edc-convert-data' ), __( 'Convert Data', 'edc-convert-data' ), 'manage_options', 'convert-data', 'convert_data_setting', ''.trailingslashit(plugins_url(null,__FILE__)).'/images/convert_data.png' );
	add_submenu_page( 'convert-data', __( 'Setting', 'edc-convert-data' ), __( 'Setting', 'edc-convert-data' ), 'manage_options', 'convert-data', 'convert_data_setting');
	add_submenu_page( 'convert-data', __( 'Categories', 'edc-convert-data' ), __( 'Categories', 'edc-convert-data' ), 'manage_options', 'convert-data-output-categories', 'convert_data_output_categories');
	add_submenu_page( 'convert-data', __( 'Posts', 'edc-convert-data' ), __( 'Posts', 'edc-convert-data' ), 'manage_options', 'convert-data-output-posts', 'convert_data_output_posts');
	add_submenu_page( 'convert-data', __( 'Post IDs', 'edc-convert-data' ), __( 'Post IDs', 'edc-convert-data' ), 'manage_options', 'convert-data-output-post-ids', 'convert_data_output_post_ids');
}

function convert_data_checkbox_categories($cats_id=''){
    global $wpdb;
	$cats = explode ( ',' , $cats_id );
	$categories = get_categories( array( 'orderby' => 'name', 'order' => 'ASC', 'parent' => 0, 'hide_empty' => 0 ) );
	$text = '<ul class="convert_data_ul">';
	foreach($categories as $category){
		$category_id = $category->term_id;
		$category_link = esc_url( get_category_link( $category_id ) );
		$category_title = esc_attr( $category->name );
		$category_count = $category->count;
		$category_category_count = $category->category_count;
		$category_slug = $category->slug;
		$category_term_taxonomy_id = $category->term_taxonomy_id;
		$category_description = esc_attr( $category->description );
		$category_category_description = esc_attr( $category->category_description );

		if ( in_array( $category_id , $cats ) ) {
			$checked = ' checked="checked"';
		}else{
			$checked = '';
		}

		$text .= '<li>';
		$text .= '<label for="in-category-'.$category_id.'" class="selectit"><input value="'.$category_id.'" type="checkbox" name="post_category[]" id="in-category-'.$category_id.'"'.$checked.' /> '.$category_title.'</label>';

		$sub_categories = get_categories( array( 'orderby' => 'name', 'order' => 'ASC', 'parent' => $category_id, 'child_of' => $category_id, 'hide_empty' => 0 ) );
		if( $sub_categories ){
			$text .= '<ul>';
			foreach($sub_categories as $sub_category){
				$sub_id = $sub_category->term_id;
				$sub_link = esc_url( get_category_link( $sub_id ) );
				$sub_title = esc_attr( $sub_category->name );
				$sub_count = $sub_category->count;
				$sub_category_count = $sub_category->category_count;
				$sub_slug = $sub_category->slug;
				$sub_term_taxonomy_id = $sub_category->term_taxonomy_id;
				$sub_description = esc_attr( $sub_category->description );
				$sub_category_description = esc_attr( $sub_category->category_description );

				if ( in_array( $sub_id , $cats ) ) {
					$sub_checked = ' checked="checked"';
				}else{
					$sub_checked = '';
				}

				$text .= '<li><label id="in-sub-'.$sub_id.'"><input value="'.$sub_id.'" type="checkbox" name="post_category[]" id="in-sub-'.$sub_id.'"'.$sub_checked.' /> '.$sub_title.'</label>';
				$sub_child_categories = get_categories( array( 'orderby' => 'name', 'order' => 'ASC', 'parent' => $sub_id, 'child_of' => $sub_id, 'hide_empty' => 0 ) );
				if( $sub_child_categories ){
					$text .= '<ul>';
					foreach($sub_child_categories as $sub_child_category){
						$sub_child_id = $sub_child_category->term_id;
						$sub_child_link = esc_url( get_category_link( $sub_id ) );
						$sub_child_title = esc_attr( $sub_child_category->name );
						$sub_child_count = $sub_child_category->count;
						$sub_child_category_count = $sub_child_category->category_count;
						$sub_child_slug = $sub_child_category->slug;
						$sub_child_term_taxonomy_id = $sub_child_category->term_taxonomy_id;
						$sub_child_description = esc_attr( $sub_child_category->description );
						$sub_child_category_description = esc_attr( $sub_child_category->category_description );

						if ( in_array( $sub_child_id , $cats ) ) {
							$sub_child_checked = ' checked="checked"';
						}else{
							$sub_child_checked = '';
						}
						$text .= '<li><label id="in-sub-'.$sub_child_id.'"><input value="'.$sub_child_id.'" type="checkbox" name="post_category[]" id="in-sub-'.$sub_child_id.'"'.$sub_child_checked.' /> '.$sub_child_title.'</label></li>';
					}
					$text .= '</ul>';
				}
				$text .= '</li>';
			}
			$text .= '</ul>';
		}
		$text .= '</li>';
	}
	$text .= '</ul>';
	return $text;
}

function convert_data_output_categories() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'edc-convert-data' ) );
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
		<h2><?php _e( 'Categories', 'edc-convert-data' ); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<form name="sytform" action="" method="post">
					<input type="hidden" name="submitted" value="1" />

						<?php if($view_source_categories == 1){ ?>
						<div class="stuffbox">
						<h3><label for="convert_data_get_categories"><?php _e( 'All categories', 'edc-convert-data' ); ?></label></h3>
						<div class="inside">
						<div><textarea id="convert_data_get_categories" rows="31" cols="30" style="width:100%; direction: ltr; text-align: left;"><?php echo convert_data_get_categories($view_just_parent, $categories_exclude, $data_type, $data_prefix); ?></textarea></div>
						</div>
						</div>
						<?php } ?>

				</div>
			</div>
		</div>
	</div>
<?php
}

function convert_data_output_posts() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'edc-convert-data' ) );
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
		<h2><?php _e( 'Posts', 'edc-convert-data' ); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<form name="sytform" action="" method="post">
					<input type="hidden" name="submitted" value="1" />

						<?php if($view_source_posts_info == 1){ ?>
						<div class="stuffbox">
						<h3><label for="convert_data_get_posts_info"><?php _e( 'Posts Info', 'edc-convert-data' ); ?></label></h3>
						<div class="inside">
						<?php echo convert_data_get_posts_info($data_type, $data_prefix); ?>
						</div>
						</div>
						<?php } ?>

				</div>
			</div>
		</div>
	</div>
<?php
}

function convert_data_output_post_ids() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'edc-convert-data' ) );
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
		<h2><?php _e( 'Post IDs', 'edc-convert-data' ); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<form name="sytform" action="" method="post">
					<input type="hidden" name="submitted" value="1" />

						<?php if($view_source_posts_ids == 1){ ?>
						<div class="stuffbox">
						<h3><label for="convert_data_get_categories_with_posts"><?php _e( 'All post IDs by categories', 'edc-convert-data' ); ?></label></h3>
						<div class="inside">
						<div><textarea id="convert_data_get_categories_with_posts" rows="31" cols="30" style="width:100%; direction: ltr; text-align: left;"><?php echo convert_data_get_categories_data($categories_exclude, $data_prefix); ?></textarea></div>
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
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'edc-convert-data' ) );
	}

if(isset($_POST['submitted']) && $_POST['submitted'] == 1){
    if(isset($_POST['post_category']) && count($_POST['post_category']) > 0){
    	$post_category = '';
    	for($i=0; $i < count($_POST['post_category']); ++$i){
    		$post_category .= intval($_POST['post_category'][$i]).',';
    	}
    	update_option( 'convert_data_categories_exclude', rtrim($post_category, ',') );
    }else{
    	echo '<div id="message" class="error"><p>'. __( '<strong>Error</strong> not found post_category!', 'edc-convert-data' ) .'</p></div>';
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
	<h2><?php _e( 'Convert Data', 'edc-convert-data' ); ?></h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<form name="sytform" action="" method="post">
				<input type="hidden" name="submitted" value="1" />

					<div class="stuffbox">
					<h3><label for="convert_data_id"><?php _e( 'Setting', 'edc-convert-data' ); ?></label></h3>
					<div class="inside">

					<select name="convert_data_view_just_parent" id="convert_data_view_just_parent">
					<?php
					if(get_option('convert_data_view_just_parent')==1){
					echo '<option value="1" selected="selected">'. __( 'Just Parent', 'edc-convert-data' ) .'</option>';
					echo '<option value="0">'. __( 'All Categories', 'edc-convert-data' ) .'</option>';
					}else{
					echo '<option value="1">'. __( 'Just Parent', 'edc-convert-data' ) .'</option>';
					echo '<option value="0" selected="selected">'. __( 'All Categories', 'edc-convert-data' ) .'</option>';
					}
					?>
					</select> <label for="convert_data_view_just_parent"><?php _e( 'View Parent', 'edc-convert-data' ); ?></label><br />

					<select name="convert_data_type" id="convert_data_type">
					<?php
					if(get_option('convert_data_type')==1){
					echo '<option value="1" selected="selected">'. __( 'Array', 'edc-convert-data' ) .'</option>';
					echo '<option value="0">'. __( 'List', 'edc-convert-data' ) .'</option>';
					}else{
					echo '<option value="1">'. __( 'Array', 'edc-convert-data' ) .'</option>';
					echo '<option value="0" selected="selected">'. __( 'List', 'edc-convert-data' ) .'</option>';
					}
					?>
					</select> <label for="convert_data_type"><?php _e( 'Data type', 'edc-convert-data' ); ?></label><br />

					<input type="text" name="convert_data_prefix" id="convert_data_prefix" value="<?php echo strip_tags(get_option('convert_data_prefix')); ?>" /> <label for="convert_data_prefix"><?php _e( 'Fuctions prefix', 'edc-convert-data' ); ?></label><br />

					<input type="text" name="convert_data_custom_field_1" id="convert_data_custom_field_1" value="<?php echo strip_tags(get_option('convert_data_custom_field_1')); ?>" /> <label for="convert_data_custom_field_1"><?php _e( 'Custom Field', 'edc-convert-data' ); ?> 1</label><br />
					<input type="text" name="convert_data_custom_field_2" id="convert_data_custom_field_2" value="<?php echo strip_tags(get_option('convert_data_custom_field_2')); ?>" /> <label for="convert_data_custom_field_2"><?php _e( 'Custom Field', 'edc-convert-data' ); ?> 2</label><br />
					<input type="text" name="convert_data_custom_field_3" id="convert_data_custom_field_3" value="<?php echo strip_tags(get_option('convert_data_custom_field_3')); ?>" /> <label for="convert_data_custom_field_3"><?php _e( 'Custom Field', 'edc-convert-data' ); ?> 3</label><br />
					<input type="text" name="convert_data_custom_field_4" id="convert_data_custom_field_4" value="<?php echo strip_tags(get_option('convert_data_custom_field_4')); ?>" /> <label for="convert_data_custom_field_4"><?php _e( 'Custom Field', 'edc-convert-data' ); ?> 4</label><br />
					<input type="text" name="convert_data_custom_field_5" id="convert_data_custom_field_5" value="<?php echo strip_tags(get_option('convert_data_custom_field_5')); ?>" /> <label for="convert_data_custom_field_5"><?php _e( 'Custom Field', 'edc-convert-data' ); ?> 5</label><br />

					<select name="convert_data_view_source_categories" id="convert_data_view_source_categories">
					<?php
					if(get_option('convert_data_view_source_categories')==1){
					echo '<option value="1" selected="selected">'. __( 'View', 'edc-convert-data' ) .'</option>';
					echo '<option value="0">'. __( 'Hidden', 'edc-convert-data' ) .'</option>';
					}else{
					echo '<option value="1">'. __( 'View', 'edc-convert-data' ) .'</option>';
					echo '<option value="0" selected="selected">'. __( 'Hidden', 'edc-convert-data' ) .'</option>';
					}
					?>
					</select> <label for="convert_data_view_source_categories"><?php _e( 'Categories output', 'edc-convert-data' ); ?></label><br />

					<select name="convert_data_view_source_posts_ids" id="convert_data_view_source_posts_ids">
					<?php
					if(get_option('convert_data_view_source_posts_ids')==1){
					echo '<option value="1" selected="selected">'. __( 'View', 'edc-convert-data' ) .'</option>';
					echo '<option value="0">'. __( 'Hidden', 'edc-convert-data' ) .'</option>';
					}else{
					echo '<option value="1">'. __( 'View', 'edc-convert-data' ) .'</option>';
					echo '<option value="0" selected="selected">'. __( 'Hidden', 'edc-convert-data' ) .'</option>';
					}
					?>
					</select> <label for="convert_data_view_source_posts_ids"><?php _e( 'Posts IDs output', 'edc-convert-data' ); ?></label><br />

					<select name="convert_data_view_source_posts_info" id="convert_data_view_source_posts_info">
					<?php
					if(get_option('convert_data_view_source_posts_info')==1){
					echo '<option value="1" selected="selected">'. __( 'View', 'edc-convert-data' ) .'</option>';
					echo '<option value="0">'. __( 'Hidden', 'edc-convert-data' ) .'</option>';
					}else{
					echo '<option value="1">'. __( 'View', 'edc-convert-data' ) .'</option>';
					echo '<option value="0" selected="selected">'. __( 'Hidden', 'edc-convert-data' ) .'</option>';
					}
					?>
					</select> <label for="convert_data_view_source_posts_info"><?php _e( 'Posts info output', 'edc-convert-data' ); ?></label><br />

					<h3><label><?php _e( 'Exclude categories', 'edc-convert-data' ); ?></label></h3>
					<?php echo convert_data_checkbox_categories($categories_exclude); ?>

					</div>
					</div>

					<div id="publishing-action">
						<input name="Submit" type="submit" class="button-large button-primary" id="publish" value="<?php _e( 'Update options', 'edc-convert-data' ); ?>" />
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
<?php
}
