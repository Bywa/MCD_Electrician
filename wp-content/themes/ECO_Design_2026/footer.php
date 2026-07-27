<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="bywa-site-footer">
    <div class="container bywa-footer-top">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <div class="bywa-footer-brand">
                    <?php echo bywa_eco_get_brand_logo_html('bywa-footer-brand-logo'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div>
                        <p class="bywa-footer-kicker">MCD Electrician</p>
                        <h3>Plus qu'un electricien, un partenaire fiable pour vos projets.</h3>
                    </div>
                </div>
                <p class="bywa-footer-text">Installation, depannage, maintenance, photovoltaïque, bornes de recharge et solutions intelligentes dans le Jura bernois et la region de Bienne.</p>
                <div class="bywa-footer-socials">
                    <?php foreach (bywa_eco_get_footer_social_links() as $social) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?>">
                            <i class="bi <?php echo esc_attr($social['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-sm-6 col-lg-2">
                <h4>Services</h4>
                <?php
                bywa_eco_render_footer_menu('footer_services', array(
                    array('label' => 'Installations electriques', 'url' => home_url('/services/')),
                    array('label' => 'Photovoltaïque', 'url' => home_url('/services/')),
                    array('label' => 'Bornes de recharge', 'url' => home_url('/services/')),
                    array('label' => 'Maintenance', 'url' => home_url('/services/')),
                ));
                ?>
            </div>

            <div class="col-sm-6 col-lg-2">
                <h4>Entreprise</h4>
                <?php
                bywa_eco_render_footer_menu('footer_company', array(
                    array('label' => 'Qui sommes-nous', 'url' => home_url('/entreprise/')),
                    array('label' => 'Realisations', 'url' => home_url('/realisations/')),
                    array('label' => 'Temoignages', 'url' => home_url('/#temoignages')),
                    array('label' => 'Contact', 'url' => bywa_eco_get_contact_url()),
                ));
                ?>
            </div>

            <div class="col-lg-3">
                <h4>Contact</h4>
                <div class="bywa-footer-contact">
                    <p><strong>Téléphone principal</strong><br><a href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone()); ?></a></p>
                    <p><strong>Téléphone secondaire</strong><br><a href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone_secondary())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone_secondary()); ?></a></p>
                    <p><strong>E-mail</strong><br><a href="mailto:<?php echo esc_attr(bywa_eco_get_contact_email()); ?>"><?php echo esc_html(bywa_eco_get_contact_email()); ?></a></p>
                    <p><strong>Adresse</strong><br><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_address', 'Rue de Bel-Air 22, 2732 Reconvilier')); ?></p>
                    <a class="bywa-footer-button" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>"><span class="bi bi-arrow-up-right"></span> Demander un devis</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container bywa-footer-bottom">
        <div class="row gy-3 align-items-center">
            <div class="col-lg-6">
                <p class="mb-0">&copy; <?php echo esc_html(date_i18n('Y')); ?> MCD Electrician. Tous droits reserves.</p>
            </div>
            <div class="col-lg-6">
                <div class="bywa-footer-legal">
                    <a href="<?php echo esc_url(home_url('/mentions-legales')); ?>">Mentions legales</a>
                    <a href="<?php echo esc_url(home_url('/protection-des-donnees')); ?>">Protection des donnees</a>
                    <a href="https://bywacreations.com/" target="_blank" rel="noopener">Site by Bywa Creations</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
