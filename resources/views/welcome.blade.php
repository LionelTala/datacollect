<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataCollect | Plateforme de collecte de données collaborative</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Favicon -->
    @include('layouts.partials.favicon')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: white;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            padding: 1.5rem 0;
            transition: all 0.3s ease;
            background: transparent;
        }

        .navbar-scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3B82F6, #7C3AED);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
        }

        /* Hero section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 6rem 0 4rem;
            position: relative;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #1E293B;
        }

        .hero-title span {
            background: linear-gradient(135deg, #3B82F6, #7C3AED);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #64748B;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        /* Boutons */
        .btn-primary-custom {
            background: linear-gradient(135deg, #3B82F6, #7C3AED);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-outline-custom {
            background: transparent;
            color: #3B82F6;
            border: 2px solid #3B82F6;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-custom:hover {
            background: #3B82F6;
            color: white;
            transform: translateY(-2px);
        }

        /* Feature cards */
        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            transition: all 0.3s ease;
            border: 1px solid #E2E8F0;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: #3B82F6;
            box-shadow: 0 20px 30px -15px rgba(59, 130, 246, 0.15);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #EFF6FF, #F3E8FF);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 2rem;
            background: linear-gradient(135deg, #3B82F6, #7C3AED);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1E293B;
        }

        .section-subtitle {
            color: #64748B;
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        /* Stats */
        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3B82F6, #7C3AED);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* CTA */
        .cta-section {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 40px;
            padding: 4rem;
            margin: 4rem 0;
            color: white;
        }

        /* Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        /* Responsive mobile */
        @media (max-width: 991px) {
            .navbar-collapse {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: white;
                padding: 1.5rem;
                border-radius: 20px;
                margin: 0 1rem;
                box-shadow: 0 20px 30px -10px rgba(0,0,0,0.1);
                z-index: 1000;
            }

            .hero {
                padding-top: 100px;
                min-height: auto;
            }

            .hero-title {
                font-size: 2.2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .cta-section {
                padding: 2rem;
                margin: 2rem 0;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.8rem;
            }

            .btn-primary-custom, .btn-outline-custom {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="navbar">
        <div class="container">
            <a class="logo" href="/">DataCollect</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"
                    style="border: none; background: #F1F5F9; padding: 10px 15px; border-radius: 12px;">
                <i class="fas fa-bars" style="font-size: 1.3rem; color: #3B82F6;"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-3">
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#features" onclick="closeNavbar()">Fonctionnalités</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="#stats" onclick="closeNavbar()">Statistiques</a></li>
                    <li class="nav-item"><a class="btn btn-outline-custom" href="/login" onclick="closeNavbar()">Connexion</a></li>
                    <li class="nav-item"><a class="btn btn-primary-custom" href="/register" onclick="closeNavbar()">Commencer</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">
                        Collectez, analysez,<br>
                        <span>révolutionnez</span>
                        vos données
                    </h1>
                    <p class="hero-subtitle">
                        La plateforme collaborative qui transforme votre collecte de données en insights puissants.
                        Gérez vos formulaires, collaborez en équipe et exportez en toute simplicité.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/register" class="btn btn-primary-custom">
                            Démarrez gratuitement <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="#features" class="btn btn-outline-custom">
                            En savoir plus
                        </a>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i> Aucune carte bancaire requise
                        </small>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="float-animation text-center">
                        <i class="fas fa-chart-pie" style="font-size: 280px; color: #3B82F6; opacity: 0.15;"></i>
                        <i class="fas fa-chart-line" style="font-size: 180px; color: #7C3AED; position: absolute; margin-left: -180px; margin-top: 80px; opacity: 0.12;"></i>
                        <i class="fas fa-database" style="font-size: 120px; color: #10B981; position: absolute; margin-left: 100px; margin-top: -50px; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Une plateforme pensée pour les data analysts</h2>
                <p class="section-subtitle">Tout ce dont vous avez besoin pour une collecte de données efficace</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Analyses avancées</h4>
                        <p class="text-muted">Visualisez vos données en temps réel avec des graphiques interactifs et des statistiques détaillées.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Collaboration en temps réel</h4>
                        <p class="text-muted">Invitez votre équipe, définissez des rôles et travaillez ensemble sur vos collectes de données.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Export multi-format</h4>
                        <p class="text-muted">Exportez vos données en CSV, JSON, Excel pour une intégration facile avec vos outils.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Sécurité maximale</h4>
                        <p class="text-muted">Données chiffrées, sauvegardes automatiques et conformité RGPD.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Formulaires intelligents</h4>
                        <p class="text-muted">Créez des formulaires dynamiques avec validation automatique et prétraitement des fichiers.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Prétraitement AI</h4>
                        <p class="text-muted">Traitement automatique des images et audios pour l'entraînement de vos modèles.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-5" style="background: #F8FAFE;">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">10K+</div>
                        <p class="text-muted mt-2">Données collectées</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <p class="text-muted mt-2">Projets actifs</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">98%</div>
                        <p class="text-muted mt-2">Satisfaction client</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <p class="text-muted mt-2">Support disponible</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container">
        <div class="cta-section text-center">
            <h2 class="fw-bold mb-3" style="color: white;">Prêt à transformer vos données ?</h2>
            <p class="mb-4 opacity-75">Rejoignez des centaines d'analystes qui utilisent DataCollect au quotidien</p>
            <a href="/register" class="btn btn-primary-custom" style="background: white; color: #3B82F6; box-shadow: none;">
                Commencer maintenant <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 mt-5" style="background: #F8FAFE;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="fw-bold mb-3">DataCollect</h4>
                    <p class="text-muted">La plateforme moderne de collecte et d'analyse de données collaboratives.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold">Produit</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-muted text-decoration-none">Fonctionnalités</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Tarifs</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">API</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold">Ressources</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Documentation</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Support</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold">Suivez-nous</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-twitter fa-xl"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin fa-xl"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-github fa-xl"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted small">
                © 2024 DataCollect. Tous droits réservés.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Fermer le menu mobile
        function closeNavbar() {
            const navbar = document.getElementById('navbarNav');
            if (navbar && navbar.classList.contains('show')) {
                navbar.classList.remove('show');
            }
        }

        // Fermer le menu quand on clique ailleurs
        document.addEventListener('click', function(event) {
            const navbar = document.getElementById('navbarNav');
            const toggler = document.querySelector('.navbar-toggler');

            if (navbar && navbar.classList.contains('show') && toggler &&
                !toggler.contains(event.target) && !navbar.contains(event.target)) {
                navbar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
