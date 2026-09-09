<?php
get_header();

while (have_posts()) : the_post(); ?>
    <main>
        <h1><?php the_title(); ?></h1>
        <p>כתובת: <?php echo get_post_meta(get_the_ID(), 'address', true); ?></p>
        
        <hr>
        
        <?php 
            if (comments_open() || get_comments_number()) {
                comments_template(); 
            }
        ?>
    </main>
<?php endwhile;
get_footer();