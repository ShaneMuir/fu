<?php

require get_theme_file_path('inc/search-route.php');
require get_theme_file_path('inc/like-route.php');
require get_theme_file_path('inc/cleanup-header-footer.php');

function fu_custom_rest () {
    register_rest_field('post', 'authorName', array(
            'get_callback' => function(){return get_the_author();}
    ));

    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function(){return count_user_posts(get_current_user_id(), 'note');}
    ));
}
add_action('rest_api_init', 'fu_custom_rest');

function pageBanner($args = NULL) {
    if($args == NULL) {
        $args['title'] = get_the_title();

        $args['subtitle'] = get_field('page_banner_subtitle');

        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() ) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
<?php }


function university_files() {
  wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAP_API_KEY, NULL, '1.0', true);
  wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

  //Puts JS onto frontend
  wp_localize_script('main-university-js', 'fuData', array(
      'root_url' => get_site_url(),
      'nonce' => wp_create_nonce('wp_rest')
  ));
}
add_action('wp_enqueue_scripts', 'university_files');


function university_features() {
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerMenuLocationOne', 'Footer Menu Location One');
    register_nav_menu('footerMenuLocationTwo', 'Footer Menu Location Two');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}
add_action('after_setup_theme', 'university_features');


function university_adjust_queries($query) {
    if (!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }


    if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }

    if(!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()) {
        $query->set('posts_per_page', -1);
    }
}
add_action('pre_get_posts', 'university_adjust_queries');


function universityMapKey($api) {
    $api['key'] = GOOGLE_MAP_API_KEY;
    return $api;
}
add_filter('acf/fields/google_map/api', 'universityMapKey');

// Redirect subscriber accounts to homepage
function redirectSubToFrontend() {
    $currentUser = wp_get_current_user();
    if( count($currentUser->roles ) == 1  AND $currentUser->roles[0] == 'subscriber') {
        wp_redirect('/');
        exit;
    }
}
add_action('admin_init', 'redirectSubToFrontend');

// Don't allow subscriber account access to admin
function noAdminForSubs() {
    $currentUser = wp_get_current_user();
    if( count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}
add_action('wp_loaded', 'noAdminForSubs');

// Customise login screen
function customLoginHtml() {
    return esc_url(site_url('/'));
}
add_filter('login_headerurl', 'customLoginHtml');

function loginCss() {
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}
add_action('login_enqueue_scripts', 'loginCss');

function loginTitle() {
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'loginTitle');

// Force notes to be published as private
function makeNotePrivate($data, $postarr) {
    if($data['post_type'] == 'note') {
        if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
            die("You have reached your note limit!");
        }
    }

    if($data['post_type'] == 'note') {
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = 'private';
    }
    return $data;
}
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

// Remove editor for pages since I don't ever use it
function remove_editor_for_pages()
{
    remove_post_type_support('page', 'editor');
}

add_action('init', 'remove_editor_for_pages');

// Completely Disable Comments and Trackbacks
function fu_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if(post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'fu_disable_comments_post_types_support');

// Close comments on the front-end
function fu_disable_comments_status(): bool
{
    return false;
}
add_filter('comments_open', 'fu_disable_comments_status', 20, 2);
add_filter('pings_open', 'fu_disable_comments_status', 20, 2);

// Hide existing comments
function fu_disable_comments_hide_existing_comments($comments): array
{
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'fu_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function fu_disable_comments_admin_menu(): void
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'fu_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function fu_disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url()); exit;
    }
}
add_action('admin_init', 'fu_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function fu_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'fu_disable_comments_dashboard');

// Remove comments links from admin bar
function fu_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'fu_disable_comments_admin_bar');