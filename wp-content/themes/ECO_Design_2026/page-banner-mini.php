<?php
/**
 * Template Name: Page avec bannière mini
 */

get_header();
?>

<main id="primary" class="site-main bywa-page-layout bywa-page-layout--hero-mini">
    <?php
    while (have_posts()) :
        the_post();

        bywa_eco_render_page_hero(get_the_ID(), 'mini');
        ?>
        <section class="bywa-page-content">
            <div class="container py-5">
                <article <?php post_class('bywa-entry-content'); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            </div>
        </section>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
