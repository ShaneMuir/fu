<?php
// Remove Unnecessary Meta Tags
remove_action( 'wp_head', 'wp_generator' ) ;
remove_action( 'wp_head', 'wlwmanifest_link' ) ;
remove_action( 'wp_head', 'rsd_link' ) ;
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

// Remove Secondary Feeds
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

//Disable native emjoi support
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

//Remove version numbers from CSS/JS in head
function remove_cssjs_ver( $src ) {
    if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );

// Remove Header Scripts
function header_deregister_scripts(){
}
add_action( 'wp_head', 'header_deregister_scripts' );

// Remove Footer Scripts
function footer_deregister_scripts(){
    wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'footer_deregister_scripts' );

// Only load jQuery Migrate where it's required
function remove_jquery_migrate( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
        if ( $script->deps ) {
            // Check whether the script has any dependencies
            $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
        }
    }
}
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );

remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
remove_action( 'wp_body_open', 'gutenberg_global_styles_render_svg_filters' );

// Defer parsing of scripts except jQuery
function defer_parsing_of_js( $url ) {
    if ( is_user_logged_in() ) return $url; //don't break WP Admin
    if ( FALSE === strpos( $url, '.js' ) ) return $url;
    if ( strpos( $url, 'jquery.js' ) || strpos( $url, 'jquery.min.js' ) ) return $url;
    return str_replace( ' src', ' defer src', $url );
}
add_filter( 'script_loader_tag', 'defer_parsing_of_js', 10 );


// Remove the REST head links, we're not using
remove_action( 'wp_head', 'rest_output_link_wp_head'  );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// Remove Classic theme styles from the head
add_action( 'wp_enqueue_scripts', 'fu_deregister_styles', 20 );
function fu_deregister_styles() {
    wp_dequeue_style( 'classic-theme-styles' );
}

// Remove User Roles we're not using
remove_role( 'contributor' );
remove_role( 'editor' );
remove_role( 'author' );
