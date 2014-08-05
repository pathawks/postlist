<?php
/*
Plugin Name: Postlist shortcode
Plugin URI: https://github.com/pathawks/postlist
Description: Adds shortcode to embed a list of posts
Author: Pat Hawks
Author URI: http://pathawks.com
License: GPL2
Version: 1.01

  Copyright 2014 Pat Hawks  (email : pat@pathawks.com)

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

// [postlist cat="7" showposts="5"]

// The shortcode should be able to take almost any argument that WP_Query understands
// For a full list, see http://codex.wordpress.org/Function_Reference/WP_Query#Parameters


add_shortcode( 'postlist', 'dirtysuds_postlist' );

function dirtysuds_postlist( $atts ) {

// First, let's lay out some default query parameters
	$defaults = array(
		'showposts' => 5,
		'offset' => 0,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'include' => array(),
		'exclude' => array(),
		'meta_key' => '',
		'meta_value' =>'',
		'post_type' => 'post',
	);


// Let's use the array of shortcode attributes as an array of arguments for WP_Query
	$query = $atts;
// That's some WTF code


// If the shortcode included the argument "query" let's parse that first, then merge it with our query defaults
	if ($atts['query']) {
		$query = wp_parse_args( $atts['query'], $query );
	}


// Finally, run the query
	$posts = get_posts($query);


// Now, to prepare the embeded text to return
	$embed = '';

	if ($posts) {
		$embed .= '<ul';
		if ($atts['id'])
			$embed .= ' id="'.$atts['id'].'"';
		$embed .= '>';
		foreach( $posts as $post ):
		setup_postdata($post);
			$embed .= '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></li>';
		endforeach;


// If a category has been set, and a "morelink" parameter specified, display a link to that category

		if ($query['cat'] && $atts['morelink']) {
			$embed .= '<li><a href="'.get_category_link($query['cat']).'">'.$atts['morelink'].'</a></li>';
		}


// If a tag has been set, and a "morelink" parameter specified, display a link to that category

		if ($query['tag'] && $atts['morelink']) {
			$embed .= '<li><a href="'.get_category_link($query['tag']).'">'.$atts['morelink'].'</a></li>';
		}


		$embed .=  '</ul>';
	} else {
		$embed = '<!-- No matching posts found -->';
	}

	return $embed;
}
