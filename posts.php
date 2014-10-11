<?php
/*
Plugin Name: DirtySuds - Postlist
Plugin URI: http://dirtysuds.com
Description: Adds shortcode to embed a list of posts
Author: Dirty Suds
Version: 1.01
Author URI: http://blog.dirtysuds.com
License: GPL2

Updates:
1.01 20110323 - Expanded everything
1.00 20110226 - First Version

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

/**
* USAGE
*
* Insert shortcode into post. Optionally, you can alter the output by
* specifying attributes and values.
*
*
*
**
* EXAMPLES
*
* 1. To insert a postlist using all default options, include this in the
* post:
*
* [postlist]
*
* ---
*
* 2. To insert a postlist for category 7 and only show 5 posts, include
*
* [postlist cat="7" showposts="5"]
*
*
*
**
* CONFIGURATION
* The shortcode should be able to take almost any argument that WP_Query understands
* For a full list, see http://codex.wordpress.org/Function_Reference/WP_Query#Parameters
*
*
*/


add_shortcode( 'postlist', 'dirtysuds_postlist' );

function dirtysuds_postlist( $atts, $template=NULL ) {

// First, let's lay out some default query parameters
	$defaults = array(
		'date_format'		=> get_option('date_format'),
		'morelink'		=> NULL,
	);


// If the shortcode included the argument "query" let's parse that first, then merge it with our query defaults

	$atts = wp_parse_args( $atts, $defaults );

	if (isset($atts['query'])) {
		$query = wp_parse_args( $atts['query'], $query );
	} else {
		$query = $atts;
	}

	unset(
		$query['date_format']
	);

// Finally, run the query
	$posts = get_posts($query);

// If there aren't any posts, there's no sense in going any further
	if (!isset($posts[0]))
		return '<!-- No matching posts found -->';


// Setup Template

	$before = '<ul>';
	$after = '</ul>';
	$itemtemplate = '<li><a href="{LINK}">{TITLE}</a></li>';
	$morelink = '';

	// tokens to replace in the item & morelink template
	$searchTokens = array (
		'{LINK}',
		'{TITLE}',
		'{DATE}'
	);

	if (strlen($template)) {

		// Because WordPress gives us filtered HTML in the content, we have to strip out a bunch of garbage

		$template =
			explode( "\n" ,
				trim(
					str_replace(
						array( "<br />\n" , '</p>' , '<p>' ),
						"\n",
						$template
					)
				)
			);
		switch (count($template)) {
			case 1:
				list($itemtemplate) = $template;
				break;
			case 2:
				list($itemtemplate,$morelink) = $template;
				break;
			case 3:
				list($before,$itemtemplate,$after) = $template;
				break;
			case 4:
				list($before,$itemtemplate,$morelink,$after) = $template;
				break;
		}
	}

	// the items
	$items = array();
	foreach( $posts as $post ):
		$items[] = str_replace(
			$searchTokens,
			array(
				get_permalink($post->ID),
				$post->post_title,
				date($atts['date_format'],strtotime($post->post_date)),
			),
			$itemtemplate
		);
	endforeach;



// If a category has been set, and a "morelink" parameter specified, display a link to that category

	if (isset($query['cat'],$morelink)) {
		$morelink = str_replace(
			$searchTokens,
			array(
				get_category_link($query['cat']),
				$atts['morelink'],
				null
			),
			$morelink
		);
	}


// If a tag has been set, and a "morelink" parameter specified, display a link to that category

	if (isset($query['tag'],$morelink)) {
		$morelink = str_replace(
			$searchTokens,
			array(
				get_category_link($query['tag']),
				$atts['morelink'],
				null
			),
			$morelink
		);
	}

		// Because we are very responsible Plugin Authors,
		// we sanatize all content with wp_kses before
		// returning any HTML

	global $allowedposttags;
	return wp_kses( $before . implode('', $items ) . $morelink . $after, $allowedposttags ,array('http','https') );
}
