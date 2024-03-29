<?php
/**
 * This file contains functions that have been deprecated.
 * They will still work, but it we recommend you switch to the new methods instead.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/*
 * get_theme_file_uri added in WP v 4.7
 * We use this fallback for older versions of WP
 * It will be removed later..
 */
if( ! function_exists( 'get_theme_file_uri' ) ) {

	function get_theme_file_uri( $file = '' ) {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$url = get_stylesheet_directory_uri();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$url = get_stylesheet_directory_uri() . '/' . $file;
		} else {
			$url = get_template_directory_uri() . '/' . $file;
		}

		return $url;
	}
}


/*
 * Update the old builder to the new one | Comaptability with Sahifa
 */
if( ! function_exists( 'tie_update_old_builder' ) ) {

	add_action( 'load-post.php', 'tie_update_old_builder' );
	function tie_update_old_builder(){

		$post = get_post();

		if( ! empty( $post->ID ) ){
			$post_id = $post->ID;
		}
		elseif( ! empty( $_GET['post'] ) ){
			$post_id = $_GET['post'];
		}

		if( empty( $post_id ) ){
			return;
		}


		# Get All Catgeories List
		$all_categories = array();
		$get_categories = get_categories( 'hide_empty=0' );

		if( ! empty( $get_categories ) && is_array( $get_categories ) ){

			foreach ( $get_categories as $category ){
				$all_categories[] = $category->cat_ID;
			}
		}

		# Get all custom meta values ---------
		$custom_data = get_post_custom( $post_id );

		# Default meta values ---------
		$default_values = array(

			# The page builder and sidebar
			'tie_builder'      => false,
			'tie_sidebar_post' => false,
			'tie_sidebar_pos'  => false,

			# Grid Slider
			'featured_posts'                => false,
			'featured_posts_style'          => false,
			'featured_posts_number'         => false,
			'featured_posts_offset'         => false,
			'featured_posts_order'          => false,
			'featured_posts_query'          => false,
			'featured_posts_cat'            => false,
			'featured_posts_tag'            => false,
			'featured_posts_custom'         => false,
			'featured_posts_colored_mask'   => false,
			'featured_auto'                 => false,
			'featured_posts_title_length'   => false,
			'featured_posts_excerpt'        => false,
			'featured_posts_excerpt_length' => false,
			'featured_posts_category'       => false,
			'featured_posts_date'           => false,
			'featured_videos_list_title'    => false,
			'featured_videos_list'          => false,
			'featured_posts_color'          => false,
			'featured_posts_bg'             => false,
			'featured_posts_parallax'       => false,
			'featured_posts_posts'          => false,
			'featured_posts_pages'          => false,
			'featured_posts_speed'          => false,
			'featured_posts_time'           => false,

			# Normal Slider
			'slider'                  => false,
			'slider_pos'              => 'small',
			'slider_type'             => false,
			'flexi_slider_effect'     => false,
			'flexi_slider_speed'      => false,
			'flexi_slider_time'       => false,
			'elastic_slider_effect'   => false,
			'elastic_slider_autoplay' => false,
			'elastic_slider_interval' => false,
			'elastic_slider_speed'    => false,
			'slider_caption'          => false,
			'slider_caption_length'   => false,
			'slider_number'           => false,
			'slider_query'            => false,
			'slider_cat'              => false,
			'slider_tag'              => false,
			'slider_posts'            => false,
			'slider_pages'            => false,
			'slider_custom'           => false,

		);

		$custom_data = wp_parse_args( $custom_data, $default_values );


		# Convert all array values to single value
		foreach ( $custom_data as $key => $data ) {
			$data = is_array( $data ) ? $data[0] : $data;
			$custom_data[ $key ] = maybe_unserialize( $data );
		}

		# Extract the meta data
		extract( $custom_data );

		# Check if there is an old builder
		if( empty( $tie_builder ) || ! is_array( $tie_builder ) ) {
			return;
		}

		$new_builder     = array();
		$modified_blocks = array();


		# The Grid Slider
		if( $featured_posts ){

			$slider_style = 'slider_12';
			if( $featured_posts_style ){
				if( $featured_posts_style == 'video_list' ){
					$slider_style = 'videos_list';
				}
				else{
					$slider_style = 'slider_'.$featured_posts_style;
				}
			}

			$slider_block = array(
				array(
					'style'          => $slider_style,
					'order'          => $featured_posts_order          ? $featured_posts_order          : 'latest',
					'id'             => $featured_posts_cat            ? $featured_posts_cat            : false,
					'tags'           => $featured_posts_tag            ? $featured_posts_tag            : false,
					'number'         => $featured_posts_number         ? $featured_posts_number         : 10,
					'offset'         => $featured_posts_offset         ? $featured_posts_offset         : false,
					'colored_mask'   => $featured_posts_colored_mask   ? $featured_posts_colored_mask   : false,
					'animate_auto'   => $featured_auto                 ? $featured_auto                 : false,
					'title_length'   => $featured_posts_title_length   ? $featured_posts_title_length   : false,
					'excerpt'        => $featured_posts_excerpt        ? $featured_posts_excerpt        : false,
					'excerpt_length' => $featured_posts_excerpt_length ? $featured_posts_excerpt_length : false,
					'posts_category' => $featured_posts_category       ? $featured_posts_category       : false,
					'post_meta'      => $featured_posts_date           ? $featured_posts_date           : false,
					'title'          => $featured_videos_list_title    ? $featured_videos_list_title    : false,
					'boxid'          => 'block_'. rand(200, 3500),
				)
			);

			if( $featured_videos_list ){
				$slider_block[0]['videos'] = $featured_videos_list;
				$slider_block[0]['dark']   = 'true';
			}

			if( $featured_posts_query == 'custom' ){
				$slider_block[0]['custom_slider'] = $featured_posts_custom ? $featured_posts_custom : false;
			}

			$new_builder[] = array(
				'settings' => array(
					'sidebar_position' => 'full',
					'section_width'    => 'true',
					'background_color' => $featured_posts_color    ? $featured_posts_color    : false,
					'background_img'   => $featured_posts_bg       ? $featured_posts_bg       : false,
					'parallax'         => $featured_posts_parallax ? $featured_posts_parallax : false,
					'section_id'       => 'tiepost-'. $post_id .'-section-'. rand(200, 3500),
				),
				'blocks' => $slider_block
			);
		}


		# The Normal Slider
		if( $slider ){

			$normal_slider = array(
				'style'          => 'slider_8',
				'id'             => $slider_cat              ? $slider_cat              : false,
				'tags'           => $slider_tag              ? $slider_tag              : false,
				'number'         => $slider_number           ? $slider_number           : 5,
				'animate_auto'   => $elastic_slider_autoplay ? $elastic_slider_autoplay : false,
				'excerpt'        => $slider_caption          ? $slider_caption          : false,
				'excerpt_length' => $slider_caption_length   ? $slider_caption_length   : false,
				'post_meta'      => 'true',
				'boxid'          => 'block_'. rand(200, 3500),
			);

			if( $slider_query == 'custom' ){
				$normal_slider[0]['slider_custom'] = $slider_custom ? $slider_custom : false;
			}

			// Big Slider
			if( $slider_pos == 'big' ){
				$new_builder[] = array(
					'settings' => array(
						'sidebar_position' => 'full',
						'section_width'    => 'true',
						'section_id'       => 'tiepost-'. $post_id .'-section-'. rand(200, 3500),
					),
					'blocks' => array( $normal_slider )
				);
			}
			// Small Slider above the blocks
			else{
				$modified_blocks[] = $normal_slider;
			}
		}


		# Prepare the he New blocks
		foreach( $tie_builder as $block ){

			$block['excerpt']   = 'true';
			$block['post_meta'] = 'true';
			$block['read_more'] = 'true';

			if( ! empty( $block['type'] ) ){

				// Scrolling Block
				if( $block['type'] == 's' ){
					$block['style'] = 'scroll';
				}

				// Tabs Block
				elseif( $block['type'] == 'tabs' ){
					$block['style'] = 'tabs';
				}

				// Ads Block
				elseif( $block['type'] == 'ads' ){
					$block['style'] = 'ad';

					if( ! empty( $block['text'] ) ){
						$block['ad_code'] = $block['text'];
						unset( $block['text'] );
					}
				}

				// Videos Block
				elseif( $block['type'] == 'videos' ){
					$block['style']  = 'first_big';
					$block['number'] = 4;
				}

				// News in Picture Block
				elseif( $block['type'] == 'news-pic' && $block['style'] == 'default' ){
					$block['style'] = 'grid';
				}

				// Recent Posts
				elseif( $block['type'] == 'recent' && ! empty( $block['display'] ) ) {

					if( $block['display'] == 'default' ){
						$block['style'] = 'mini';
					}
					elseif( $block['display'] == 'full_thumb' ){
						$block['style'] = 'full_thumb';
					}
					elseif( $block['display'] == 'blog' ){
						$block['style'] = 'default';
					}
					elseif( $block['display'] == 'content' ){
						$block['style'] = 'content';
					}
					elseif( $block['display'] == 'masonry' ){
						$block['style'] = 'big';
					}
					elseif( $block['display'] == 'timeline' ){
						$block['style'] = 'timeline';
					}

					unset( $block['display'] );

					// Categories
					if( ! empty( $block['exclude'] ) && is_array( $block['exclude'] ) ) {

						if( is_array( $all_categories ) ) {
							$block['id'] = array_diff( $all_categories, $block['exclude'] );
						}

						unset( $block['exclude'] );
					}

				}
				unset( $block['type'] );
			}

			// Old slider block
			if( ! empty( $block['style'] ) && $block['style'] == 'slider' ){
				$block['style'] = 'slider_8';
			}


			$modified_blocks[] = $block;
		}


		# Custom Sidebar
		$tie_sidebar_post = $tie_sidebar_post ? $tie_sidebar_post : tie_get_option( 'sidebar_page' );

		# Sidebar Position
		if( empty( $tie_sidebar_pos ) || ( ! empty( $tie_sidebar_pos ) && $tie_sidebar_pos == 'default' ) ){
			$tie_sidebar_pos = tie_get_option( 'sidebar_pos' );
		}

		# Prepare the new builder
		$new_builder[] = array(
			'settings' => array(
				'sidebar_position'   => $tie_sidebar_pos,
				'sidebar_id'         => $tie_sidebar_post,
				'predefined_sidebar' =>	'true',
				'section_id'         => 'tiepost-'. $post_id .'-section-'. rand(200, 3500),
			),
			'blocks' => $modified_blocks
		);


		# Update the new builder
		update_post_meta( $post_id, 'tie_page_builder', $new_builder );

		# Delete the old builder data
		foreach ( $default_values as $key => $value ) {
			if( $key != 'tie_sidebar_post' && $key != 'tie_sidebar_pos' ){
				delete_post_meta( $post_id, $key );
			}
		}
	}
}


/*
 * wp_body_open added in WP v 5.2
 * We use this fallback for older versions of WP
 * It will be removed later..
 */
if ( ! function_exists( 'wp_body_open' ) ) {
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}


/*
 * wp_doing_ajax added in WP v 4.7
 * We use this fallback for older versions of WP
 * It will be removed later..
 */
if( ! function_exists( 'wp_doing_ajax' ) ) {

	function wp_doing_ajax() {
		return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
}
