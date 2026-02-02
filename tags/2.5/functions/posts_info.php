<?php
function convert_data_get_posts_info($data_type = 0, $prefix = '')
{
	global $post;

	if (isset($_GET['pag']) && is_numeric($_GET['pag'])) {
		$paged = intval($_GET['pag']);
	} else {
		$paged = 1;
	}

	$post_type = esc_attr(get_option('convert_data_post_type'));
	$order = esc_attr(get_option('convert_data_order'));
	$orderby = esc_attr(get_option('convert_data_orderby'));
	$posts_per_page = esc_attr(get_option('convert_data_posts'));

	if( empty($post_type) ){
		$post_type = 'post';
	}
	if( empty($order) ){
		$order = 'ASC';
	}
	if( empty($orderby) ){
		$orderby = 'id';
	}
	if( empty($posts_per_page) ){
		$posts_per_page = 30;
	}

	//$posts_per_page = (get_option('posts_per_page')) ? get_option('posts_per_page') : 10;
	//$posts_per_page = 500;

	$all_args = [
		'posts_per_page' => -1,
		'fields' => 'ids',
		'orderby' => $orderby,
		'order' => $order,
		'post_type' => $post_type,
		'post_status' => 'publish'
	];

	$query = new WP_Query( $all_args );
	$post_count = $query->found_posts;

	//$all_posts = get_posts($all_args);
	//$post_count = count($all_posts);
	$num_pages = ceil($post_count / $posts_per_page);
	if ($paged > $num_pages || $paged < 1) {
		$paged = $num_pages;
	}

	$convert_data_custom_field_1 = esc_attr(get_option('convert_data_custom_field_1'));
	$convert_data_custom_field_2 = esc_attr(get_option('convert_data_custom_field_2'));
	$convert_data_custom_field_3 = esc_attr(get_option('convert_data_custom_field_3'));
	$convert_data_custom_field_4 = esc_attr(get_option('convert_data_custom_field_4'));
	$convert_data_custom_field_5 = esc_attr(get_option('convert_data_custom_field_5'));

	$text = '<ul>' . "\n";
	$text_array = '';
	if( $paged == 1 ){
		$text_array .= '$p = [];' . "\n";
	}

	$args = [
		'posts_per_page' => $posts_per_page,
		'orderby' => $orderby,
		'order' => $order,
		'post_type' => $post_type,
		'post_status' => 'publish',
		'paged' => $paged
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$post_id = get_the_ID();
			$title = esc_html( get_the_title() );
			$post_url = get_the_permalink();
			$excerpt = esc_html( get_the_excerpt() );
	
			$post_name = $post->post_name;
			$post_guid = $post->guid;
			$post_date = $post->post_date;
			$post_modified = $post->post_modified;
			$post_image = wp_get_attachment_url(get_post_thumbnail_id($post_id));
	
			if (function_exists('convert_data_filter')) {
				$post_title = convert_data_filter($title);
				$post_excerpt = convert_data_filter($excerpt);
			} else {
				$post_title = esc_attr($title);
				$post_excerpt = esc_attr($excerpt);
			}
	
			if ($post_title != "") {
				$text .= '<li><a href="' . esc_url($post_url) . '">' . $post_title . '</a></li>' . "\n";
				$text_array .= '$p[\'' . $post_id . '\'] = [' . "\n";
				$text_array .= "'post_title' => '" . $post_title . "',\n";
				$text_array .= "'post_excerpt' => '" . $post_excerpt . "',\n";
				$text_array .= "'post_image' => '" . $post_image . "',\n";
				$text_array .= "'post_link' => '" . esc_url($post_url) . "',\n";
				$text_array .= "'post_url' => '" . esc_url($post_guid) . "',\n";
	
				if (!empty($convert_data_custom_field_1)) {
					$field_1 = get_post_meta($post_id, $convert_data_custom_field_1, true);
					$text_array .= "'post_custom_field_1' => '" . convert_data_filter($field_1) . "',\n";
				}
	
				if (!empty($convert_data_custom_field_2)) {
					$field_2 = get_post_meta($post_id, $convert_data_custom_field_2, true);
					$text_array .= "'post_custom_field_2' => '" . convert_data_filter($field_2) . "',\n";
				}
	
				if (!empty($convert_data_custom_field_3)) {
					$field_3 = get_post_meta($post_id, $convert_data_custom_field_3, true);
					$text_array .= "'post_custom_field_3' => '" . convert_data_filter($field_3) . "',\n";
				}
	
				if (!empty($convert_data_custom_field_4)) {
					$field_4 = get_post_meta($post_id, $convert_data_custom_field_4, true);
					$text_array .= "'post_custom_field_4' => '" . convert_data_filter($field_4) . "',\n";
				}
	
				if (!empty($convert_data_custom_field_5)) {
					$field_5 = get_post_meta($post_id, $convert_data_custom_field_5, true);
					$text_array .= "'post_custom_field_5' => '" . convert_data_filter($field_5) . "',\n";
				}
	
				//$text_array .= "'post_date' => '".$post_date."',\n";
				//$text_array .= "'post_modified' => '".$post_modified."'\n";
				$text_array .= "];\n";
			}
		}
	}

	$text .= '</ul>';

	$pagination = '';
	if ($post_count > $posts_per_page) {
		$pagination .= '<div class="pagination">';
		$pagination .= '<ul>';
		if ($paged > 1) {
			$pagination .= '<li><a class="first" href="admin.php?page=convert-data-output-posts&pag=1">&laquo;</a></li>';
		} else {
			$pagination .= '<li><span class="first">&laquo;</span></li>';
		}

		for ($p = 1; $p <= $num_pages; $p++) {
			if ($paged == $p) {
				$pagination .= '<li><span class="current">' . $p . '</span></li>';
			} else {
				$pagination .= '<li><a href="admin.php?page=convert-data-output-posts&pag=' . $p . '">' . $p . '</a></li>';
			}
		}

		if ($paged < $num_pages) {
			$pagination .= '<li><a class="last" href="admin.php?page=convert-data-output-posts&pag=' . $num_pages . '">&raquo;</a></li>';
		} else {
			$pagination .= '<li><span class="last">&raquo;</span></li>';
		}

		$pagination .= '</ul>';
		$pagination .= '</div>';
	}
	if ($data_type == 1) {
		$html = '<div class="wpwrap">';
		$html .= '<div id="major-publishing-actions">';
		$html .= '<textarea id="convert_data_get_posts_info" rows="31" cols="30" style="width:100%; direction: ltr; text-align: left;">';
		if( $paged == 1 ){
			$html .= "function " . $prefix . "posts(){\n" . $text_array . "\nreturn $" . "p;\n}";
		}else{
			$html .= $text_array;
		}
		
		$html .= '</textarea>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= $pagination;
		return $html;
	} else {
		$html = '<div class="wpwrap">';
		$html .= '<div id="major-publishing-actions">';
		$html .= $text;
		$html .= '</div>';
		$html .= '</div>';
		$html .= $pagination;
		return $html;
	}
}
