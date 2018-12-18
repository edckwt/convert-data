<?php
function convert_data_get_posts_info($data_type=0, $prefix=''){
    global $wpdb;
    
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
			$text_array .= "'post_date' => '".$post_date."',\n";
			$text_array .= "'post_modified' => '".$post_modified."'\n";
			$text_array .= ");\n";
		}

	}
	$text .= '</ul>';
	if($data_type == 1){
		return "function ".$data_prefix."posts_info(){\n".$text_array."\nreturn $"."POSTS_INFO;\n}";
	}else{
		return $text;
	}
}
?>