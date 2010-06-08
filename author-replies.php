<?php
/*
Plugin Name: Author Comment Replies
Plugin URI: http://vocecommunications.com/services/wordpress/plugins/author-comment-replies/
Description: The Author Comment Replies plugin filters WordPress's normal comment replies handling to only allow users with authoring privileges to reply directly to comments.
Version: 2.0.1
Author: Michael Pretty (prettyboymp)
Author URI: http://vocecommunications.com

*******************************************************************
Copyright 2007-2009 Michael Pretty 

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*******************************************************************
*/

class AuthorCommentReplies
{
	public function current_user_can_reply()
	{
		static $user_can_reply;
		if(!isset($user_can_reply))
		{
			$user_can_reply = current_user_can('edit_posts'); //require at least contributor level.
		}
		return $user_can_reply;
	}

	public function comment_reply_link_filter($html, $args, $comment, $post)
	{
		if(!$this->current_user_can_reply())
		{
			$html = '';
		}
		return $html;
	}

	public function preprocess_comment_filter($commentdata)
	{
		if(!$this->current_user_can_reply())
		{
			$commentdata['comment_parent'] = 0;
		}
		else
		{
			delete_transient('author_reply_counts');
			delete_transient('author_reply_links');
		}
		return $commentdata;
	}

	public function get_author_reply_count($post_id = 0)
	{
		global $wpdb;
		if($post_id == 0)
		{
			$post_id = get_the_ID();
		}
		$reply_counts = get_transient('author_reply_counts');
		if(!$reply_counts || !is_array($reply_counts))
		{
			$reply_counts = array();
		}
		if(!isset($reply_counts[$post_id]))
		{
			$author_ids = $this->get_author_user_ids();
			if(count($author_ids) == 0)
			{
				$reply_counts[$post_id] = 0;
			}
			else
			{
				$user_ids = join(',', $author_ids);
				$reply_counts[$post_id] =	(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d and comment_approved = 1 and comment_parent <> 0 and user_id in ($user_ids)", $post_id));
			}
			set_transient('author_reply_counts', $reply_counts);
		}
		return $reply_counts[$post_id];
	}

	public function get_first_author_reply_url($post_id)
	{
		global $wpdb;
		if($post_id == 0)
		{
			$post_id = get_the_ID();
		}
		$reply_links = get_transient('author_reply_links');
		if(!$reply_links || !is_array($reply_links))
		{
			$reply_links = array();
		}
		if(!isset($reply_links[$post_id]))
		{
			$author_ids = $this->get_author_user_ids();
			$user_ids = join(',', $author_ids);

			$comment_id = $wpdb->get_var($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d and comment_approved = 1 and comment_parent <> 0 and user_id in ($user_ids) ORDER BY comment_parent LIMIT 1", $post_id));
			if($comment_id)
			{
				$reply_links[$post_id] = 	get_comment_link($comment_id);
			}
			else
			{
				$reply_links[$post_id] = "#";
			}
			set_transient('author_reply_links', $reply_links);
		}
		return $reply_links[$post_id];
	}

	public function get_author_user_ids()
	{
		global $wpdb;
		$author_ids = get_transient('cr_author_ids');
		if(!$author_ids)
		{
			$level_key = $wpdb->prefix . 'capabilities'; // wpmu site admins don't have user_levels
			$author_ids = $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value != '0' and meta_value not like %s ", $level_key, '%subscriber%') );
			set_transient('cr_author_ids', $author_ids);
		}
		return $author_ids;
	}

	public function filter_get_comments_number($count)
	{
		$count = $count - $this->get_author_reply_count();
		return $count;
	}
}
$acr = new AuthorCommentReplies();
add_filter('comment_reply_link', array($acr, 'comment_reply_link_filter'), 10, 4);
add_filter('preprocess_comment', array($acr, 'preprocess_comment_filter'), 10, 4);
add_filter('get_comments_number', array($acr, 'filter_get_comments_number'), 10, 1);