# Design Brief — REVVO Landing Page

A landing page for REVVO, a workshop management system for Indonesian motorcycle bengkels. The page sells the product to bengkel owners — the people who'd actually pay for this.

## What This Page Has to Do

A bengkel owner — usually a guy in his 40s, runs the place himself, knows everything about motors but is tired of writing booking di buku tulis — lands here from Instagram ad or a friend's recommendation. He has maybe 90 seconds before he gets pulled away by a customer or a phone call.

In those 90 seconds we need to make him:
1. Understand what REVVO is (a system to handle his bengkel)
2. Recognize his own problem in our headline
3. Trust that we get the workshop world (not some random SaaS pitch)
4. Take one action — either request demo, see pricing, or save the link

That's it. No funnel of 14 sections, no "Join 10,000+ businesses" lie when we have 0, no scroll-jacked parallax animations that crash his Vivo phone.

## The Vibe

Confident. Industrial. Slightly cocky in a way that says "we built this for you, not for VCs in Silicon Valley."

Closest reference is the Linear landing page — bold type, restraint, dark hero, lots of breathing room — but warmer. Linear is cold tech. REVVO is warm tech. We're talking to a man who has motor oil under his fingernails, not a CTO in Singapore.

The aesthetic carries over from the app itself: amber on stone, industrial-precision warmth, mechanical typography moments. But amplified. The app has to disappear into work. The landing page has to grab attention.

## Brand Name Treatment

REVVO — written in all caps when used as a brand mark. The double-V matters. In the logo, the two Vs can be styled to evoke a tachometer needle or a motorcycle fork at certain weights, but don't make it cute or literal. No spinning gear animations. No revving sound effects on hover.

The wordmark should work in pure typography — Bricolage Grotesque ExtraBold, tight tracking, maybe a 2-3 degree forward italic to suggest motion. That's it. No icon needed for the primary mark.

Tagline goes underneath in JetBrains Mono, smaller, all-caps with letter-spacing: "WORKSHOP OS FOR MOTORCYCLES" or "SISTEM BENGKEL YANG NGERTI BENGKEL" depending on audience.

## Color

Dark hero. The fold should be stone-900 (#1c1917) background with amber-500 (#f59e0b) as the single point of focus — the brand mark, the primary CTA, one accent line. White text for the headline, stone-400 for the supporting copy.

After the fold, transition to stone-50 (#fafaf9) for the rest of the page. Don't make every section dark — that's a 2018 SaaS thing. Alternate light sections with stone-50 and white. Use stone-900 panels strategically for high-impact moments (testimonial quote, CTA section near footer).

Amber is the only chromatic color we use prominently. Everything else stays neutral. Status colors from the app (blue, green, red) can appear inside product screenshots, but never as page accent colors.

## Typography

Same three typefaces from the app. Carry the system over.

Bricolage Grotesque for hero headline (60-80px desktop, 40-48px mobile), section headers (32-40px), big numbers in stats.

Plus Jakarta Sans for body copy, buttons, navigation.

JetBrains Mono for taglines, technical detail labels, version numbers, anything that needs "system" energy.

The hero headline should be huge. Don't be shy. 72px+ on desktop, tracked tight (-0.03em), weight 700. The headline does 80% of the conversion work — give it the space.

Body copy at 16-17px for landing page (not 14px like the app — landing reads differently than scanning). Line-height 1.6 for paragraphs.

## Page Structure

Seven sections. In order.

Hero (above fold). Dark background. Tiny nav at top — REVVO wordmark left, "Login" + "Mulai Gratis" right. No bloated nav with 8 menu items. The page itself is the navigation. Headline center-anchored or left-aligned. One headline, one sub-headline (1 line, max 12 words), one primary CTA button (amber, bold), one secondary text link below ("Lihat demo →"). Below the headline group, a single product screenshot — the booking list or admin dashboard from the app, presented in a stylized device frame or just floating with a subtle gradient mask at the bottom. Don't show 3 floating screenshots arranged isometrically. One screenshot, big, confident.

The Problem (single section, no header). A single bold statement that the bengkel owner reads and thinks "yeah, ini gue." Either a wall of one big paragraph or a 3-card layout where each card describes a pain point in their actual words. Use language from the wawancara — "lupa booking", "double-booking pas hari Sabtu", "ngitung revenue di kalkulator HP". Avoid generic SaaS pain points like "boost efficiency" or "streamline operations."

The Solution — Three Features. Not 12 features. Three. Each one solves one of the pain points from section 2.
- Booking online + tracking real-time
- Auto-kurang stok sparepart + alert
- Laporan bulanan otomatis (PDF + Excel)

Each feature gets equal space. Big icon (lucide, 40px), feature name (Bricolage 24px), short description (2 lines max), and a tiny visual proof — could be a cropped screenshot or a UI element of that feature in isolation.

How It Works. A 3-step flow. Numbered cards (01, 02, 03 in JetBrains Mono, large, muted). Step name, one-line description. Maybe a thin connecting line between cards to suggest progression. No flowchart aesthetic — keep it editorial.
- 01 — Daftar bengkelmu (5 menit)
- 02 — Customer booking online
- 03 — Kelola dari satu dashboard

Social Proof. Tricky for a UAS project — we don't have real customers yet. Options: skip this section if no real data, don't fake testimonials. Use mockup quotes attributed to a "Bengkel Andalan, Karawang" type generic name only if 100% honest. Or replace with a stats section using realistic numbers from the dummy data demo. If kept, make it ONE testimonial, large, with the quote in display type. Not a carousel of 6. One quote that's actually good is worth more than 6 generic ones.

Pricing or CTA Block. For a UAS project, pricing might not apply. Replace with a "Coba Demo" CTA section — dark stone-900 panel, large headline, amber button, single line of supporting copy. If pricing is needed: 2-3 tiers max. Card per tier. Highlight the middle one. Don't compare 14 features in a table — that's enterprise software thinking.

Footer. Minimal. Three columns: about, product, contact. REVVO wordmark on the left. No newsletter signup unless we'll actually send emails. No social icons unless those accounts exist. Copyright line at the bottom in muted text. Year, REVVO, "Made in Indonesia."

## Specific Components

The Hero CTA Button. Amber-500 background, white text, 16px font weight 600, padding 14px 28px, 8px radius, subtle 1px inner highlight on top for that mechanical-button feel. Hover state shifts to amber-400 + 2px translate-y down (like a physical button press). No glow, no gradient, no shimmer effect.

Section padding. Generous. 120px+ vertical between sections on desktop. Don't cram. Indonesian landing pages tend to over-pack — we do the opposite.

Container width. Max 1200px content, 64px horizontal padding on desktop, 24px on mobile. Hero can break out wider for impact.

Borders & dividers. Use sparingly. A 1px stone-200 border under sections is fine. No big decorative dividers.

Subtle motion. When sections come into view, soft fade-up animation (10-20px translateY, 400ms ease). Run once. Don't loop. Hero text can have a 1-frame stagger on the first load. That's all the motion we need.

## Imagery

Product screenshots. Use actual screenshots from the app. Frame them in a simple stylized browser chrome (just three colored circles top-left, no URL bar) or float them with a soft shadow + subtle reflection. Don't use 3D mockups of MacBooks unless we have a designer who can pull it off without looking like a Behance template.

Photography. Only if we have access to real bengkel photos. Stock photos of generic mechanics in coveralls are worse than no photo. If we use photos, they should feel candid — a real Indonesian bengkel, a real mekanik (with permission), maybe a Honda or Yamaha because that's what's actually being serviced here. Black & white treatment can save weak photos but use the trick once, not throughout.

Illustrations. Skip them. Don't add generic "person at laptop with floating UI elements" SaaS illustrations. We're not Notion. We're a workshop OS. Let the typography and product screenshots carry the page.

## Voice & Copy

Indonesian first. The audience is Indonesian bengkel owners. English copy is a red flag for them — feels like we're targeting Singapore enterprises.

Casual but respectful. "Bengkelmu" not "Bengkel Anda" in body copy. "Bos" or "Pak" feel too colloquial for a brand voice — find middle ground.

Headlines can be punchy. Try:
- "Bengkelmu butuh sistem. Bukan buku tulis lagi."
- "Setiap motor punya history. Sekarang ada tempatnya."
- "Atur booking, mekanik, sparepart, dan laporan — dari satu layar."

Avoid:
- "Revolutionary platform" type claims
- "All-in-one" — every SaaS says this
- "AI-powered" unless there's actual AI
- Statistics we can't back up
- "Join thousands of bengkels" when we have zero

Buttons stay short. "Mulai Gratis", "Lihat Demo", "Hubungi Kami". Not "Get Started With REVVO Today".

## What to Avoid

No infinite scroll. No "scroll for more" indicators. The page ends, there's a footer, that's it.

No chatbot bubble in the corner. We're not running a 24/7 support team.

No animated counter that goes from 0 to 5,000 when scrolled into view. We don't have 5,000 anything.

No "Backed by Y Combinator" badges if we're not. No fake VC backing logos.

No Lottie animations that take 200KB to render a tiny floating shape.

No carousel of customer logos that auto-scrolls. If logos matter, show 5 of them as a static row.

No floating CTA that follows the user as they scroll. Trust them to scroll back up if they want to convert.

No video background in the hero. It's heavy, it's distracting, and bengkel internet in Karawang doesn't need that load.

No "We use cookies" banner that takes up half the screen. One small bottom bar, dismissable, that's all.

## Mobile

This page will probably get most of its traffic from mobile. Don't treat mobile as the afterthought.

Hero headline: 40-48px, still bold and big. Single-column everything below the fold. The screenshot in hero scales to 90% viewport width, slight horizontal scroll on mobile is fine if needed. CTA buttons full-width on mobile. Footer collapses to 1 column.

Test on a low-end Android with throttled 3G. If it doesn't feel instant, cut something.

## Reference Points

What we want to feel like:
- Linear (linear.app) — bold type, restraint, dark hero done right
- Resend (resend.com) — confident, brand-forward, clean
- Vercel (vercel.com) — amber accent done well, typography hierarchy
- Stripe Press (press.stripe.com) — editorial, considered, respects the reader

What we don't want to feel like:
- Generic Bootstrap SaaS landing (every "Premium HTML Template")
- 2015 startup landing with floating illustrations and gradient hero
- Indonesian fintech aggressive landing pages with 17 sections
- Anything with "Powered by [Stack]" badges at the bottom

## The Test

A bengkel owner lands on the page on mobile, scrolls once with his thumb, and either says "menarik nih, gue mau coba" or "ah, paham." Not "wow keren nih animasinya."

If he can summarize what REVVO does to his friend in one sentence after closing the tab, we did it right.
