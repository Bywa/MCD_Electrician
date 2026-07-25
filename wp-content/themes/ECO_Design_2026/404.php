<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main">
    <section class="bywa-404 bywa-page-layout">
        <div class="container bywa-404__content">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="bywa-404__eyebrow"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Erreur 404</span>
                    <h1 class="bywa-404__title">Page introuvable</h1>
                    <p class="bywa-404__lead">
                        La page demandée n’existe pas ou a été déplacée. Vous pouvez revenir à l’accueil,
                        consulter nos services, ou nous contacter directement pour une réponse rapide.
                    </p>

                    <div class="bywa-404__actions">
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(home_url('/')); ?>">Retour à l’accueil</a>
                        <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>">Demander un devis</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="bywa-404__panel">
                        <h2>Liens utiles</h2>
                        <p>Accès rapide aux pages les plus consultées du site.</p>

                        <div class="bywa-404__links">
                            <a href="<?php echo esc_url(home_url('/services/')); ?>">
                                <span>Services</span>
                                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/realisations/')); ?>">
                                <span>Réalisations</span>
                                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/entreprise/')); ?>">
                                <span>Entreprise</span>
                                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                            </a>
                            <a href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>">
                                <span>Contact</span>
                                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </aside>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="bywa-404__panel">
                        <h2>Recherche rapide</h2>
                        <p>Si vous cherchez une information précise, utilisez la recherche du site.</p>
                        <?php get_search_form(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
