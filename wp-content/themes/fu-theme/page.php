<?php

get_header();

while(have_posts()) {
    the_post();

    pageBanner();

    ?>

    <div class="container container--narrow page-section">

        <?php

        $theParentPage = wp_get_post_parent_id(get_the_ID());
        if ($theParentPage) { ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($theParentPage); ?>">
                        <i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParentPage); ?></a>
                    <span class="metabox__main"><?php the_title(); ?></span>
                </p>
            </div>
       <?php } ?>


        <?php
        $isParent = get_pages(array(
                'child_of' => get_the_ID()
        ));
        if ($theParentPage || $isParent) { ?>

        <div class="page-links">
          <h2 class="page-links__title"><a href="<?php echo get_the_permalink($theParentPage); ?>"><?php echo get_the_title($theParentPage);?></a></h2>
          <ul class="min-list">
            <?php

                if ($theParentPage) {
                    $isChildPage = $theParentPage;
                } else {
                    $isChildPage = get_the_ID();
                }

                wp_list_pages(array(
                        'title_li' => NULL,
                        'child_of' => $isChildPage,
                    'sort_column' => 'menu_order'
                ));

            ?>
          </ul>
        </div>

        <?php } ?>


        <div class="generic-content">
            <?php the_content();
            $skyColorValue = sanitize_text_field(get_query_var('skyColor'));
            $grassColorValue = sanitize_text_field(get_query_var('grassColor'));

            if ($skyColorValue == 'blue' AND $grassColorValue == 'green') {
                echo '<p>The sky is blue today and the grass is green. Life is good.</p>';
            } elseif($skyColorValue != "" AND $grassColorValue != "") {
                echo '<p>Whaaat... are you dumb? try again...</p>';
            }

            ?>

            <form method="get">
                <input name="skyColor" placeholder="Sky color">
                <input name="grassColor" placeholder="Grass color">
                <button>Submit</button>
            </form>

        </div>

    </div>

<?php }

get_footer();

?>