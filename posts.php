<?php
/*
Plugin Name: DirtySuds - Postlist
Plugin URI: http://dirtysuds.com
Description: Adds shortcode to embed a list of posts
Author: Pat Hawks
Version: 1.00.20110226
Author URI: http://www.pathawks.com

Updates:
1.00.20110226 - First Version

  Copyright 2011 Pat Hawks  (email : pat@pathawks.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// [postlist query="Post query"]

function dirtysuds_postlist( $atts ) {
	extract( shortcode_atts( array(
		'query' => '',
		'id' => '',
		'title' => '',
		'notcat' => '',
	), $atts ) );
	
	$embed = '';
	if ($notcat) {
		$defaults = array(
			'numberposts' => 5, 'offset' => 0,
			'category' => 0, 'orderby' => 'post_date',
			'order' => 'DESC', 'include' => array(),
			'exclude' => array(), 'meta_key' => '',
			'meta_value' =>'', 'post_type' => 'post',
			'suppress_filters' => true
		);
		$query = wp_parse_args( $query, $defaults );
		$query = array_merge( $query, array( 'category__not_in' => explode(',',$notcat) ) );
query_posts( $args );
	}
	
	$posts = get_posts($query);
	
	if ($title){
		$embed .= '<h2>'.$title.'</h2>';
	}

	if ($posts):
		$embed .= '<ul';
		if ($id)
			$embed .= ' id="'.$id.'"';
		$embed .= '>';
		foreach( $posts as $post ) : setup_postdata($post);
			$embed .= '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></li>';
		endforeach;
		$embed .=  '</ul>';
	endif;
	
	if ($notcat) {
		wp_reset_query();
	}
	
	return $embed;
}

add_shortcode( 'postlist', 'dirtysuds_postlist' );