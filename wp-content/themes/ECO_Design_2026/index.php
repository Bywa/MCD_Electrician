<?php get_header(); ?>
<main class="bywa-generic-page">
    <div class="container py-5">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article <?php post_class('py-4'); ?>>
                <h1 class="mb-4"><?php the_title(); ?></h1>
                <div class="entry-content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; else : ?>
            <p>Aucun contenu.</p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
