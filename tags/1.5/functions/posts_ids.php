<?php
function convert_data_get_categories_data($exclude='', $prefix=''){
	if($prefix == ""){
		$data_prefix = '';
	}else{
		$data_prefix = $prefix;
	}

	$categories = get_categories( array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, 'exclude' => $exclude ) );

	$text_array = '';
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
		$category_parent = $category->parent;
		$category_category_parent = $category->category_parent;
		
		$text_array .= '/* '.$category_title.' */'."\n";
		$text_array .= convert_data_get_posts_data($category_id)."\n";
	}
	return "function ".$data_prefix."posts_ids(){\n".$text_array."return $"."POSTS_ID;\n}";
}

function convert_data_get_posts_data($category_id=''){
    global $wpdb, $post;

	$args = array(
		'posts_per_page'   => 10,
		'orderby'          => 'id',
		'category'         => $category_id,
		'order'            => 'ASC',
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'fields'           => 'ids'
	);
	
	$posts = get_posts($args);
	
	$text_array = '';
	foreach($posts as $post){
		setup_postdata( $post );
		$post_id = get_the_ID();
		$text_array .= '$POSTS_ID[\''.$category_id.'\'][] = '.$post_id.';'."\n";
	}

	return $text_array;
}
?>