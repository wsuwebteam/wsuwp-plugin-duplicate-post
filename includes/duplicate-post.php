<?php namespace WSUWP\Plugin\Duplicate_Post;

class Duplicate_Post {

	public static function create_duplicate_button( $actions, $post ) {

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$duplicate_link = wp_nonce_url(
			add_query_arg(
				array(
					'post_id' => $post->ID,
					'action'  => 'duplicate_post',
				)
			),
			basename( __FILE__ ),
			'duplicate_post_nonce'
		);

		$actions = array_merge(
			$actions,
			array(
				'duplicate' => sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $duplicate_link ),
					'Duplicate'
				),
			)
		);

		return $actions;

	}


	public static function duplicate_post() {

		// check if post ID has been provided and action
		if ( empty( $_GET['post_id'] ) ) {
			wp_die( 'No post to duplicate has been provided!' );
		}

		// Nonce verification
		if ( ! isset( $_GET['duplicate_post_nonce'] ) || ! wp_verify_nonce( $_GET['duplicate_post_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		// Get the original post id
		$post_id = absint( $_GET['post_id'] );

		// Get the original post
		$post = get_post( $post_id );

		// Get author id
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;

		// if post data exists, create the post duplicate
		if ( $post ) {

			// new post data array
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => str_replace( '\\', '\\\\', $post->post_content ),
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name . '-copy', // slug
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title . ' (Copy)',
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order,
			);

			// insert the post by wp_insert_post() function
			$new_post_id = wp_insert_post( $args );

			/*
			* get all current post terms ad set them to the new post draft
			*/
			$taxonomies = get_object_taxonomies( get_post_type( $post ) ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}

			// duplicate all post meta
			$post_meta = get_post_meta( $post_id );
			if ( $post_meta ) {

				foreach ( $post_meta as $meta_key => $meta_values ) {

					if ( '_wp_old_slug' == $meta_key ) { // do nothing for this meta key
						continue;
					}

					$current_value = get_post_meta( $post_id, $meta_key, true );
					add_post_meta( $new_post_id, $meta_key, $current_value );

				}
			}

			// redirect to the edit post screen for the new draft
			wp_safe_redirect(
				add_query_arg(
					array(
						'post_type' => ( 'post' !== get_post_type( $post ) ? get_post_type( $post ) : false ),
						'saved'     => 'post_duplication_created', // just a custom slug here
					),
					admin_url( 'edit.php' )
				)
			);
			exit;

		} else {
			wp_die( 'Post creation failed, could not find original post.' );
		}

	}


	public static function duplicate_post_notice() {

		// Get the current screen
		$screen = get_current_screen();

		if ( 'edit' !== $screen->base ) {
			return;
		}

		// Checks if settings updated
		if ( isset( $_GET['saved'] ) && 'post_duplication_created' == $_GET['saved'] ) {
			 echo '<div class="notice notice-success is-dismissible"><p>Post copy created.</p></div>';
		}

	}


	public static function init() {

		add_filter( 'post_row_actions', __CLASS__ . '::create_duplicate_button', 10, 2 );
		add_filter( 'page_row_actions', __CLASS__ . '::create_duplicate_button', 10, 2 );

		add_action( 'admin_action_duplicate_post', __CLASS__ . '::duplicate_post' );
		add_action( 'admin_notices', __CLASS__ . '::duplicate_post_notice' );

	}
}

Duplicate_Post::init();
