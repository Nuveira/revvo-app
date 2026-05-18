<?php
/**
 * Header Template
 * Includes: DOCTYPE, <head>, Tailwind config, custom styles, opening <body>
 * 
 * Usage: Set $pageTitle before including this file
 * Example: $pageTitle = "Dashboard"; include 'includes/header.php';
 */

$pageTitle = $pageTitle ?? 'REVVO | Workshop Management System';
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="src/images/logo.png">

    <!-- Tailwind CSS via CDN Play -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Plus+Jakarta+Sans:wght@400;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#D32F2F",
                        background: "#0c0a09",
                        surface: "#ffffff",
                        "stone-900": "#0c0a09",
                        "stone-800": "#1c1917",
                        "stone-700": "#292524",
                        "stone-400": "#a8a29e",
                        "stone-50": "#fafaf9",
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "1rem",
                        xl: "1.5rem",
                        "2xl": "2rem",
                        "3xl": "3rem",
                    },
                    spacing: {
                        "container-max": "1200px",
                        "margin-desktop": "64px",
                        "section-gap-desktop": "120px",
                        gutter: "24px",
                    },
                    fontFamily: {
                        headline: ["Bricolage Grotesque", "sans-serif"],
                        body: ["Plus Jakarta Sans", "sans-serif"],
                        technical: ["JetBrains Mono", "monospace"],
                    },
                    boxShadow: {
                        "glow-red": "0 0 20px rgba(211, 47, 47, 0.4)",
                    },
                },
            },
        }
    </script>

    <!-- Custom Styles -->
    <style>
        /* === Liquid Glass Navbar === */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }
        .navbar-glass-scrolled {
            background: rgba(12, 10, 9, 0.85);
            backdrop-filter: blur(24px) saturate(200%);
            -webkit-backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 
                0 12px 40px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        /* === Glass Cards === */
        .liquid-glass {
            background: rgba(28, 25, 23, 0.5);
            backdrop-filter: blur(16px) saturate(150%);
            -webkit-backdrop-filter: blur(16px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* === Gradients === */
        .mesh-gradient-red {
            background: radial-gradient(at 0% 0%, #D32F2F 0%, transparent 50%),
                        radial-gradient(at 100% 0%, #b71c1c 0%, transparent 50%),
                        radial-gradient(at 100% 100%, #d32f2f 0%, transparent 50%),
                        radial-gradient(at 0% 100%, #ff5252 0%, transparent 50%),
                        #D32F2F;
        }
        .grid-bg {
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .hero-gradient-overlay {
            background: linear-gradient(to bottom, rgba(12, 10, 9, 0.8) 0%, rgba(12, 10, 9, 0.4) 50%, rgba(12, 10, 9, 0.9) 100%);
        }

        /* === Interactions === */
        .mechanical-press:active {
            transform: scale(0.98);
        }
        .organic-shape {
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
        }

        /* === Fade-in Animations === */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(3deg); }
            50% { transform: translateY(-10px) rotate(-2deg); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(211, 47, 47, 0.4); }
            50% { box-shadow: 0 0 40px rgba(211, 47, 47, 0.7); }
        }

        /* Scroll-triggered animation classes */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), 
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .animate-on-scroll.from-left {
            transform: translateX(-40px);
        }
        .animate-on-scroll.from-left.visible {
            transform: translateX(0);
        }
        .animate-on-scroll.from-right {
            transform: translateX(40px);
        }
        .animate-on-scroll.from-right.visible {
            transform: translateX(0);
        }
        .animate-on-scroll.scale-in {
            transform: scale(0.9);
        }
        .animate-on-scroll.scale-in.visible {
            transform: scale(1);
        }

        /* Stagger delays */
        .delay-100 { transition-delay: 0.1s; }
        .delay-200 { transition-delay: 0.2s; }
        .delay-300 { transition-delay: 0.3s; }
        .delay-400 { transition-delay: 0.4s; }
        .delay-500 { transition-delay: 0.5s; }

        /* Float animation for decorative elements */
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Slow spin for gear */
        .animate-spin-slow {
            animation: spin 12s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Shimmer effect for buttons */
        .shimmer {
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }

        /* Pulse glow for CTA */
        .pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite;
        }

    </style>
</head>
<body class="bg-stone-900 text-white font-body overflow-x-hidden selection:bg-primary selection:text-white">
