=== Postlist Shortcode ===
Contributors: pathawks,jrevillini,dirtysuds
Donate link: https://github.com/pathawks/postlist
Tags: plugins, wordpress, shortcode, homepage
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.03

Adds shortcode `[postlist]` for embedding a list of posts into a page

== Description ==

Add the shortcode `[postlist query="PostQuery"]` to a page to enable an unordered list of links to posts matching the query.
Useful for designing custom homepages.

== Installation ==

1. Upload 'dirtysuds-postlist' to the '/wp-content/plugins/' directory
2. Activate **Postlist shortcode** through the 'Plugins' menu in WordPress
3. In the page editor, add the shortcode `[postlist query="_PostQuery_"]` where _PostQuery_ is a query that Wordpress understands, like _cat=3_ to find all posts from Category 3.


== Frequently Asked Questions ==

= I have an idea for a great way to improve this plugin =

Please open a pull request on [Github](https://github.com/pathawks/postlist)


== Changelog ==

= 1.03 20141011 =
* Bugfix

= 1.02 20141011 =
* Make use of transients

= 1.00.20110226 =
* First version
* Works
