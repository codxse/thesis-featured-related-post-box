<?php

/*
Name: Related Post Box
Author: Nadiar AS -- ngeblog.co
Description: Adds Related Post with Img to Thesis.
Version: 2.1.1
Class: related_post_box
*/


class related_post_box extends thesis_box {
	
	protected function translate() {
		$this->title = __('Related Posts Box', 'thesis');
	}
	
	protected function construct() {
		global $thesis;
		if (!$this->_display()) return;
		add_theme_support('post-thumbnails');
		if (empty($thesis->_wp_featured_image_rss)) {
			add_filter('the_content', array($this, 'add_image_to_feed'));
			$thesis->_wp_featured_image_rss = true;
		}
	}

	protected function options() {
		global $thesis, $_wp_additional_image_sizes;
		$options = array(
			'full' => __('Full size (default)', 'thesis'),
			'thumbnail' => __('Thumbnail', 'thesis'),
			'medium' => __('Medium', 'thesis'),
			'large' => __('Large', 'thesis'));
		if (!empty($_wp_additional_image_sizes))
			foreach ($_wp_additional_image_sizes as $size => $data)
				$options[$size] = $size;
		return array(
			'activate' => array(
				'type' => 'checkbox',
					'label' => __('Display Related Post with Image', 'thesis'),
					'options' => array(
						'img1checktext' => __('Click here to activate Related Post with Image', 'thesis'),
					),
					'default' => array(
						'img1checktext' => false,
						'html'	=> ''
					)
			),
			'title' => array(
				'type' => 'text',
				'width' => 'medium',
				'label' => __('Title', 'thesis'),
				'tooltip' => sprintf(__('Enter the title of Related Posts you would like to show', 'thesis')),
				'default' => 'Related Posts'
				),
			'number' => array(
				'type' => 'text',
				'width' => 'tiny',
				'label' => __('Max Number of Related Post', 'thesis'),
				'tooltip' => sprintf(__('Enter the Number of Related Post you want to display(leave blank for 3 posts)', 'thesis')),
				'default' => '4'
				),
			'size' => array(
				'type' => 'select',
				'label' => __('Featured Image Size', 'thesis'),
				'tooltip' => sprintf(__('Choose the size of the Feature Image for this location. The list includes <a href="%s">WordPress standard image sizes</a> and any other registered image sizes.', 'thesis'), admin_url('options-media.php')),
				'options' => $options,
				'default' => 'full'),
			'link' => array(
				'type' => 'checkbox',
				'options' => array(
					'link' => __('Link image to post', 'thesis')),
				'default' => array(
					'link' => true))
		);
	}

	public function html() {
		global $thesis, $post;
		// get options
		$options = $thesis->api->get_options($this->_options(), $this->options);
		
		// activate
		if ($options['activate']) {
			/*
			 *	CORE CODE
			 */ 
			$orig_post = $post;
   			global $post;
    		$categories = get_the_category($post->ID);

			$title = !empty($this->options['title']) ? $this->options['title'] : 'Related posts';
			$number = !empty($this->options['number']) ? $this->options['number'] : '4';
			$size = !empty($this->options['size']) ? $this->options['size'] : 'thumbnail';

    		if ($categories) {
    			$category_ids = array();
 				
 				foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

 				$args=array(
    				'category__in' => $category_ids,
    				'post__not_in' => array($post->ID),
    				'posts_per_page'=> $number, // Number of related posts that will be shown.
    				'caller_get_posts'=>1
    			);

    			$my_query = new wp_query( $args );

    			if( $my_query->have_posts() ) { ?>

    				<div id="relatedposts">
                	<h3 class="related_post_label"><?php echo $title; ?></h3>
                	<ul class="related_posts_list">

    				<?php	

    				$link = !isset($this->options['link']) ? ?> <a href="<?php the_permalink()?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( $options['size'] ); ?></a> <?php : the_post_thumbnail( $options['size'] ); ?>

    				while( $my_query->have_posts() ) {
    					$my_query->the_post(); ?>
    					<!-- related post <li> -->
						<li>
							<div class="relatedthumb">
							  <?php
							  	if (!isset($this->options['link'])) { ?>
								<a href="<?php the_permalink()?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( $options['size'] ); ?></a>
							  <?php
							  	} else {
							  		the_post_thumbnail( $options['size'] );
							  	}
							  ?>
							</div>
							<div class="relatedcontent">
								<span><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php if (strlen($post->post_title) > 65) { echo substr(the_title($before = '', $after = '', FALSE), 0, 65) . '...'; } else { the_title();} ?></a></a></span>
							</div>
						</li>
    					<!-- related post </li> -->
    				<?php
    				} // end while 
    				?> 
    				</ul>
    				</div>
    				<?php
    			} // end if
			} // end if
			$post = $orig_post;
   			wp_reset_query();
			/*
			 *	CORE CODE END
			 */ 
		} // end if
	} // end public 
}
