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
};
