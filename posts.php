<?php
/*
Plugin Name: Postlist shortcode
Plugin URI: https://github.com/pathawks/postlist
Description: Adds shortcode to embed a list of posts
Author: Pat Hawks
Author URI: http://pathawks.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 1.03

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
	global $wpdb;


	$embed = get_transient( 'dirtysuds_postlist' . implode($atts) );
	if( $embed ) return $embed;

	$embed = '';

        $cats = get_categories();

      if ( $cats ) {
        $embed .= '<ul>';

        foreach ( $cats as $cat ) {
          $embed .= '<li class="category-tree-item"><a href="'.get_category_link( $cat->cat_ID ).'">'.$cat->cat_name.'</a>';

          $posts = $wpdb->get_results( $wpdb->prepare(
		"SELECT ID, post_title FROM $wpdb->posts
		 LEFT JOIN $wpdb->term_relationships ON
			($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		 LEFT JOIN $wpdb->term_taxonomy ON
			($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
		WHERE $wpdb->posts.post_status = 'publish'
		AND $wpdb->term_taxonomy.taxonomy = 'category'
		AND $wpdb->term_taxonomy.term_id = %d
		ORDER BY ID DESC",
		$cat->cat_ID ) );

          if ( $posts ) {
		$embed .= '<ul';
		if ( in_array( 'id', $atts ) )
			$embed .= ' id="'.$atts['id'].'"';
		$embed .= '>';

		$displayed = 0;
		foreach( $posts as $post ):
			$embed .= '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></li>';
		$displayed++;
		endforeach;

		if ( $cat->count > $displayed ) {
                    $embed .= '<li class="more-in-category"><a href="'.get_category_link( $cat->cat_ID ).'">'.__('(more in this category ...)').'</a></li>';
                }

// If a tag has been set, and a "morelink" parameter specified, display a link to that category

		if ( in_array( 'tag', $atts ) && in_array( 'morelink', $atts ) ) {
			$embed .= '<li><a href="'.get_category_link($atts['tag']).'">'.$atts['morelink'].'</a></li>';
		}


		$embed .=  '</ul>';
	} else {
		$embed = '<!-- No matching posts found -->';
	}

        $embed .=  '</li>';
      }

      $embed .=  '</ul>';
    }

	set_transient( 'dirtysuds_postlist' . implode($atts), $embed, 5 * MINUTE_IN_SECONDS );

	return $embed;
}
