<?php 
    //Custom Adobe font
	function custom_add_adobe_fonts() {
		wp_enqueue_style( 'custom-adobe-fonts', 'https://use.typekit.net/uyp0ibb.css', false );
		}
		add_action( 'wp_enqueue_scripts', 'custom_add_adobe_fonts' );
		
    //Custom child theme styles
	add_action( 'wp_enqueue_scripts', 'my_enqueue_styles' );
	function my_enqueue_styles() {
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); 
		} 

    //Custom child theme scripts
	function my_custom_scripts() {
		wp_enqueue_script( 'my_custom_scripts', get_stylesheet_directory_uri() . '/custom/scripts.js', array( 'jquery' ), '1.0.0', true );
	}
	add_action( 'wp_enqueue_scripts', 'my_custom_scripts', 20, 1 );

    //Add phone meta tags on mobile
	function add_meta_tags() {
	?>
		<meta name="format-detection" content="telephone=no">
	<?php }
	add_action('wp_head', 'add_meta_tags');

    //Remove new post from menu
	function remove_wp_nodes() 
	{
		global $wp_admin_bar;   
		$wp_admin_bar->remove_node( 'new-post' );
	}
	add_action( 'admin_bar_menu', 'remove_wp_nodes', 999 );

	function post_remove ()
	{ 
	remove_menu_page('edit.php');
	}

	add_action('admin_menu', 'post_remove');

	// Showing multiple post types in Elementor Posts Widget
	add_action( 'elementor/query/show_all_post_types', function( $query ) {
		$query->set( 'post_type', [ 'events', 'blog', 'newsletters', 'staff-vacancies', 'videos', 'awards' ] );
	} );

    //Enable media categories
	add_action('init', 'wpse_77390_enable_media_categories' , 1);
	function wpse_77390_enable_media_categories() {
	register_taxonomy_for_object_type('category', 'attachment');
	}
	
	//Custom excerpt tags with html
	function lt_html_excerpt($text) {
		global $post;
		if ( '' == $text ) {
			$text = get_the_content('');
			$text = apply_filters('the_content', $text);
			$text = str_replace('\]\]\>', ']]&gt;', $text);
			$text = strip_tags($text,'<img><a>');
		}
		return $text;
	}
	
	remove_filter('get_the_excerpt', 'wp_trim_excerpt');
	add_filter('get_the_excerpt', 'lt_html_excerpt');

	/*show cpt categories */
	add_filter('pre_get_posts', 'query_post_type');
	function query_post_type($query) {
		if(is_category() || is_tag() || is_home() && empty( $query->query_vars['suppress_filters'] ) ) {
		$post_type = get_query_var('post_type');
		if($post_type)
		$post_type = $post_type;
		else
		$post_type = array('post','blog' , 'events' , 'newsletters', 'videos', 'awards','job-vacancies','nav_menu_item');
		$query->set('post_type',$post_type);
		return $query;
		}
	}

/**
 * Disable the emoji's
 */
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	
	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

//General shortcode
function anyShortcode() {
	return get_site_url()."/path?query=".get_the_title();
}
add_shortcode('any', 'anyShortcode');

//Allow excerpts on pages
add_post_type_support( 'page', 'excerpt' );

?>