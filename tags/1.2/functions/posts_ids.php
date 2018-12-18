<?php
function convert_data_get_posts_by_category_id($category_id=0){
    global $wpdb;
    
    $text = '';
	$taxonomy = $wpdb->get_row("SELECT * FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND term_id='".$category_id."'");
	$term_id = $taxonomy->term_id;
	$term_taxonomy_id = $taxonomy->term_taxonomy_id;
		
	$get_category_name = $wpdb->get_row( "SELECT * FROM $wpdb->terms WHERE term_id='".$term_id."'" );
	$category_title = esc_attr($get_category_name->name);
		
	$term_relationships = $wpdb->get_results("SELECT * FROM $wpdb->term_relationships WHERE term_taxonomy_id='".$term_taxonomy_id."' order by object_id DESC");
	foreach( $term_relationships as $relationship ){
		$post_id = $relationship->object_id;
		$term_taxonomy_id = $relationship->term_taxonomy_id;
		$post_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent='0' AND post_status='publish' AND post_type='post' AND ID='".$post_id."'" );
		if($post_count == 0){
			$text .= '';
		}else{
			$text .= '$POSTS_ID[\''.$term_id.'\'][] = '.$post_id.';'."\n";
		}
	}
	return $text;
}

function convert_data_get_categories_with_posts($exclude='', $prefix=''){
    global $wpdb;
	
	if($prefix == ""){
		$data_prefix = '';
	}else{
		$data_prefix = $prefix;
	}
	
    if($exclude == ""){
    	$where = "taxonomy='category' AND parent='0'";
    }else{
    	$where = "(taxonomy='category' AND parent='0') AND (term_id NOT IN ($exclude))";
    }
	$text = '';
	$categories = $wpdb->get_results("SELECT * FROM $wpdb->term_taxonomy WHERE ".$where." order by term_id ASC");
	foreach($categories as $category){
		$id = $category->term_taxonomy_id;
		$category_id = $category->term_id;
		$count = $category->count;
		$description = esc_attr($category->description);
		
		$get_title = $wpdb->get_row( "SELECT * FROM $wpdb->terms WHERE term_id='".$category_id."'" );
		$category_name = esc_attr($get_title->name);
		
		if(convert_data_get_posts_by_category_id($category_id) == ""){
			$text .= '';
		}else{
			$text .= '/* '.$category_name.' */'."\n";
			$text .= convert_data_get_posts_by_category_id($category_id)."\n";
		}
		
		/* Start sub categories*/
		$categories_child_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND parent='".$category_id."'" );
		$categories_child = $wpdb->get_results("SELECT * FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND parent='".$category_id."' order by term_id ASC");
		foreach($categories_child as $sub){
			$sub_id = $sub->term_taxonomy_id;
			$sub_category_id = $sub->term_id;
			$sub_count = $sub->count;
			$sub_description = esc_attr($sub->description);
			$get_sub_title = $wpdb->get_row( "SELECT * FROM $wpdb->terms WHERE term_id='".$sub_category_id."'" );
			$sub_category_name = esc_attr($get_sub_title->name);
			if(convert_data_get_posts_by_category_id($sub_category_id) == ""){
				$text .= '';
			}else{
				$text .= '/* '.$sub_category_name.' */'."\n";
				$text .= convert_data_get_posts_by_category_id($sub_category_id)."\n";
			}
		}
		/* End sub categories*/
	}
	return "function ".$data_prefix."posts_ids(){\n".$text."return $"."POSTS_ID;\n}";
}
?>