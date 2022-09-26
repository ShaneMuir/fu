<?php
get_header();

if( have_posts() ) :
    while(have_posts()) : the_post();

        if( have_rows('page_content') ) :
            while( have_rows('page_content') ) : the_row();
                if( get_row_layout() === 'page_banner') :
                    $page_banner = get_sub_field('top_page_banner');
                    $page_banner_text = get_sub_field('top_page_banner_text'); ?>

                <div class="hero-banner">
                    <div class="hero-banner__image" style="background-image: url('<?php echo $page_banner['sizes']['pageBanner'];?>')"></div>
                        <div class="hero-banner__content">
                            <?php if( $page_banner_text ) : ?>
                                <h1 class="hero-banner__text">
                                    <?php echo $page_banner_text; ?>
                                </h1>
                            <?php endif; ?>
                        </div>
                </div>

                <?php endif;

                if( get_row_layout() === 'services' ) : ?>

                    <div class="services-container">
                    <?php while(the_repeater_field('service')) :
                        if(get_sub_field('image')) :
                            $image = get_sub_field('image');
                        endif;
                        if(get_sub_field('text')) :
                            $text = get_sub_field('text');
                        endif;
                        if(get_sub_field('url')) : ?>
                            <a href="<?php the_sub_field('url'); ?>" target="_blank">
                                <div class="services-container__inner">
                                    <div class="service-image" style="background-image: url('<?php echo $image['sizes']['medium']; ?>')"></div>
                                    <div class="service-text"><?php echo $text; ?></div>
                                </div>
                            </a>
                        <?php endif; ?>

                    <?php endwhile; ?>
                    </div>
                <?php endif;
            endwhile;
        endif;

    endwhile;
else:
    echo "No posts found.";
endif;

get_footer();
