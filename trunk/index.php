<?php

/**
 * Convert Data
 *
 * Plugin Name: Convert Data
 * Plugin URI:  https://wordpress.org/plugins/convert-data/
 * Description: This plugin produce for you code is array of categories and posts.
 * Version:     2.5
 * Author:      EDC TEAM (E-Dawah Committee)
 * Author URI:  https://edc.org.kw
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Text Domain: edc-convert-data
 * Domain Path: /languages
 */

function convert_data_filter($text = '')
{
	$code = stripslashes($text);
	$code = str_replace("'", "&#39;", $code);
	$code = str_replace("&", "&amp;", $code);
	$code = str_replace('"', "&quot;", $code);
	return trim($code);
}

include(plugin_dir_path(__FILE__) . "functions/categories.php");
include(plugin_dir_path(__FILE__) . "functions/posts_ids.php");
include(plugin_dir_path(__FILE__) . "functions/posts_info.php");

add_action('plugins_loaded', 'edc_covert_data_load_textdomain');
function edc_covert_data_load_textdomain()
{
	load_plugin_textdomain('edc-convert-data', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

function convert_data_install()
{
	add_option('convert_data_categories_exclude', '1', '', 'yes');
	add_option('convert_data_view_just_parent', '0', '', 'yes');
	add_option('convert_data_type', '0', '', 'yes');
	add_option('convert_data_prefix', '', '', 'yes');
	add_option('convert_data_custom_field_1', '', '', 'yes');
	add_option('convert_data_custom_field_2', '', '', 'yes');
	add_option('convert_data_custom_field_3', '', '', 'yes');
	add_option('convert_data_custom_field_4', '', '', 'yes');
	add_option('convert_data_custom_field_5', '', '', 'yes');
	add_option('convert_data_view_source_categories', '1', '', 'yes');
	add_option('convert_data_view_source_posts_ids', '1', '', 'yes');
	add_option('convert_data_view_source_posts_info', '1', '', 'yes');
	add_option('convert_data_post_type', 'post', '', 'yes');
	add_option('convert_data_order', 'ASC', '', 'yes');
	add_option('convert_data_orderby', 'id', '', 'yes');
	add_option('convert_data_posts', '30', '', 'yes');
}
register_activation_hook(__FILE__, 'convert_data_install');

function convert_data_add_style()
{
	echo "<style type=\"text/css\" media=\"screen\">\n";
	echo ".convert_data { margin:0; padding:10px; background-color:#ffffff; border:0px solid #cccccc; }";
	echo ".convert_data_ul li { border-bottom:0px solid #cccccc; padding-bottom:7px; }";
	echo ".convert_data_ul li ul { padding:0 10px; margin-top:7px; }";
	echo ".convert_data_ul li ul li { padding:0 10px; margin-bottom:7px; border-bottom:0px solid #cccccc; padding-bottom:5px; }";
	echo ".convert_data_ul li ul li ul li { padding:0 10px; border-bottom:0px solid #cccccc; padding-bottom:5px; }";
	echo ".pagination { margin: 30px 0px; }";
	echo ".pagination ul { display:block; list-style-type:none; margin:0 auto; padding: 0px; }";
	echo ".pagination ul li { display:inline-block; list-style-type:none; margin:5px; padding: 5px 10px; border: 1px solid #ccc; }";
	echo ".pagination ul li a { display:block; text-decoration: none; }";
	echo ".mb-1 { margin-bottom: 15px; }";
	do_action('convert_data_css');
	echo "</style>\n";
}
add_action('admin_head', 'convert_data_add_style');

add_action('admin_menu', 'convert_data_menu');
function convert_data_menu()
{
	add_menu_page(__('Convert Data', 'edc-convert-data'), __('Convert Data', 'edc-convert-data'), 'manage_options', 'convert-data', 'convert_data_setting', '' . trailingslashit(plugins_url(null, __FILE__)) . '/images/convert_data.png');
	add_submenu_page('convert-data', __('Setting', 'edc-convert-data'), __('Setting', 'edc-convert-data'), 'manage_options', 'convert-data', 'convert_data_setting');
	add_submenu_page('convert-data', __('Categories', 'edc-convert-data'), __('Categories', 'edc-convert-data'), 'manage_options', 'convert-data-output-categories', 'convert_data_output_categories');
	add_submenu_page('convert-data', __('Posts', 'edc-convert-data'), __('Posts', 'edc-convert-data'), 'manage_options', 'convert-data-output-posts', 'convert_data_output_posts');
	add_submenu_page('convert-data', __('Post IDs', 'edc-convert-data'), __('Post IDs', 'edc-convert-data'), 'manage_options', 'convert-data-output-post-ids', 'convert_data_output_post_ids');
}

function convert_data_checkbox_categories($cats_id = '')
{
	$cats = explode(',', $cats_id);

	$taxonomy = esc_attr(get_option('convert_data_taxonomy'));
	if( empty($taxonomy) ){
		$taxonomy = 'category';
	}

	$args = [
		'taxonomy' => $taxonomy,
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => false
	];

	$text = '';

	$categories = get_terms( $args );
	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ){
		$text = '<ul class="convert_data_ul">';
		foreach ($categories as $category) {
			$category_id = $category->term_id;
			$category_link = esc_url( get_term_link( $category ) );
			$category_count = $category->count;
			$category_category_count = $category->category_count;
			$category_slug = $category->slug;
			$category_term_taxonomy_id = $category->term_taxonomy_id;
			$category_category_parent = $category->category_parent;
			$category_parent = $category->parent;

			$category_title = esc_attr($category->name);
			$category_description = esc_attr($category->description);

			if (in_array($category_id, $cats)) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}

			if( $category_parent == 0 ) {
				$text .= '<li>';
				$text .= '<label for="in-category-' . $category_id . '" class="selectit"><input value="' . $category_id . '" type="checkbox" name="post_category[]" id="in-category-' . $category_id . '"' . $checked . ' /> ' . $category_title . '</label>';

				$textSub = '';
				foreach( $categories as $sub_category ) {
					if($sub_category->parent == $category_id) {
		
						$sub_id = $sub_category->term_id;
						$sub_link = esc_url( get_term_link( $sub_category ) );
						$sub_title = esc_attr($sub_category->name);
						$sub_description = esc_attr($sub_category->description);
		
						$sub_parent = $sub_category->parent;
		
						if (in_array($sub_id, $cats)) {
							$sub_checked = ' checked="checked"';
						} else {
							$sub_checked = '';
						}
		
						$textSub .= '<li><label id="in-sub-' . $sub_id . '"><input value="' . $sub_id . '" type="checkbox" name="post_category[]" id="in-sub-' . $sub_id . '"' . $sub_checked . ' /> ' . $sub_title . '</label>';
		
						/* Start Sub Child */
						$textChild = '';
						foreach( $categories as $sub_child_category ) {
							if($sub_child_category->parent == $sub_id) {
		
								$sub_child_id = $sub_child_category->term_id;
								$sub_child_link = esc_url( get_term_link( $sub_child_category ) );
								$sub_child_title = esc_attr($sub_child_category->name);
								$sub_child_description = esc_attr($sub_child_category->description);
		
								if (in_array($sub_child_id, $cats)) {
									$sub_child_checked = ' checked="checked"';
								} else {
									$sub_child_checked = '';
								}
								$textChild .= '<li><label id="in-sub-' . $sub_child_id . '"><input value="' . $sub_child_id . '" type="checkbox" name="post_category[]" id="in-sub-' . $sub_child_id . '"' . $sub_child_checked . ' /> ' . $sub_child_title . '</label></li>';
							}
						}
		
						if( !empty($textChild) ){
							$textSub .= "\n" . '<ul>' . "\n";
							$textSub .= $textChild;
							$textSub .= '</ul>' . "\n";
						}
						
						/* End Sub Child */
						$textSub .= '</li>' . "\n";
					}
				}

				if( !empty($textSub) ){
					$text .= "\n" . '<ul>' . "\n";
					$text .= $textSub;
					$text .= '</ul>' . "\n";
				}

				$text .= '</li>';
			}
		}
		$text .= '</ul>';
	}

	return $text;
}

function convert_data_output_categories()
{
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'edc-convert-data'));
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
		<h2><?php _e('Categories', 'edc-convert-data'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<?php if ($view_source_categories == 1) { ?>
						<div class="stuffbox">
							<h3><label for="convert_data_get_categories"><?php _e('All categories', 'edc-convert-data'); ?></label></h3>
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

function convert_data_output_posts()
{
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'edc-convert-data'));
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
		<h2><?php _e('Posts', 'edc-convert-data'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<?php if ($view_source_posts_info == 1) { ?>
						<div class="stuffbox">
							<h3><label for="convert_data_get_posts_info"><?php _e('Posts Info', 'edc-convert-data'); ?></label></h3>
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

function convert_data_output_post_ids()
{
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'edc-convert-data'));
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
		<h2><?php _e('Post IDs', 'edc-convert-data'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
					<?php if ($view_source_posts_ids == 1) { ?>
						<div class="stuffbox">
							<h3><label for="convert_data_get_categories_with_posts"><?php _e('All post IDs by categories', 'edc-convert-data'); ?></label></h3>
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

function convert_data_setting()
{
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'edc-convert-data'));
	}

	if (isset($_POST['submitted']) && $_POST['submitted'] == 1) {
		if (! isset($_POST['CD_update']) || ! wp_verify_nonce(sanitize_text_field($_POST['CD_update']), 'CD_nonce')) {
			wp_die(__('Sorry, your nonce did not verify.'));
		} else {
			$post_category = '';
			if (isset($_POST['post_category']) && count($_POST['post_category']) > 0) {
				for ($i = 0; $i < count($_POST['post_category']); ++$i) {
					$post_category .= intval($_POST['post_category'][$i]) . ',';
				}
				if (!empty($post_category)) {
					$post_category = rtrim($post_category, ',');
				}
				//} else {
				//echo '<div id="message" class="error"><p>' . __('<strong>Error</strong> not found post_category!', 'edc-convert-data') . '</p></div>';
			}

			update_option('convert_data_categories_exclude', $post_category);

			if (isset($_POST['convert_data_view_just_parent'])) {
				$convert_data_view_just_parent = sanitize_text_field($_POST['convert_data_view_just_parent']);
				update_option('convert_data_view_just_parent', intval($convert_data_view_just_parent));
			}

			if (isset($_POST['convert_data_type'])) {
				$convert_data_type = sanitize_text_field($_POST['convert_data_type']);
				update_option('convert_data_type', intval($convert_data_type));
			}

			if (isset($_POST['convert_data_prefix'])) {
				$convert_data_prefix = sanitize_text_field($_POST['convert_data_prefix']);
				update_option('convert_data_prefix', $convert_data_prefix);
			}

			if (isset($_POST['convert_data_custom_field_1'])) {
				update_option('convert_data_custom_field_1', sanitize_text_field($_POST['convert_data_custom_field_1']));
			}

			if (isset($_POST['convert_data_custom_field_2'])) {
				update_option('convert_data_custom_field_2', sanitize_text_field($_POST['convert_data_custom_field_2']));
			}

			if (isset($_POST['convert_data_custom_field_3'])) {
				update_option('convert_data_custom_field_3', sanitize_text_field($_POST['convert_data_custom_field_3']));
			}

			if (isset($_POST['convert_data_custom_field_4'])) {
				update_option('convert_data_custom_field_4', sanitize_text_field($_POST['convert_data_custom_field_4']));
			}

			if (isset($_POST['convert_data_custom_field_5'])) {
				update_option('convert_data_custom_field_5', sanitize_text_field($_POST['convert_data_custom_field_5']));
			}

			if (isset($_POST['convert_data_view_source_categories'])) {
				$convert_data_view_source_categories = sanitize_text_field($_POST['convert_data_view_source_categories']);
				update_option('convert_data_view_source_categories', intval($convert_data_view_source_categories));
			}

			if (isset($_POST['convert_data_view_source_posts_ids'])) {
				$convert_data_view_source_posts_ids = sanitize_text_field($_POST['convert_data_view_source_posts_ids']);
				update_option('convert_data_view_source_posts_ids', intval($convert_data_view_source_posts_ids));
			}

			if (isset($_POST['convert_data_view_source_posts_info'])) {
				$convert_data_view_source_posts_info = sanitize_text_field($_POST['convert_data_view_source_posts_info']);
				update_option('convert_data_view_source_posts_info', intval($convert_data_view_source_posts_info));
			}

			if (isset($_POST['convert_data_post_type'])) {
				update_option('convert_data_post_type', sanitize_text_field($_POST['convert_data_post_type']));
			}

			if (isset($_POST['convert_data_order'])) {
				update_option('convert_data_order', sanitize_text_field($_POST['convert_data_order']));
			}

			if (isset($_POST['convert_data_orderby'])) {
				update_option('convert_data_orderby', sanitize_text_field($_POST['convert_data_orderby']));
			}

			if (isset($_POST['convert_data_taxonomy'])) {
				update_option('convert_data_taxonomy', sanitize_text_field($_POST['convert_data_taxonomy']));
			}

			if (isset($_POST['convert_data_posts'])) {
				$convert_data_posts = sanitize_text_field($_POST['convert_data_posts']);
				update_option('convert_data_posts', intval($convert_data_posts));
			}
		}
	}

	$categories_exclude = sanitize_text_field(get_option('convert_data_categories_exclude'));
	$view_just_parent = intval(get_option('convert_data_view_just_parent'));
	$data_type = intval(get_option('convert_data_type'));
	$data_prefix = sanitize_text_field(get_option('convert_data_prefix'));
	$convert_data_custom_field_1 = sanitize_text_field(get_option('convert_data_custom_field_1'));
	$convert_data_custom_field_2 = sanitize_text_field(get_option('convert_data_custom_field_2'));
	$convert_data_custom_field_3 = sanitize_text_field(get_option('convert_data_custom_field_3'));
	$convert_data_custom_field_4 = sanitize_text_field(get_option('convert_data_custom_field_4'));
	$convert_data_custom_field_5 = sanitize_text_field(get_option('convert_data_custom_field_5'));
	$view_source_categories = intval(get_option('convert_data_view_source_categories'));
	$view_source_posts_ids = intval(get_option('convert_data_view_source_posts_ids'));
	$view_source_posts_info = intval(get_option('convert_data_view_source_posts_info'));

	$convert_data_post_type = esc_attr(get_option('convert_data_post_type'));
	$convert_data_order = esc_attr(get_option('convert_data_order'));
	$convert_data_orderby = esc_attr(get_option('convert_data_orderby'));
	$convert_data_posts = intval(get_option('convert_data_posts'));
	$convert_data_taxonomy = esc_attr(get_option('convert_data_taxonomy'));

	if (empty($convert_data_posts)) {
		$convert_data_posts = 30;
	}

	if (empty($convert_data_taxonomy)) {
		$convert_data_taxonomy = 'category';
	}

	$hide_list = [
		1 => __('View', 'edc-convert-data'),
		0 => __('Hidden', 'edc-convert-data')
	];

	$type_list = [
		0 => __('List', 'edc-convert-data'),
		1 => __('Array', 'edc-convert-data')
	];

	$view_just_parent_list = [
		0 => __('Just Parent', 'edc-convert-data'),
		1 => __('All Categories', 'edc-convert-data')
	];

	$order_list = [
		'ASC' => __('ASC', 'edc-convert-data'),
		'DESC' => __('DESC', 'edc-convert-data')
	];

	$orderby_list = [
		'ID' => __('ID', 'edc-convert-data'),
		'title' => __('Title', 'edc-convert-data'),
		'date' => __('Date', 'edc-convert-data'),
		'comment_count' => __('Comment count', 'edc-convert-data')
	];

	$args = array(
		'public'   => true,
		'_builtin' => false
	);

	$output = 'names';
	$operator = 'and';

	$post_types = get_post_types($args, $output, $operator);

	$post_type_list = [];
	$post_type_list['post'] = 'post';
	if ($post_types) {
		foreach ($post_types  as $post_type) {
			$post_type_list[$post_type] = $post_type;
		}
	}

	$taxonomies = get_object_taxonomies( $post_type_list, 'objects' );
	$hide_taxonomies = ['post_tag', 'post_format'];
	$taxonomies_list = [];
	if( is_array($taxonomies) && count($taxonomies) > 0 ){
		foreach ($taxonomies as $key => $value) {
			$taxonomy_lanbel = esc_attr($value->label);
			$taxonomy_name = esc_attr($value->name);
			if( !in_array($taxonomy_name, $hide_taxonomies) ){
				$taxonomies_list[$taxonomy_name] = $taxonomy_lanbel;
			}
		}
	}
?>

	<div class="wpwrap">
		<h2><?php _e('Convert Data', 'edc-convert-data'); ?></h2>
		<div id="major-publishing-actions">
			<form name="sytform" action="" method="post">
				<?php wp_nonce_field('CD_nonce', 'CD_update'); ?>
				<input type="hidden" name="submitted" value="1" />

				<h3><label for="convert_data_id"><?php _e('Setting', 'edc-convert-data'); ?></label></h3>
				<div class="inside">
					<div class="mb-1">
						<select name="convert_data_view_just_parent" id="convert_data_view_just_parent">
							<?php
							foreach ($view_just_parent_list as $key_view => $value_view) {
								$selected_view = ($key_view == $view_just_parent ? ' selected' : '');
								echo '<option value="' . $key_view . '"' . $selected_view . '>' . $value_view . '</option>';
							}
							?>
						</select> <label for="convert_data_view_just_parent"><?php _e('View Parent', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_type" id="convert_data_type">
							<?php
							foreach ($type_list as $key_type => $value_type) {
								$selected_type_list = ($key_type == $data_type ? ' selected' : '');
								echo '<option value="' . $key_type . '"' . $selected_type_list . '>' . $value_type . '</option>';
							}
							?>
						</select> <label for="convert_data_type"><?php _e('Data type', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_order" id="convert_data_order">
							<?php
							foreach ($order_list as $key_order => $value_order) {
								$selected_order = ($key_order == $convert_data_order ? ' selected' : '');
								echo '<option value="' . $key_order . '"' . $selected_order . '>' . $value_order . '</option>';
							}
							?>
						</select> <label for="convert_data_order"><?php _e('Order', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_orderby" id="convert_data_orderby">
							<?php
							foreach ($orderby_list as $key_orderby => $value_orderby) {
								$selected_orderby = ($key_orderby == $convert_data_orderby ? ' selected' : '');
								echo '<option value="' . $key_orderby . '"' . $selected_orderby . '>' . $value_orderby . '</option>';
							}
							?>
						</select> <label for="convert_data_orderby"><?php _e('Order by', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_post_type" id="convert_data_post_type">
							<?php
							foreach ($post_type_list as $key_post_type => $value_post_type) {
								$selected_post_type = ($key_post_type == $convert_data_post_type ? ' selected' : '');
								echo '<option value="' . $key_post_type . '"' . $selected_post_type . '>' . $value_post_type . '</option>';
							}
							?>
						</select> <label for="convert_data_post_type"><?php _e('Post type', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_taxonomy" id="convert_data_taxonomy">
							<?php
							foreach ($taxonomies_list as $key_taxonomies => $value_taxonomies) {
								$selected_taxonomies = ($key_taxonomies == $convert_data_taxonomy ? ' selected' : '');
								echo '<option value="' . $key_taxonomies . '"' . $selected_taxonomies . '>' . $value_taxonomies . '</option>';
							}
							?>
						</select> <label for="convert_data_taxonomy"><?php _e('Taxonomy', 'edc-convert-data'); ?></label>
					</div>
	
					<div class="mb-1">
						<input type="number" name="convert_data_posts" id="convert_data_posts" value="<?php echo $convert_data_posts; ?>" /> <label for="convert_data_posts"><?php _e('Posts', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<input type="text" name="convert_data_prefix" id="convert_data_prefix" value="<?php echo $data_prefix; ?>" /> <label for="convert_data_prefix"><?php _e('Fuctions prefix', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<input type="text" name="convert_data_custom_field_1" id="convert_data_custom_field_1" value="<?php echo $convert_data_custom_field_1; ?>" /> <label for="convert_data_custom_field_1"><?php _e('Custom Field', 'edc-convert-data'); ?> 1</label>
					</div>

					<div class="mb-1">
						<input type="text" name="convert_data_custom_field_2" id="convert_data_custom_field_2" value="<?php echo $convert_data_custom_field_2; ?>" /> <label for="convert_data_custom_field_2"><?php _e('Custom Field', 'edc-convert-data'); ?> 2</label>
					</div>

					<div class="mb-1">
						<input type="text" name="convert_data_custom_field_3" id="convert_data_custom_field_3" value="<?php echo $convert_data_custom_field_3; ?>" /> <label for="convert_data_custom_field_3"><?php _e('Custom Field', 'edc-convert-data'); ?> 3</label>
					</div>

					<div class="mb-1">
						<input type="text" name="convert_data_custom_field_4" id="convert_data_custom_field_4" value="<?php echo $convert_data_custom_field_4; ?>" /> <label for="convert_data_custom_field_4"><?php _e('Custom Field', 'edc-convert-data'); ?> 4</label>
					</div>

					<div class="mb-1">
						<input type="text" name="convert_data_custom_field_5" id="convert_data_custom_field_5" value="<?php echo $convert_data_custom_field_5; ?>" /> <label for="convert_data_custom_field_5"><?php _e('Custom Field', 'edc-convert-data'); ?> 5</label>
					</div>

					<div class="mb-1">
						<select name="convert_data_view_source_categories" id="convert_data_view_source_categories">
							<?php
							foreach ($hide_list as $key_view_categories => $value_view_categories) {
								$selected_view_categories = ($key_view_categories == $view_source_categories ? ' selected' : '');
								echo '<option value="' . $key_view_categories . '"' . $selected_view_categories . '>' . $value_view_categories . '</option>';
							}
							?>
						</select> <label for="convert_data_view_source_categories"><?php _e('Categories output', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_view_source_posts_ids" id="convert_data_view_source_posts_ids">
							<?php
							foreach ($hide_list as $key_posts_ids => $value_posts_ids) {
								$selected_posts_ids = ($key_posts_ids == $view_source_posts_ids ? ' selected' : '');
								echo '<option value="' . $key_posts_ids . '"' . $selected_posts_ids . '>' . $value_posts_ids . '</option>';
							}
							?>
						</select> <label for="convert_data_view_source_posts_ids"><?php _e('Posts IDs output', 'edc-convert-data'); ?></label>
					</div>

					<div class="mb-1">
						<select name="convert_data_view_source_posts_info" id="convert_data_view_source_posts_info">
							<?php
							foreach ($hide_list as $key_posts_info => $value_posts_info) {
								$selected_posts_info = ($key_posts_info == $view_source_posts_info ? ' selected' : '');
								echo '<option value="' . $key_posts_info . '"' . $selected_posts_info . '>' . $value_posts_info . '</option>';
							}
							?>
						</select> <label for="convert_data_view_source_posts_info"><?php _e('Posts info output', 'edc-convert-data'); ?></label>
					</div>

					<h3><label><?php _e('Exclude categories', 'edc-convert-data'); ?></label></h3>
					<?php echo convert_data_checkbox_categories($categories_exclude); ?>

				</div>


				<div id="publishing-action">
					<input name="Submit" type="submit" class="button-large button-primary" id="publish" value="<?php _e('Update options', 'edc-convert-data'); ?>" />
				</div>

			</form>
		</div>
	</div>
<?php
}
