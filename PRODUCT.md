# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primary: people with disabilities and their caregivers planning a real trip in Jakarta & Tangerang — wheelchair users, blind and low-vision people, Deaf and hard-of-hearing people, and elderly travelers. Their situation: they are about to leave (or are deciding whether to leave) for a station, mall, hospital, café, park, or public facility, and they need to know *before arriving* whether the physical environment will actually accommodate them. The job: "tell me honestly whether this place will work for me, so I can go with confidence — or choose somewhere else."

Secondary: everyday volunteer contributors (the general public, disability-community members, students) who observe accessibility conditions in the field and submit photo reports.

Tertiary: admins who moderate and verify reports and manage place data.

## Product Purpose

AksesKita maps the accessibility of public facilities across Jakarta and Tangerang — ramps, lifts, accessible toilets, guiding blocks (ubin pemandu), wide doors, and Braille lift buttons — and scores each place 0–100 with a citizen-reportable, admin-verified honesty model. It exists so that a person planning a journey stops guessing: no more phone calls, no more arriving to find stairs where the map promised a ramp. Success means a disabled traveler opens AksesKita, reads a place's score and feature checklist, and makes a confident, informed decision to go.

## Positioning

A citizen-powered accessibility map for the two cities, where every score is grounded in real field observations by residents and confirmed by a human verifier before it publishes. The mechanism a neighboring product could not truthfully copy: the reported condition comes from people who live the barrier, and the map is explicitly honest about partial access (a "yellow / limited" tier), not a binary accessible/inaccessible claim.

## Operating Context

- Bright, high-contrast, low-light and outdoor real-world use: people check the map on a phone at street level, in sunlight, at a station entrance, or with a screen reader while moving.
- The three-tier vocabulary is core: Green (Ramah / friendly), Yellow (Terbatas / limited), Red (Berbahaya / unsafe).
- Six accessibility features are checked per place: ramp, lift, accessible toilet, guiding block, wide door, Braille lift buttons.
- Categories: stasiun, mal, rumah sakit, kafe, taman, fasilitas publik.
- Modes the product lets a visitor simulate: general, wheelchair, blind/low-vision, elderly.
- Admin verification gates all public data; reports have pending/approved/rejected states.

## Capabilities and Constraints

Confirmed: animated narrative landing page; full-screen interactive Leaflet map with status filtering; place detail pages with per-feature checklists; citizen report submission with photo evidence; admin moderation (approve/reject); admin place CRUD; role-gated user/admin dashboards. Stack is fixed: Laravel 12, Blade, Tailwind CSS v4 (`@theme` tokens in `resources/css/app.css`), Alpine.js, Leaflet.js, Vite. Database is local MySQL; 20 sample places are seeded (16 green).

Constraint: the redesign must stay within this stack and keep all existing routes, controllers, models, and data contracts working.

## Brand Commitments

- Name: **AksesKita** ("Our Access"). It renders as `Akses` + accent-coloured `Kita`.
- Language and voice: Indonesian, warm, plain-spoken, human ("dari warga untuk warga", "mata & telinga kota yang inklusif"). The product speaks *with* disabled users, never down to them.
- Existing tagline concept: "Kemana pun kamu pergi, harus bisa diakses semua."
- The three-tier colour vocabulary (green/yellow/red) is a fixed product fact and must remain legible and consistent.

## Evidence on Hand

- Live database with 20 seeded places across Jakarta & Tangerang and real per-feature accessibility data (`PlaceSampleSeeder.php`, `App\Models\Place`).
- Existing status-colour and category taxonomy in `App\Models\Place`.
- Custom Leaflet helper in `public/assets/js/leaflet-map.js` (base tiles, `addPlaces`, custom pin markers).
- No real photography, logos-as-assets, testimonials, press, or partner logos exist. Future work must NOT fabricate customer names, percentages, or endorsements. Any illustrative imagery must be authored as clearly synthetic design material.

## Product Principles

1. **Legibility is the accessibility.** If the map, score, or checklist isn't instantly readable — visually regardless of vision, hit-target-size regardless of motor ability — the product has failed at its core promise.
2. **Honest about partial access.** Never flatten Yellow into "fine". Nuance is the product's integrity.
3. **Dignity, not pity.** The disabled traveler is the protagonist with agency, never a poster for sympathy.
4. **Citizen-grounded truth.** Every fact traces to a real observation; the design foregrounds the people who report.
5. **Confidence before departure.** The product's whole value is felt in the moment *before* someone leaves.

## Accessibility & Inclusion

Target: **WCAG 2.2 Level AA**, with strong extras. Requirements set by the user for this redesign:

- AA contrast everywhere; AAA contrast (7:1) on primary body and key text.
- Minimum 44×44px interactive targets.
- Keyboard-first: visible `:focus-visible` rings, logical order, skip link, full keyboard operability of every control (including the map's alternatives).
- `prefers-reduced-motion` honoured; motion is never required to reach content.
- Screen-reader landmarks, labelled regions, `aria-live` for dynamic status, descriptive alt/labels.
- A user-toggleable **high-contrast** mode and a **dyslexia-friendly / larger-text** comfort option.
- Never rely on colour alone to convey the green/yellow/red status — pair every colour with text and a distinct shape/symbol.
- Plain Indonesian language; controls name their action; errors name the problem and the recovery.
