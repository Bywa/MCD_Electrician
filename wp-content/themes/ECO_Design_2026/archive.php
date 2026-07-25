<?php
get_header();
?>
<main class="bywa-generic-page">
    <div class="container py-5">
        <header class="mb-5">
            <h1><?php the_archive_title(); ?></h1>
            <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
        </header>
        <div class="row g-4">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="col-md-6 col-lg-4">
                    <article <?php post_class('bywa-archive-card'); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                    </article>
                </div>
            <?php endwhile; else : ?>
                <p><?php echo esc_html__('Aucun contenu.', 'eco-design-2026'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php get_footer(); ?>
