<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitItems - Il Tuo Shop di Oggetti Virtuali</title>
    <style>
        :root {
            --primary: #9c27b0;
            --primary-light: #bb86fc;
            --secondary: #03dac6;
            --background: #121212;
            --surface: #1e1e1e;
            --surface-light: #2d2d2d;
            --on-primary: #ffffff;
            --on-background: #ffffff;
            --on-surface: #ffffff;
            --error: #cf6679;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--background);
            color: var(--on-background);
        }

        /* Header Styling */
        header {
            background-color: var(--surface);
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--primary-light);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo span {
            color: var(--secondary);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            background-color: var(--primary-light);
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-link {
            color: var(--on-surface);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-light);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary-light);
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .auth-links {
            display: flex;
            gap: 1rem;
        }

        .auth-btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .login-btn {
            background-color: transparent;
            color: var(--on-surface);
            border: 1px solid var(--primary-light);
        }

        .login-btn:hover {
            background-color: rgba(187, 134, 252, 0.1);
        }

        .register-btn {
            background-color: var(--primary);
            color: var(--on-primary);
            border: none;
        }

        .register-btn:hover {
            background-color: var(--primary-light);
            box-shadow: 0 2px 8px rgba(187, 134, 252, 0.4);
        }

        /* Hero Section */
        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-content {
            flex: 1;
            padding-right: 2rem;
        }

        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-title span.highlight {
            color: var(--primary-light);
        }

        .hero-title span.highlight-secondary {
            color: var(--secondary);
        }

        .hero-description {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2rem;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--on-primary);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            box-shadow: 0 2px 8px rgba(187, 134, 252, 0.4);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--on-surface);
            border: 2px solid var(--secondary);
            color: var(--secondary);
        }

        .btn-outline:hover {
            background-color: rgba(3, 218, 198, 0.1);
            box-shadow: 0 2px 8px rgba(3, 218, 198, 0.4);
            transform: translateY(-2px);
        }

        .hero-image {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .game-icon {
            background-color: var(--surface-light);
            border-radius: 12px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }

        .game-icon:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(187, 134, 252, 0.3);
            border-color: var(--primary-light);
        }

        .game-icon svg {
            width: 50px;
            height: 50px;
            fill: none;
            stroke: var(--primary-light);
            stroke-width: 1.5;
        }

        .game-icon:nth-child(even) svg {
            stroke: var(--secondary);
        }

        /* Mobile account section */
        .mobile-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--surface);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background-color: var(--surface-light);
            border-radius: 50%;
            margin-right: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .user-details {
            line-height: 1.4;
        }

        .username {
            font-weight: bold;
        }

        .balance {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        /* Mobile Nav */
        .mobile-nav {
            display: flex;
            background-color: var(--surface);
            padding: 0.75rem 1rem;
            justify-content: space-between;
            align-items: center;
        }

        .menu-btn {
            background: none;
            border: none;
            color: var(--on-surface);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .sidebar {
            width: 25%;
            background-color: var(--surface);
            height: 100vh;
            padding: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                padding: 2rem 1rem;
            }

            .hero-content {
                padding-right: 0;
                margin-bottom: 2rem;
            }

            .hero-image {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-links, .auth-links {
                display: none;
            }

            .sidebar {
                width: 70%;
            }
        }
    </style>
</head>
<body>
    <!-- Desktop Header -->
    <header>
        <div class="nav-container">
            <a href="#" class="logo">
                <div class="logo-icon">B</div>
                Bit<span>Items</span>
            </a>
            <nav class="nav-links">
                <a href="#" class="nav-link">Home</a>
                <a href="#" class="nav-link">Giochi</a>
                <a href="#" class="nav-link">Oggetti Virtuali</a>
                <a href="#" class="nav-link">Top Vendite</a>
            </nav>
            <div class="auth-links">
                <a href="#" class="auth-btn login-btn">Accedi</a>
                <a href="#" class="auth-btn register-btn">Registrati</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">
                Benvenuto nel mondo degli <span class="highlight">Oggetti Virtuali</span> di <span class="highlight-secondary">BitItems</span>
            </h1>
            <p class="hero-description">
                Esplora la nostra collezione esclusiva di oggetti digitali per i tuoi giochi preferiti. Dai skin rari agli oggetti leggendari, tutto ciò che serve per migliorare la tua esperienza di gioco.
            </p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Esplora Catalogo</a>
                <a href="#" class="btn btn-outline">Chi Siamo</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="game-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M6 9h4V6h4v3h4v4h-4v3h-4v-3H6V9z"/>
                    <rect x="2" y="2" width="20" height="20" rx="10"/>
                </svg>
            </div>
            <div class="game-icon">
                <svg viewBox="0 0 24 24">
                    <rect x="2" y="6" width="20" height="12" rx="3"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <line x1="12" y1="6" x2="12" y2="18"/>
                </svg>
            </div>
            <div class="game-icon">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="12" r="3"/>
                    <line x1="12" y1="2" x2="12" y2="5"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                    <line x1="2" y1="12" x2="5" y2="12"/>
                    <line x1="19" y1="12" x2="22" y2="12"/>
                </svg>
            </div>
            <div class="game-icon">
                <svg viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="10" rx="2"/>
                    <circle cx="6" cy="12" r="2"/>
                    <circle cx="18" cy="12" r="2"/>
                </svg>
            </div>
            <div class="game-icon">
                <svg viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <circle cx="8" cy="12" r="2"/>
                    <circle cx="16" cy="12" r="2"/>
                </svg>
            </div>
            <div class="game-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2L2 7v10l10 5 10-5V7l-10-5z"/>
                    <path d="M12 12L2 7"/>
                    <path d="M12 12l10-5"/>
                    <path d="M12 12v10"/>
                </svg>
            </div>
        </div>
    </section>