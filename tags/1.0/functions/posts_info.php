<?php
function convert_data_get_posts_info($data_type=0){
    global $wpdb;
	$text = '<ul>'."\n";;
	$text_array = '';
	$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent='0' AND post_status='publish' AND post_type='post' order by ID DESC");
	foreach($posts as $post){
		$post_id = $post->ID;
		if(function_exists('convert_data_filter')){
			$post_title = convert_data_filter($post->post_title);
			$post_excerpt = convert_data_filter($post->post_excerpt);
		}else{
			$post_title = esc_attr($post->post_title);
			$post_excerpt = esc_attr($post->post_excerpt);
		}
		$post_name = $post->post_name;
		$post_guid = $post->guid;
		$post_date = $post->post_date;
		$post_modified = $post->post_modified;
		$post_image = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
		
		if($post_title != ""){
			$text .= '<li><a href="'.esc_url( get_permalink($post_id) ).'">'.$post_title.'</a></li>'."\n";;
			$text_array .= '$POSTS_INFO[\''.$post_id.'\'] = array('."\n";
			$text_array .= "'post_title' => '".$post_title."',\n";
			$text_array .= "'post_excerpt' => '".$post_excerpt."',\n";
			$text_array .= "'post_image' => '".$post_image."',\n";
			$text_array .= "'post_link' => '".esc_url( get_permalink($post_id) )."',\n";
			$text_array .= "'post_date' => '".$post_date."',\n";
			$text_array .= "'post_modified' => '".$post_modified."'\n";
			$text_array .= ");\n";
		}

	}
	$text .= '</ul>';
	if($data_type == 1){
		return "function posts_info(){\n".$text_array."\nreturn $"."POSTS_INFO;\n}";
	}else{
		return $text;
	}
}
?>