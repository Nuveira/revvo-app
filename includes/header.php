<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/app.php';
}
$pageTitle = $pageTitle ?? 'REVVO | Workshop Management System';
?>
<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/logo.png') ?>">

    <!-- tailwind + fonts + icons -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=Plus+Jakarta+Sans:wght@400;600;700&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- tailwind config -->
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

    <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
</head>
<body class="bg-stone-900 text-white font-body overflow-x-hidden selection:bg-primary selection:text-white">
