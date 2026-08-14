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
                    <span class="bywa-404__eyebrow"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Eroare 404</span>
                    <h1 class="bywa-404__title">Pagina nu a fost găsită</h1>
                    <p class="bywa-404__lead">
                        Pagina solicitată nu există sau a fost mutată. Poți reveni la pagina principală,
                        consulta serviciile noastre sau ne poți contacta direct pentru un răspuns rapid.
                    </p>

                    <div class="bywa-404__actions">
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(home_url('/')); ?>">Înapoi la prima pagină</a>
                        <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>">Cere o ofertă</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="bywa-404__panel">
                        <h2>Legături utile</h2>
                        <p>Acces rapid la cele mai consultate pagini ale site-ului.</p>

                        <div class="bywa-404__links">
                            <a href="<?php echo esc_url(home_url('/services/')); ?>">
                                <span>Servicii</span>
                                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/realisations/')); ?>">
                                <span>Lucrări</span>
                                <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/entreprise/')); ?>">
                                <span>Companie</span>
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
                        <h2>Căutare rapidă</h2>
                        <p>Dacă vrei o informație exactă, folosește căutarea site-ului.</p>
                        <?php get_search_form(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
