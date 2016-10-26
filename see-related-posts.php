<?php
/**
 * Plugin Name: See Related Articles
 * Description: Load 3 latest posts snippits with AJAX, from same category
 * Author: Nilojan Alvis
 * Author URI: http://www.nilojan.com
 * Version: 0.1
 */
 
 /**
  * Initialization. Add our script if needed on this page.
  */
 // register the ajax loading
 add_action('single_template', 'sra_init');
 function sra_init() {

 	// check if single post
 	if( is_single() ) {	
	global $post;
	// check if the post is enabled to show the related post
	if( !empty( $post->meta_val ) ) {
    
 		// Queue JS and CSS
 		wp_enqueue_script(
 			'sra-related-posts',
 			plugin_dir_url( __FILE__ ) . 'js/related-posts.js',
 			array('jquery'),
 			'1.0',
 			true
 		);
 		
 		wp_enqueue_style(
 			'sra-style',
 			plugin_dir_url( __FILE__ ) . 'css/style.css',
 			false,
 			'1.0',
 			'all'
 		);
		
 		// Add some parameters for the JS. set the ajax url and the post id
 		wp_localize_script(
 			'sra-related-posts',
 			'sra_related',
 			array( 
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'poid' => $post->ID,
				)
 		);	
	}
	wp_reset_query();
 	}
 }
 // the ajax function hook for logged in user and non logged in user
add_action( 'wp_ajax_nopriv_my_related_posts', 'my_related_posts' );
add_action('wp_ajax_my_related_posts','my_related_posts');

function my_related_posts() {
	$pido = is_int($_POST['pido']);
	// query to get the latest 3 post from same category
	$related = new WP_Query( array( 'category__in' => wp_get_post_categories($pido), 'posts_per_page' => 3, 'post__not_in' => array($pido) ) );
		if($related->have_posts()){
		echo "<h3>Related Articles</h3><ul class='relpost'>";
			while( $related->have_posts() ) { $related->the_post();
			/* the html output */ ?>
			<li class="post clearfix" id="post-<?php the_ID(); ?>">
				<div class="post-content clearfix">
				<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
					<h4 class="post-title"><?php the_title(); ?></h4></a>
					<div class="post-img"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail');  ?></a></div>
					<div class="post-text"><?php //the_excerpt();
							 echo mb_strimwidth(get_the_excerpt(), 0, 150, '...');
							 //echo excerpt(30); ?>
							 </div>
				</div>
			</li>
		<?php
		}
		echo "</ul>";
	}wp_die();
}

// Limit Excerpt Length by number of Words
/*
function excerpt( $limit ) {
	$excerpt = explode(' ', get_the_excerpt(), $limit);
	if (count($excerpt)>=$limit) {
	array_pop($excerpt);
	$excerpt = implode(" ",$excerpt).'...';
	} else {
	$excerpt = implode(" ",$excerpt);
	}
	$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
	return $excerpt;
}
*/

	

/**
 * Adds a meta box to the post editing
 */
function see_rel_custom_meta() {
    add_meta_box( 'see_rel_meta', __( 'Load Related Posts', 'see_rel_textdomain' ), 'see_rel_meta_callback', 'post' );
}
add_action( 'add_meta_boxes', 'see_rel_custom_meta' );

/**
 * Outputs the content of the see related checkbox
 */
function see_rel_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'see_rel_nonce' );
    $see_rel_stored_meta = get_post_meta( $post->ID );
    ?>
 <p>
    <div class="see-rel-row-content">
        <label for="see-rel-checkbox">
            <input type="checkbox" name="meta_val" id="see-rel-checkbox" value="1" <?php if ( isset ( $see_rel_stored_meta['meta_val'] ) ) checked( $see_rel_stored_meta['meta_val'][0], 1 ); ?> />
            <?php _e( 'Include "See Related Post Button" in this Post', 'see_rel_textdomain' )?>
        </label>
    </div>
</p>
    <?php
}

/**
 * Saves the see related input
 */
function see_rel_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'see_rel_nonce' ] ) && wp_verify_nonce( $_POST[ 'see_rel_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'meta-text' ] ) ) {
        update_post_meta( $post_id, 'meta_val', sanitize_text_field( $_POST[ 'meta_val' ] ) );
    }
	
	// Checks for input and saves
	if( isset( $_POST[ 'meta_val' ] ) ) {
		update_post_meta( $post_id, 'meta_val', 1 );
	} else {
		update_post_meta( $post_id, 'meta_val', '' );
	}
 
}
add_action( 'save_post', 'see_rel_meta_save' );
?>
