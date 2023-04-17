<?php

add_action('rest_api_init', 'fuLikeRoutes');

function fuLikeRoutes() {
    register_rest_route('fu/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('fu/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}


function createLike($data) {
    if(is_user_logged_in()) {
        $professor_id = sanitize_text_field($data['professorId']);
        $professor_name = get_the_title($professor_id);
        $current_user = get_currentuserinfo();
        $user_nicename = ucfirst($current_user->data->user_nicename);

        $existQuery = new WP_Query(array(
           'author' => get_current_user_id(),
           'post_type'=> 'like',
           'meta_query' => array(
               array(
                   'key' => 'liked_professor_id',
                   'compare' => '=',
                   'value' => $professor_id
               ),
           ),
        ));

        if($existQuery->found_posts == 0 AND get_post_type($professor_id) == 'professor') {
            return wp_insert_post(array(
                'post_type' => 'like',
                'post_status' => 'publish',
                'post_title' => $user_nicename . ' likes ' . $professor_name,
                'meta_input' => array(
                    'liked_professor_id' => $professor_id,
                ),
            ));
        } else {
            die('Invalid professor ID');
        }

    } else {
        die('Yo dude you need to be logged in to like a professor...');
    }
    
}

function deleteLike($data) {
    $likedId = sanitize_text_field($data['like']);
    if(get_current_user_id() == get_post_field('post_author', $likedId) AND get_post_type($likedId) == 'like') {
        wp_delete_post($likedId, true);
        return 'Congrats, like deleted.';
    } else {
        die('You do not have permission to delete that...');
    }
}
