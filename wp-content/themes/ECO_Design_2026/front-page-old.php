<?php
get_header();

$services = array(
    array('icon' => 'bi-lightning-charge-fill', 'title' => 'Installations electriques', 'text' => 'Installations neuves, renovations et mises en conformite pour logements, commerces et batiments techniques.'),
    array('icon' => 'bi-sun-fill', 'title' => 'Photovoltaïque', 'text' => 'Solutions solaires performantes pour reduire vos charges et valoriser votre bien en Suisse romande.'),
    array('icon' => 'bi-ev-front-fill', 'title' => 'Bornes de recharge', 'text' => 'Installation de bornes pour particuliers, PPE, entreprises et parcs de stationnement.'),
    array('icon' => 'bi-house-gear-fill', 'title' => 'Domotique & maintenance', 'text' => 'Maison intelligente, controles, depannage rapide et entretien de vos installations existantes.'),
);

$domains = array(
    array('number' => '01', 'title' => 'Habitat', 'text' => 'Villas, appartements, PPE et renovations completes.', 'image' => 'linear-gradient(160deg, rgba(13,24,26,.58), rgba(13,24,26,.1)), url(https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80)'),
    array('number' => '02', 'title' => 'Immeubles & regies', 'text' => 'Tableaux, communs, maintenance preventive et interventions planifiees.', 'image' => 'linear-gradient(160deg, rgba(84,185,71,.72), rgba(84,185,71,.35)), url(https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1200&q=80)'),
    array('number' => '03', 'title' => 'Commerces & bureaux', 'text' => 'Eclairage, reseaux, prises, securite et optimisation energetique.', 'image' => 'linear-gradient(160deg, rgba(13,24,26,.58), rgba(13,24,26,.1)), url(https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80)'),
    array('number' => '04', 'title' => 'Smart home & energie', 'text' => 'Pilotage, photovoltaïque, recharge et automatismes connectes.', 'image' => 'linear-gradient(160deg, rgba(13,24,26,.58), rgba(13,24,26,.1)), url(https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=1200&q=80)'),
);

$testimonials = array(
    array('name' => 'Client vérifié', 'role' => 'Reconvilier', 'text' => 'Entreprise serieuse, reactive et tres propre sur chantier. Le devis etait clair et le travail parfaitement execute.'),
    array('name' => 'Client vérifié', 'role' => 'Tavannes', 'text' => 'Tres bon accompagnement pour notre projet de renovation electrique. Communication simple et equipe fiable.'),
    array('name' => 'Client vérifié', 'role' => 'Bienne', 'text' => 'Installation de borne de recharge rapide, conseils utiles et intervention professionnelle du debut a la fin.'),
);

$projects = array(
    array('title' => 'Renovation complete villa', 'city' => 'Reconvilier', 'text' => 'Reprise de l installation electrique, eclairage LED, tableau et commandes intelligentes.'),
    array('title' => 'Installation solaire + borne', 'city' => 'Bienne', 'text' => 'Production photovoltaïque, supervision et recharge vehicule electrique.'),
    array('title' => 'Maintenance immeuble locatif', 'city' => 'Moutier', 'text' => 'Depannages, conformites et optimisation des espaces communs.'),
);
?>

<main id="primary" class="site-main">
    <section class="bywa-hero">
        <div class="bywa-hero-media"></div>
        <div class="container bywa-hero-content">
            <div class="row align-items-center min-vh-100">
                <div class="col-xl-8 col-lg-10">
                    <span class="bywa-hero-kicker">ECO Electricite · Jura bernois & Bienne</span>
                    <h1>Votre electricien de confiance pour installations, energie et depannage.</h1>
                    <p>Une equipe locale de 5 a 6 professionnels pour vos projets electriques, photovoltaïques, bornes de recharge et maintenance, avec une approche claire, moderne et rassurante.</p>
                    <div class="bywa-hero-actions">
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>"><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_cta_label', 'Demander un devis')); ?></a>
                        <a class="bywa-btn bywa-btn-outline" href="<?php echo esc_url(bywa_eco_get_theme_mod('bywa_eco_cta_secondary_url', '/services')); ?>"><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_cta_secondary_label', 'Voir nos services')); ?></a>
                    </div>
                    <div class="bywa-hero-points">
                        <span><i class="bi bi-check-circle-fill"></i> Intervention locale</span>
                        <span><i class="bi bi-check-circle-fill"></i> Devis rapides</span>
                        <span><i class="bi bi-check-circle-fill"></i> Solutions sur mesure</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bywa-main-content">
        <section id="services" class="bywa-section bywa-services">
            <div class="container">
                <div class="bywa-section-head">
                    <span class="bywa-section-kicker">Services</span>
                    <h2>4 services cles mis en avant</h2>
                    <p>Section de demonstration du design. Plus tard, elle sera reliee au shortcode officiel <strong>[bywa_services]</strong>.</p>
                </div>
                <div class="row g-4">
                    <?php foreach ($services as $service) : ?>
                        <div class="col-md-6 col-xl-3">
                            <article class="bywa-service-card">
                                <div class="bywa-service-image"></div>
                                <div class="bywa-service-icon"><i class="bi <?php echo esc_attr($service['icon']); ?>"></i></div>
                                <div class="bywa-service-body">
                                    <h3><?php echo esc_html($service['title']); ?></h3>
                                    <p><?php echo esc_html($service['text']); ?></p>
                                    <a href="#" class="bywa-read-more">Voir plus <span class="bi bi-arrow-up-right"></span></a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <section class="bywa-section bywa-domains">
            <div class="container">
                <div class="bywa-section-head">
                    <span class="bywa-section-kicker">Domaines d activite</span>
                    <h2>Un rendu inspire de votre visuel de references</h2>
                    <p>Effet carte verticale avec grand visuel, overlay, numero et triangle au survol.</p>
                </div>
                <div class="row g-4">
                    <?php foreach ($domains as $domain) : ?>
                        <div class="col-md-6 col-xl-3">
                            <article class="bywa-domain-card" style="background-image: <?php echo esc_attr($domain['image']); ?>;">
                                <div class="bywa-domain-overlay"></div>
                                <div class="bywa-domain-number"><?php echo esc_html($domain['number']); ?></div>
                                <div class="bywa-domain-content">
                                    <h3><?php echo esc_html($domain['title']); ?></h3>
                                    <p><?php echo esc_html($domain['text']); ?></p>
                                    <a href="#" class="bywa-read-more">Lire plus <span class="bi bi-arrow-up-right"></span></a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="temoignages" class="bywa-section bywa-testimonials">
            <div class="container">
                <div class="bywa-section-head bywa-section-head-light">
                    <span class="bywa-section-kicker">Temoignages</span>
                    <h2>Une section citation moderne et horizontale</h2>
                </div>
                <div class="bywa-testimonials-track">
                    <?php foreach ($testimonials as $testimonial) : ?>
                        <article class="bywa-testimonial-card">
                            <div class="bywa-quote-shape"></div>
                            <div class="bywa-testimonial-inner">
                                <div class="bywa-quote-icon">&#10077;</div>
                                <p><?php echo esc_html($testimonial['text']); ?></p>
                                <h3><?php echo esc_html($testimonial['name']); ?></h3>
                                <span><?php echo esc_html($testimonial['role']); ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="realisations" class="bywa-section bywa-projects">
            <div class="container">
                <div class="bywa-section-head">
                    <span class="bywa-section-kicker">Realisations</span>
                    <h2>Proposition retenue : cartes projets avec grand visuel et focus local</h2>
                    <p>Cette section sera ensuite branchee sur le shortcode <strong>[bywa_realisations]</strong> avec un rendu carousel ou grid.</p>
                </div>
                <div class="row g-4">
                    <?php foreach ($projects as $project) : ?>
                        <div class="col-lg-4">
                            <article class="bywa-project-card">
                                <div class="bywa-project-thumb"></div>
                                <div class="bywa-project-body">
                                    <span class="bywa-project-city"><?php echo esc_html($project['city']); ?></span>
                                    <h3><?php echo esc_html($project['title']); ?></h3>
                                    <p><?php echo esc_html($project['text']); ?></p>
                                    <a href="#" class="bywa-read-more">Voir la realisation <span class="bi bi-arrow-up-right"></span></a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="presentation" class="bywa-section bywa-about">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="bywa-section-kicker">Entreprise</span>
                        <h2>Une presentation courte de l equipe</h2>
                        <p>ECO Electricite intervient avec une equipe a taille humaine basee a Reconvilier. Le design met en avant la proximite, la confiance et la clarte des prestations, avec une forte orientation devis et contact.</p>
                        <div class="bywa-about-list">
                            <div><strong>5 a 6</strong><span>personnes a plein temps</span></div>
                            <div><strong>Local</strong><span>Jura bernois / Bienne</span></div>
                            <div><strong>Rapide</strong><span>interventions et devis</span></div>
                        </div>
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(home_url('/entreprise')); ?>">Decouvrir l entreprise</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="bywa-about-visual">
                            <div class="bywa-about-photo"></div>
                            <div class="bywa-about-box">
                                <span>Adresse</span>
                                <strong><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_address', 'Rue de Bel-Air 22, 2732 Reconvilier')); ?></strong>
                                <span>Téléphone principal</span>
                                <strong><a href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone()); ?></a></strong>
                                <span>Téléphone secondaire</span>
                                <strong><a href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone_secondary())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone_secondary()); ?></a></strong>
                                <span>E-mail</span>
                                <strong><a href="mailto:<?php echo esc_attr(bywa_eco_get_contact_email()); ?>"><?php echo esc_html(bywa_eco_get_contact_email()); ?></a></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bywa-section bywa-final-cta">
            <div class="container">
                <div class="bywa-final-cta-box">
                    <div>
                        <span class="bywa-section-kicker">Besoin d un electricien ?</span>
                        <h2>Parlons de votre projet ou de votre depannage</h2>
                        <p>Le theme est pret pour une homepage moderne, responsive et orientee conversion.</p>
                    </div>
                    <div class="bywa-final-cta-actions">
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>">Demander un devis</a>
                        <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone())); ?>">Appeler maintenant</a>
                        <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone_secondary())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone_secondary()); ?></a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
