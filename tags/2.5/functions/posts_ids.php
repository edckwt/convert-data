<?php
function convert_data_get_categories_data($exclude = '', $prefix = '')
{
	if ($prefix == "") {
		$data_prefix = '';
	} else {
		$data_prefix = $prefix;
	}

	$taxonomy = esc_attr(get_option('convert_data_taxonomy'));
	if (empty($taxonomy)) {
		$taxonomy = 'category';
	}

	$args = [
		'taxonomy' => $taxonomy,
		'orderby' => 'id',
		'order' => 'ASC',
		'hide_empty' => false,
		'exclude' => $exclude
	];

	$categories = get_terms($args);

	$text_array = '';
	if (! empty($categories) && ! is_wp_error($categories)) {
		$text_array .= '$p = [];' . "\n";
		foreach ($categories as $category) {
			$category_id = $category->term_id;
			$category_link = esc_url(get_term_link($category));
			$category_title = esc_attr($category->name);
			$category_count = $category->count;
			$category_category_count = $category->category_count;
			$category_slug = $category->slug;
			$category_term_taxonomy_id = $category->term_taxonomy_id;
			$category_description = esc_attr($category->description);
			$category_category_description = esc_attr($category->category_description);
			$category_parent = $category->parent;
			$category_category_parent = $category->category_parent;

			$text_array .= '/* ' . $category_title . ' */' . "\n";
			$text_array .= convert_data_get_posts_data($category_id, $taxonomy) . "\n";
		}
	}

	return "function " . $data_prefix . "posts_ids(){\n" . $text_array . "return $" . "p;\n}";
}

function convert_data_get_posts_data($category_id = '', $taxonomy = '')
{
	$post_type = esc_attr(get_option('convert_data_post_type'));
	$order = esc_attr(get_option('convert_data_order'));
	$orderby = esc_attr(get_option('convert_data_orderby'));
	$posts_per_page = esc_attr(get_option('convert_data_posts'));

	if (empty($post_type)) {
		$post_type = 'post';
	}
	if (empty($order)) {
		$order = 'ASC';
	}
	if (empty($orderby)) {
		$orderby = 'id';
	}
	if (empty($posts_per_page)) {
		$posts_per_page = 30;
	}

	$args = [
		'posts_per_page' => -1,
		'orderby' => $orderby,
		'order' => $order,
		'post_type' => $post_type,
		'post_status' => 'publish',
		'fields' => 'ids',
		'tax_query' => [
			[
				'taxonomy' => $taxonomy,
				'field' => 'term_id',
				'terms' => $category_id
			],
		]
	];

	$posts = new WP_Query($args);
	$post_count = $posts->found_posts;

	$text_array = '';
	if ($posts->have_posts()) {
		$text_array .= '$p[\'' . $category_id . '\'] = [' . "\n";
		while ($posts->have_posts()) {
			$posts->the_post();
			$post_id = get_the_ID();
			//$text_array .= '$p[\''.$category_id.'\'][] = '.$post_id.';'."\n";
			$text_array .= '' . $post_id . ',' . "\n";
		}
		$text_array .= '];' . "\n";
	}

	return $text_array;
}
