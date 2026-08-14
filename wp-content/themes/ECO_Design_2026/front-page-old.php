<?php
get_header();

$services = array(
    array('icon' => 'bi-lightning-charge-fill', 'title' => 'Instalații electrice', 'text' => 'Instalații noi, renovări și puneri în conformitate pentru locuințe, spații comerciale și clădiri tehnice.'),
    array('icon' => 'bi-sun-fill', 'title' => 'Fotovoltaic', 'text' => 'Soluții solare performante pentru a reduce costurile și a valorifica proprietatea ta în Elveția romandă.'),
    array('icon' => 'bi-ev-front-fill', 'title' => 'Stații de încărcare', 'text' => 'Instalare de stații pentru persoane fizice, PPE, companii și parcări.'),
    array('icon' => 'bi-house-gear-fill', 'title' => 'Domotică & mentenanță', 'text' => 'Casă inteligentă, comenzi, intervenții rapide și întreținerea instalațiilor existente.'),
);

$domains = array(
    array('number' => '01', 'title' => 'Locuințe', 'text' => 'Vile, apartamente, PPE și renovări complete.', 'image' => 'linear-gradient(160deg, rgba(13,24,26,.58), rgba(13,24,26,.1)), url(https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80)'),
    array('number' => '02', 'title' => 'Imobile & administratori', 'text' => 'Tablouri, spații comune, mentenanță preventivă și intervenții planificate.', 'image' => 'linear-gradient(160deg, rgba(84,185,71,.72), rgba(84,185,71,.35)), url(https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1200&q=80)'),
    array('number' => '03', 'title' => 'Magazine & birouri', 'text' => 'Iluminat, rețele, prize, siguranță și optimizare energetică.', 'image' => 'linear-gradient(160deg, rgba(13,24,26,.58), rgba(13,24,26,.1)), url(https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80)'),
    array('number' => '04', 'title' => 'Smart home & energie', 'text' => 'Control, fotovoltaic, încărcare și automatizări conectate.', 'image' => 'linear-gradient(160deg, rgba(13,24,26,.58), rgba(13,24,26,.1)), url(https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=1200&q=80)'),
);

$testimonials = array(
    array('name' => 'Client verificat', 'role' => 'Reconvilier', 'text' => 'Companie serioasă, reactivă și foarte curată pe șantier. Oferta a fost clară, iar lucrarea executată impecabil.'),
    array('name' => 'Client verificat', 'role' => 'Tavannes', 'text' => 'Suport foarte bun pentru proiectul nostru de renovare electrică. Comunicarea a fost simplă, iar echipa de încredere.'),
    array('name' => 'Client verificat', 'role' => 'Bienne', 'text' => 'Instalare rapidă a unei stații de încărcare, sfaturi utile și intervenție profesionistă de la început până la final.'),
);

$projects = array(
    array('title' => 'Renovare completă vilă', 'city' => 'Reconvilier', 'text' => 'Refacerea instalației electrice, iluminat LED, tablou și comenzi inteligente.'),
    array('title' => 'Instalație solară + stație', 'city' => 'Bienne', 'text' => 'Producție fotovoltaică, monitorizare și încărcare vehicul electric.'),
    array('title' => 'Mentenanță imobil locativ', 'city' => 'Moutier', 'text' => 'Intervenții, conformitate și optimizarea spațiilor comune.'),
);
?>

<main id="primary" class="site-main">
    <section class="bywa-hero">
        <div class="bywa-hero-media"></div>
        <div class="container bywa-hero-content">
            <div class="row align-items-center min-vh-100">
                <div class="col-xl-8 col-lg-10">
                    <span class="bywa-hero-kicker">ECO Electricite · Jura bernois & Bienne</span>
                    <h1>Electricianul tău de încredere pentru instalații, energie și intervenții.</h1>
                    <p>O echipă locală de 5 până la 6 profesioniști pentru proiectele tale electrice, fotovoltaice, stații de încărcare și mentenanță, cu o abordare clară, modernă și liniștitoare.</p>
                    <div class="bywa-hero-actions">
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>"><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_cta_label', 'Cere o ofertă')); ?></a>
                        <a class="bywa-btn bywa-btn-outline" href="<?php echo esc_url(bywa_eco_get_theme_mod('bywa_eco_cta_secondary_url', '/services')); ?>"><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_cta_secondary_label', 'Vezi serviciile')); ?></a>
                    </div>
                    <div class="bywa-hero-points">
                        <span><i class="bi bi-check-circle-fill"></i> Intervenție locală</span>
                        <span><i class="bi bi-check-circle-fill"></i> Oferte rapide</span>
                        <span><i class="bi bi-check-circle-fill"></i> Soluții personalizate</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bywa-main-content">
        <section id="services" class="bywa-section bywa-services">
            <div class="container">
                <div class="bywa-section-head">
                    <span class="bywa-section-kicker">Servicii</span>
                    <h2>4 servicii-cheie evidențiate</h2>
                    <p>Secțiune de demonstrație a designului. Mai târziu, va fi conectată la shortcode-ul oficial <strong>[bywa_services]</strong>.</p>
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
                                    <a href="#" class="bywa-read-more">Vezi mai mult <span class="bi bi-arrow-up-right"></span></a>
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
                    <span class="bywa-section-kicker">Domenii de activitate</span>
                    <h2>Un randament inspirat de vizualul tău de referință</h2>
                    <p>Efect de card vertical cu vizual mare, overlay, număr și triunghi la hover.</p>
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
                                    <a href="#" class="bywa-read-more">Citește mai mult <span class="bi bi-arrow-up-right"></span></a>
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
                    <span class="bywa-section-kicker">Mărturii</span>
                    <h2>O secțiune de citate modernă și orizontală</h2>
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
                    <span class="bywa-section-kicker">Lucrări</span>
                    <h2>Propunere reținută: carduri de proiect cu vizual mare și accent local</h2>
                    <p>Această secțiune va fi apoi conectată la shortcode-ul <strong>[bywa_realisations]</strong> cu afișare tip carusel sau grilă.</p>
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
                                    <a href="#" class="bywa-read-more">Vezi lucrarea <span class="bi bi-arrow-up-right"></span></a>
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
                        <span class="bywa-section-kicker">Companie</span>
                        <h2>O scurtă prezentare a echipei</h2>
                        <p>ECO Electricite lucrează cu o echipă de dimensiuni umane, bazată în Reconvilier. Designul pune accent pe proximitate, încredere și claritatea serviciilor, cu orientare puternică spre ofertă și contact.</p>
                        <div class="bywa-about-list">
                            <div><strong>5 până la 6</strong><span>persoane full-time</span></div>
                            <div><strong>Local</strong><span>Jura bernois / Bienne</span></div>
                            <div><strong>Rapid</strong><span>intervenții și oferte</span></div>
                        </div>
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(home_url('/entreprise')); ?>">Descoperă compania</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="bywa-about-visual">
                            <div class="bywa-about-photo"></div>
                            <div class="bywa-about-box">
                                <span>Adresă</span>
                                <strong><?php echo esc_html(bywa_eco_get_theme_mod('bywa_eco_address', 'Rue de Bel-Air 22, 2732 Reconvilier')); ?></strong>
                                <span>Telefon principal</span>
                                <strong><a href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone()); ?></a></strong>
                                <span>Telefon secundar</span>
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
                        <span class="bywa-section-kicker">Ai nevoie de un electrician?</span>
                        <h2>Hai să discutăm despre proiectul sau intervenția ta</h2>
                        <p>Tema este pregătită pentru o pagină principală modernă, responsive și orientată spre conversie.</p>
                    </div>
                    <div class="bywa-final-cta-actions">
                        <a class="bywa-btn bywa-btn-primary" href="<?php echo esc_url(bywa_eco_get_contact_url()); ?>">Cere o ofertă</a>
                        <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone())); ?>">Sună acum</a>
                        <a class="bywa-btn bywa-btn-outline-dark" href="<?php echo esc_url(bywa_eco_get_tel_link(bywa_eco_get_contact_phone_secondary())); ?>"><?php echo esc_html(bywa_eco_get_contact_phone_secondary()); ?></a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
