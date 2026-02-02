<?php
function convert_data_get_categories($justparent=0, $exclude='', $data_type=0, $prefix=''){
	if($prefix == ""){
		$data_prefix = '';
	}else{
		$data_prefix = $prefix;
	}

	$categories = get_categories( array( 'orderby' => 'id', 'order' => 'ASC', 'parent' => 0, 'hide_empty' => 0, 'exclude' => $exclude ) );
	$text = '<ul class="convert_data_ul">'."\n";
	$text_array = '';
	$text_sub_array = '';
	$text_sub_child_array = '';
	foreach($categories as $category){
		$category_id = $category->term_id;
		$category_link = esc_url( get_category_link( $category_id ) );
		if(function_exists('convert_data_filter')){
			$category_title = convert_data_filter( $category->name );
			$category_description = convert_data_filter( $category->description );
			$category_category_description = convert_data_filter( $category->category_description );
		}else{
			$category_title = esc_attr( $category->name );
			$category_description = esc_attr( $category->description );
			$category_category_description = esc_attr( $category->category_description );
		}

		$category_count = $category->count;
		$category_category_count = $category->category_count;
		$category_slug = $category->slug;
		$category_term_taxonomy_id = $category->term_taxonomy_id;
		$category_parent = $category->parent;
		$category_category_parent = $category->category_parent;
		
		$text .= '<li><a href="'.$category_link.'">'.$category_title.'</a>';
		
		$sub_categories = get_categories( array( 'orderby' => 'id', 'order' => 'ASC', 'parent' => $category_id, 'child_of' => $category_id, 'hide_empty' => 0, 'exclude' => $exclude ) );
		if( $sub_categories ){
			$text .= "\n".'<ul>'."\n";
			foreach($sub_categories as $sub_category){
				$sub_id = $sub_category->term_id;
				$sub_link = esc_url( get_category_link( $sub_id ) );
				if(function_exists('convert_data_filter')){
					$sub_title = convert_data_filter( $sub_category->name );
					$sub_description = convert_data_filter( $sub_category->description );
					$sub_category_description = convert_data_filter( $sub_category->category_description );
				}else{
					$sub_title = esc_attr( $sub_category->name );
					$sub_description = esc_attr( $sub_category->description );
					$sub_category_description = esc_attr( $sub_category->category_description );
				}
				
				$sub_count = $sub_category->count;
				$sub_category_count = $sub_category->category_count;
				$sub_slug = $sub_category->slug;
				$sub_term_taxonomy_id = $sub_category->term_taxonomy_id;
				$sub_parent = $sub_category->parent;
				$sub_category_parent = $sub_category->category_parent;

				$text .= '<li><a href="'.$sub_link.'">'.$sub_title.'</a>';
				
				/* Start Sub Child */
				$sub_child_categories = get_categories( array( 'orderby' => 'id', 'order' => 'ASC', 'parent' => $sub_id, 'child_of' => $sub_id, 'hide_empty' => 0, 'exclude' => $exclude ) );
				if( $sub_child_categories ){
					$text .= "\n".'<ul>'."\n";
					foreach($sub_child_categories as $sub_child_category){
						$sub_child_id = $sub_child_category->term_id;
						$sub_child_link = esc_url( get_category_link( $sub_child_id ) );
						
						if(function_exists('convert_data_filter')){
							$sub_child_title = convert_data_filter( $sub_child_category->name );
							$sub_child_description = convert_data_filter( $sub_child_category->description );
							$sub_child_category_description = convert_data_filter( $sub_child_category->category_description );
						}else{
							$sub_child_title = esc_attr( $sub_child_category->name );
							$sub_child_description = esc_attr( $sub_child_category->description );
							$sub_child_category_description = esc_attr( $sub_child_category->category_description );
						}
						
						$sub_child_count = $sub_child_category->count;
						$sub_child_category_count = $sub_child_category->category_count;
						$sub_child_slug = $sub_child_category->slug;
						$sub_child_term_taxonomy_id = $sub_child_category->term_taxonomy_id;

						$sub_child_parent = $sub_child_category->parent;
						$sub_child_category_parent = $sub_child_category->category_parent;

						$text .= '<li><a href="'.$sub_child_link.'">'.$sub_child_title.'</a></li>'."\n";
						$text_sub_child_array .= "array(\n";
						$text_sub_child_array .= "'id' => '".$sub_child_id."',\n";
						$text_sub_child_array .= "'title' => '".$sub_child_title."',\n";
						$text_sub_child_array .= "'parent' => '".$sub_child_parent."',\n";
						/*
						if($sub_child_description != ""){
						$text_sub_child_array .= "'description' => '".$sub_child_description."',\n";
						}
						*/
						$text_sub_child_array .= "),\n";
					}
					$text .= '</ul>'."\n";
				}
				/* End Sub Child */
				
				$text_sub_array .= "array(\n";
				$text_sub_array .= "'id' => '".$sub_id."',\n";
				$text_sub_array .= "'title' => '".$sub_title."',\n";
				$text_sub_array .= "'parent' => '".$sub_parent."',\n";
				if($sub_description != ""){
				$text_sub_array .= "'description' => '".$sub_description."',\n";
				}
				if($text_sub_child_array == ""){
					$text_sub_array .= '';
				}else{
					$text_sub_array .= "'child' => array(\n".$text_sub_child_array."),\n";
				}
				$text_sub_array .= "),\n";
				
				$text .= '</li>'."\n";
			}
			
			$text .= '</ul>'."\n";
		}
		$text .= '</li>'."\n";
		$text_array .= '$Categories[\''.$category_id.'\'] = array('."\n";
		$text_array .= "'id' => '".$category_id."',\n";
		$text_array .= "'title' => '".$category_title."',\n";
		$text_array .= "'description' => '".$category_description."',\n";
		$text_array .= "'parent' => '".$category_parent."',\n";
		if($text_sub_array == ""){
			$text_array .= '';
		}else{
			$text_array .= "'child' => array(\n".$text_sub_array."),\n";
		}
		$text_array .= ");\n";
	}
	$text .= '</ul>'."\n";
	if($data_type == 1){
		return "function ".$data_prefix."categories(){\n".$text_array."\nreturn $"."Categories;\n}";
	}else{
		return $text;
	}
}
?>