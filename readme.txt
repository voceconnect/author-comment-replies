=== Author Comment Replies ===
Contributors: Michael Pretty
Donate link: http://voceconnect.com/
Tags: comments, author only
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: trunk

Filters the ability to reply to comments to only contributor level or higher users.

== Description ==

Filters the ability to reply to comments to only contributor level or higher users

We originally developed the Author Comment Reply plugin solely for use on Sony Computer Entertainment Americaas (SCEA) PlayStation.Blog to visually associate reader comments and author responses. Thanks to SCEA's desire to contribute back to the WordPress community, we are able to offer this plugin for public consumption.

Requirements:

*   "php5" - I'm a big proponent of dropping the php4 compatibility of WordPress due to the improved OO support.  Because of this, I prefer to write my plugins in php5 form in hopes to help push the community along.
*   "A Comment Reply Compatible Theme" - Comment replies were introduced in WP 2.5, which means a lot of older themes don't support the comment reply functionality.

== Installation ==

1. Upload `author-replies.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Who should be able to reply to a comment? =

The plugin name of 'Author Comment Replies' is slightly misleading.  The user level required to reply to a comment is coded at level 1, which is anyone with a contributor level or above.

= I previously had version 1.? version of the Author Comment Replies plugin installed, and none of those replies are showing up after I upgraded. =

The 1.? version of this plugin came out before comment replies was a part of WP core.  Because of this, it had to use a custom table to store the replies, which is no longer the case.

== Changelog ==
= 2.1 =
* added readme
* changed capability to use 'edit_posts'
= 2.0 =
* deprecated old handling of replies to use WordPress' build in reply handling
* complete plugin rewrite
