<?php
/*
Plugin Name: Stoopid Facebook
Plugin URI: http://josephcarrington.com
Description: Most Facebook page feed widgets are way too complex for my tastes. This just makes a list.
Version: 0.1
Author: Joseph Carrington
Author URI: http://josephcarrington.com
License: GPL2
*/

class StoopidFacebook extends WP_Widget
{
	private $page_id;
	private $number_posts;
	function __construct()
	{
		parent::__construct(
			'stoopid_facebook',
			'Stoopid Facebook Page Feed',
			array(
				'description' => 'Allows you to pull public page posts from Facebook'
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$page_id = $instance['stoopid_facebook_page_id'];
		$app_id = $instance['stoopid_facebook_app_id'];
		$app_secret = $instance['stoopid_facebook_app_secret'];
		$num_posts = $instance['stoopid_facebook_num_posts'];
		if(!$page_id) return;
		// Curl Stuff
		$graph_url ="https://graph.facebook.com/$page_id/posts?limit=$num_posts&fields=link,message&access_token=$app_id|$app_secret";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $graph_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$output = curl_exec($ch);
		curl_close($ch);
		
		$posts = json_decode($output)->data;
		echo $args['before_widget'];
		if(!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

		if(count($posts) > 0)
		{
			echo "<ul class='stoopid_facebook_posts'>";
				foreach($posts as $post)
				{
					
					if(isset($post->message))
					{
						?>
						<li class='stoopid_facebook_post'>
							
								
							<a href='<?php echo isset($post->link) ? $post->link : "http://facebook.com/" . $page_id; ?>' title='<?php echo htmlspecialchars($post->message); ?>'><?php echo $post->message; ?></a>

						</li><!-- .stoopid_facebook_post -->
					<?php
					}
				}
			echo "</ul><!-- .stoopid_facebook_posts -->";
		}
		else echo "<p class='stoopid_error'>No recent posts</p>";
		echo $args['after_widget'];
	}

	public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : '';
		$stoopid_facebook_page_id = isset($instance['stoopid_facebook_page_id']) ? $instance['stoopid_facebook_page_id'] : '';
		$stoopid_facebook_app_id = isset($instance['stoopid_facebook_app_id']) ? $instance['stoopid_facebook_app_id'] : '';
		$stoopid_facebook_app_secret = isset($instance['stoopid_facebook_app_secret']) ? $instance['stoopid_facebook_app_secret'] : '';
		$stoopid_facebook_num_posts = isset($instance['stoopid_facebook_num_posts']) ? $instance['stoopid_facebook_num_posts'] : '3';
		?>
		<p>
			<label for='<?php echo $this->get_field_id('title'); ?>'>Title</label>
			<input class='widefat' id='<?php echo $this->get_field_id('title'); ?>' name='<?php  echo $this->get_field_name('title'); ?>' type='text' value='<?php echo esc_attr($title); ?>' />
		<p>
			<label for='<?php echo $this->get_field_id('stoopid_facebook_page_id'); ?>'>Facebook Page ID</label>
			<input class='widefat' id='<?php echo $this->get_field_id('stoopid_facebook_page_id'); ?>' name='<?php echo $this->get_field_name('stoopid_facebook_page_id'); ?>' type='text' value='<?php echo esc_attr($stoopid_facebook_page_id); ?>' />
			Go to your page, the ID looks like http://facebook.com/YOUR_PAGE_ID
		</p>
		<p>
			<label for='<?php echo $this->get_field_id('stoopid_facebook_app_id'); ?>'>App ID</label>
			<input class='widefat' id='<?php echo $this->get_field_id('stoopid_facebook_app_id'); ?>' name='<?php echo $this->get_field_name('stoopid_facebook_app_id'); ?>' type='text' value='<?php echo esc_attr($stoopid_facebook_app_id); ?>' />
			For this and the secret below, you have to go to http://developers.facebook.com and set up a new App.
		</p>
		<p>
			<label for='<?php echo $this->get_field_id('stoopid_facebook_app_secret'); ?>'>App Secret</label>
			<input class='widefat' id='<?php echo $this->get_field_id('stoopid_facebook_app_secret'); ?>' name='<?php echo $this->get_field_name('stoopid_facebook_app_secret'); ?>' type='text' value='<?php echo esc_attr($stoopid_facebook_app_secret); ?>' />
		</p>
		<p>
			<label for='<?php echo $this->get_field_id('stoopid_facebook_num_posts'); ?>'>Number of Posts</label>
			<input class='widefat' id='<?php echo $this->get_field_id('stoopid_facebook_num_posts'); ?>' name='<?php echo $this->get_field_name('stoopid_facebook_num_posts'); ?>' type='text' value='<?php echo esc_attr($stoopid_facebook_num_posts); ?>' />
		</p>
		<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['stoopid_facebook_page_id'] = (!empty($new_instance['stoopid_facebook_page_id'])) ? strip_tags($new_instance['stoopid_facebook_page_id']) : '';
		$instance['stoopid_facebook_app_id'] = (!empty($new_instance['stoopid_facebook_app_id'])) ? strip_tags($new_instance['stoopid_facebook_app_id']) : '';
		$instance['stoopid_facebook_app_secret'] = (!empty($new_instance['stoopid_facebook_app_secret'])) ? strip_tags($new_instance['stoopid_facebook_app_secret']) : '';
		$instance['stoopid_facebook_num_posts'] = (!empty($new_instance['stoopid_facebook_num_posts'])) ? strip_tags($new_instance['stoopid_facebook_num_posts']) : '3';
		return $instance;

	}
}

add_action('widgets_init', function() 
{
	register_widget('StoopidFacebook');
});
