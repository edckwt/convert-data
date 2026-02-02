<?php
function convert_data_get_posts_info($data_type=0, $prefix=''){
    global $post;
    
	if( isset($_GET['pag']) && is_numeric($_GET['pag']) ){
		$paged = intval($_GET['pag']);
	}else{
		$paged = 1;
	}
	
	//$posts_per_page = (get_option('posts_per_page')) ? get_option('posts_per_page') : 10;
	$posts_per_page = 500;
	
	$all_args = array(
		'posts_per_page'   => -1,
		'fields'           => 'ids',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post_type'        => 'post',
		'post_status'      => 'publish'
	);

	$all_posts = get_posts($all_args);
	$post_count = count($all_posts);
	$num_pages = ceil($post_count / $posts_per_page);
	if($paged > $num_pages || $paged < 1){
		$paged = $num_pages;
	}
	
	if($prefix == ""){
		$data_prefix = '';
	}else{
		$data_prefix = $prefix;
	}
	
	$convert_data_custom_field_1 = strip_tags(get_option('convert_data_custom_field_1'));
	$convert_data_custom_field_2 = strip_tags(get_option('convert_data_custom_field_2'));
	$convert_data_custom_field_3 = strip_tags(get_option('convert_data_custom_field_3'));
	$convert_data_custom_field_4 = strip_tags(get_option('convert_data_custom_field_4'));
	$convert_data_custom_field_5 = strip_tags(get_option('convert_data_custom_field_5'));
	
	$text = '<ul>'."\n";
	$text_array = '';
	$text_array .= '$p = [];'."\n";

	$args = array(
		'posts_per_page'   => $posts_per_page,
		'orderby'          => 'id',
		'order'            => 'ASC',
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'paged'            => $paged
	);
	
	$posts = get_posts($args);
	
	foreach($posts as $post){
		setup_postdata( $post );
		$post_id = get_the_ID();
		$title = get_the_title();
		$post_url = get_the_permalink();
		$excerpt = get_the_excerpt();
		
		$post_name = $post->post_name;
		$post_guid = $post->guid;
		$post_date = $post->post_date;
		$post_modified = $post->post_modified;
		$post_image = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
		
		if(function_exists('convert_data_filter')){
			$post_title = convert_data_filter($title);
			$post_excerpt = convert_data_filter($excerpt);
		}else{
			$post_title = esc_attr($title);
			$post_excerpt = esc_attr($excerpt);
		}
		
		if($post_title != ""){
			$text .= '<li><a href="'.esc_url( $post_url ).'">'.$post_title.'</a></li>'."\n";
			$text_array .= '$p[\''.$post_id.'\'] = ['."\n";
			$text_array .= "'post_title' => '".$post_title."',\n";
			$text_array .= "'post_excerpt' => '".$post_excerpt."',\n";
			$text_array .= "'post_image' => '".$post_image."',\n";
			$text_array .= "'post_link' => '".esc_url( $post_url )."',\n";
			$text_array .= "'post_url' => '".esc_url( $post_guid )."',\n";
			if($convert_data_custom_field_1 == ""){
				$text_array .= "";
			}else{
				$text_array .= "'post_custom_field_1' => '".get_post_meta($post_id, $convert_data_custom_field_1, true)."',\n";
			}
			if($convert_data_custom_field_2 == ""){
				$text_array .= "";
			}else{
				$text_array .= "'post_custom_field_2' => '".get_post_meta($post_id, $convert_data_custom_field_2, true)."',\n";
			}
			if($convert_data_custom_field_3 == ""){
				$text_array .= "";
			}else{
				$text_array .= "'post_custom_field_3' => '".get_post_meta($post_id, $convert_data_custom_field_3, true)."',\n";
			}
			if($convert_data_custom_field_4 == ""){
				$text_array .= "";
			}else{
				$text_array .= "'post_custom_field_4' => '".get_post_meta($post_id, $convert_data_custom_field_4, true)."',\n";
			}
			if($convert_data_custom_field_5 == ""){
				$text_array .= "";
			}else{
				$text_array .= "'post_custom_field_5' => '".get_post_meta($post_id, $convert_data_custom_field_5, true)."',\n";
			}
			//$text_array .= "'post_date' => '".$post_date."',\n";
			//$text_array .= "'post_modified' => '".$post_modified."'\n";
			$text_array .= "];\n";
		}

	}
	$text .= '</ul>';
		
	$pagination = '';	
	if($post_count > $posts_per_page ){
		$pagination .= '<div class="pagination">';
		$pagination .= '<ul>';
		if($paged > 1){
			$pagination .= '<li><a class="first" href="admin.php?page=convert-data-output-posts&pag=1">&laquo;</a></li>';
		}else{
			$pagination .= '<li><span class="first">&laquo;</span></li>';
		}

		for($p = 1; $p <= $num_pages; $p++){
			if ($paged == $p) {
				$pagination .= '<li><span class="current">'.$p.'</span></li>';
			}else{
				$pagination .= '<li><a href="admin.php?page=convert-data-output-posts&pag='.$p.'">'.$p.'</a></li>';
			}
		}

		if($paged < $num_pages){
			$pagination .= '<li><a class="last" href="admin.php?page=convert-data-output-posts&pag='.$num_pages.'">&raquo;</a></li>';
		}else{
			$pagination .= '<li><span class="last">&raquo;</span></li>';
		}

		$pagination .= '</ul>';
		$pagination .= '</div>';
	}
	if($data_type == 1){
		$textarea = '<textarea id="convert_data_get_posts_info" rows="31" cols="30" style="width:100%; direction: ltr; text-align: left;">';
		$textarea .= "function ".$data_prefix."posts(){\n".$text_array."\nreturn $"."p;\n}";
		$textarea .= '</textarea>';
		$textarea .= $pagination;
		return $textarea;
	}else{
		return $text;
	}
}
?>