<?php
/*
Name: Related Post Box
Author: Nadiar AS -- ngeblog.co
Description: Adds Related Post with Img to Thesis.
Version: 2.0.3
Class: related_post_box
*/


class related_post_box extends thesis_box {
	
	protected function translate() {
		$this->title = __('Related Post Box', 'thesis');
	}
	
	
	protected function options() {
		global $thesis;
		return array(
			'img1check' => array(
				'type' => 'checkbox',
					'label' => __('Display Related Post with Image', 'thesis'),
					'options' => array(
						'img1checktext' => __('Click here to enable Related Post with Image', 'thesis'),
					),
					'default' => array(
						'img1checktext' => false,
						'html'	=> ''
					)
				),
				'h3title'=>array(
					'type'=>'text',
					'width'=>'long',
					'label'=>__('H3 title','thesis'),
					'tooltip'=>sprintf(__('The title for h3 tag above related post img','thesis')),
					'default'=>'Related Posts'
				),
				'nrelated'=>array(
					'type'=>'text',
					'width'=>'short',
					'label'=>__('Related posts','thesis'),
					'tooltip'=>sprintf(__('Number of related posts that will be shown','thesis')),
					'default'=>'4'
				)
			);
	}

	public function html() {
		global $thesis;
		// get options
		$options = $thesis->api->get_options($this->options(), $this->options);

	?>
	
	<?php if($options['img1check']){ ?>
	
	<?php $orig_post = $post;
		add_theme_support( 'post-thumbnails' );
		// add_image_size('featuredImageCropped', 100, 50, true);
		global $post;
		$nposts = $options['nrelated'];
		$relatedTitle = $options['h3title'];
		$tags = wp_get_post_tags($post->ID);

		if ($tags) {
			$tag_ids = array();
			foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
				$args=array(
					'tag__in' => $tag_ids,
					'post__not_in' => array($post->ID),
					'posts_per_page'=> $nposts,
					'caller_get_posts'=>1,
					'orderby'=>'rand'
				);
			// endfor
			
			$my_query = new wp_query( $args );
			if( $my_query->have_posts() ) {
				echo '<div id="relatedposts"><h3>'.$relatedTitle.'</h3><ul>';
				
				while( $my_query->have_posts() ) {
					$my_query->the_post(); ?>
					<li>
						<div class="relatedthumb">
							<a href="<? the_permalink()?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_post_thumbnail( array(100,100) ); ?></a>
						</div>
						<div class="relatedcontent">
							<span><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php if (strlen($post->post_title) > 65) { echo substr(the_title($before = '', $after = '', FALSE), 0, 65) . '...'; } else { the_title();} ?></a></a></span>
						</div>
					</li>
				<? } // endwhile
				
				echo '</ul></div>';
			}
		}
		
		$post = $orig_post;
		wp_reset_query(); ?>	
		
	<?php }
	}
}
?>