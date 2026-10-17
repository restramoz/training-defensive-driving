# PLAN.md --- Pelatihan P3K Website

**Target project:** `/root/pelatihan-p3k`\
**Reference project:** `/root/trainingkota` (read-only)\
**Execution environment:** Google Antigravity\
**Language:** Bahasa Indonesia\
**Design direction:** Light Premium K3

## 1. Project goal

Build a complete website for the P3K training service by reusing and
adapting the existing homepage, city-page, district-page patterns,
routes, and regional data from TrainingKota. Maximize the existing
implementation and data; do not restart from scratch or repeat broad
audits without a concrete reason.

The initial scope is exactly three page types: 1. Homepage 2. City page
3. District page

## 2. Project boundaries

-   Make all changes only in `/root/pelatihan-p3k`.
-   Treat `/root/trainingkota` as a read-only reference. Never modify
    its files.
-   Copy/adapt relevant source implementation into the P3K project; do
    not create runtime dependencies between the two projects.
-   Preserve the target project's existing Laravel, Blade, Vite, and
    Tailwind CSS v4 architecture.
-   Do not install dependencies, change package versions, edit `.env`,
    alter database schema, deploy, or push without explicit approval.
-   Do not fabricate prices, schedules, phone numbers, addresses,
    testimonials, statistics, certifications, accreditations, trainer
    credentials, client logos, or business guarantees.

## 3. Existing data and reuse strategy

The regional dataset already exists in the P3K project as
`master_wilayah_bps.json`, previously reported as approximately 7,288
rows. Reuse this file and its region hierarchy. Do not ask the user to
provide the data again or replace it with another dataset without
evidence of a concrete problem.

Use the TrainingKota project as the reference for: - Homepage structure
and reusable UI patterns. - City-page and district-page templates. -
Route patterns and region lookup logic. - Breadcrumbs, internal regional
links, and SEO patterns. - Existing business copy only where it
accurately applies to P3K training.

Inspect only files needed to locate these patterns. Treat them as
read-only. Adapt them inside `/root/pelatihan-p3k`. If a source feature
depends on a missing package, first seek a solution with the existing
stack; do not install packages without approval.

## 4. Homepage requirements

Implement a full marketing homepage, not just a hero section:

1.  **Header/navigation:** clear brand treatment, responsive
    desktop/mobile navigation, working section anchors, and a
    consultation CTA only if a real destination is configured.
2.  **Hero:** clear Indonesian headline, concise value proposition, CTA
    hierarchy, relevant visual composition, and no excessive empty
    space.
3.  **Benefits:** credible practical benefits for organizations and
    workplace teams.
4.  **Training topics:** show confirmed topics, or clearly label
    provisional examples pending curriculum confirmation.
5.  **Target audience:** explain who may benefit without unsupported
    legal eligibility claims.
6.  **Process:** consultation, needs discussion, scope/schedule/price
    confirmation, and training delivery after confirmation---adjust to
    confirmed business processes.
7.  **Regional coverage:** introduce coverage and link to valid
    city/district pages. Do not imply physical presence or guaranteed
    delivery in every region.
8.  **FAQ:** questions about audience, scope, customization, scheduling,
    pricing, and inquiries. Do not invent fixed terms.
9.  **Final CTA:** encourage consultation with a verified contact
    destination. If none exists, use a clearly identified
    pending/unavailable state.
10. **Footer:** only verified business identity/contact details, plus
    useful navigation.

## 5. City page requirements

Reuse the source city-page pattern and data-driven hierarchy.

-   Dynamic city name in title, H1, and relevant copy.
-   Parent province/regional context from actual data.
-   Unique, accurate title and meta description.
-   Explain that organizations can inquire about P3K training needs for
    the city.
-   Breadcrumb: Homepage → City.
-   Link to valid districts in that city where supported by the dataset.
-   Link back to the homepage and related regional pages.
-   Return a proper 404 for invalid or unresolved city slugs.
-   Never invent local offices, trainers, clients, facilities,
    testimonials, or delivery guarantees.
-   Avoid indexing near-empty or invalid region pages.

## 6. District page requirements

Reuse the source district-page pattern and data hierarchy.

-   Dynamic district name in title, H1, and copy.
-   Parent city/regency and province from the existing dataset.
-   Breadcrumb: Homepage → Parent city → District.
-   Links to parent city and valid related regions.
-   Honest copy inviting organizations to discuss P3K training needs.
-   Consultation CTA only with a verified destination.
-   Proper 404 handling for invalid district slugs.
-   Do not imply a physical office or trainer exists in the district
    unless confirmed.

## 7. Light Premium K3 design system

Adapt the supplied TrainingKota industrial K3 design system to a light,
premium marketing interface.

  Token             Value       Use
  ----------------- ----------- -------------------------------
  Background        `#F7F8FA`   Main background
  Surface           `#FFFFFF`   Cards/forms/navigation
  Muted surface     `#F0F3F6`   Alternate sections
  Primary emerald   `#0D7A5F`   Main actions
  Primary hover     `#09664F`   Hover/active action
  Primary soft      `#E6F4EF`   Subtle accent surface
  Command navy      `#172538`   Headings/structure
  Secondary soft    `#EAF0F6`   Secondary surfaces
  Champagne         `#C5A46D`   Restrained decorative accent
  Main text         `#172538`   Primary text
  Muted text        `#5E6B7B`   Secondary text
  Border            `#E1E7EE`   Dividers/outlines
  WhatsApp green    `#25D366`   Verified WhatsApp action only
  Danger            `#B42318`   Error states

Typography: - Space Grotesk for headings, labels, and emphasized
metrics. - IBM Plex Sans for body copy, forms, and long text. - Load
fonts through one consistent mechanism only.

Geometry and interaction: - Standard buttons: `18px` border radius. -
Floating action buttons: full rounding (`9999px`). - Cards: restrained
`12–16px` radius. - Thin borders; minimal or no shadows. - Visible
keyboard focus, accessible touch targets, and clear hover/active
states. - Respect `prefers-reduced-motion`. - Mobile-first layout
without horizontal overflow. - Use emerald for important actions, navy
for hierarchy, and champagne sparingly. - Never use champagne for small
text on white unless measured contrast passes.

Accessibility target: - WCAG AA contrast: `4.5:1` for normal text and
`3:1` for large text. - Use semantic landmarks and a logical heading
hierarchy. - Do not claim contrast compliance unless actually measured.

## 8. SEO and internal linking

-   Unique, accurate title and meta description for each page and valid
    region.
-   One meaningful H1 per page.
-   Canonical URL only if a real production domain is configured.
-   Breadcrumb structured data only if accurate.
-   FAQ structured data only when it matches visible qualifying FAQ
    content.
-   Valid internal links among homepage, city pages, and district pages.
-   No indexable thin pages for invalid or unsupported regions.
-   Escape region names and use framework-safe output.

## 9. Technical rules

-   Reuse existing Laravel routes, Blade templates, and lookup logic
    where compatible.
-   Use shared layouts/components when they improve consistency without
    overengineering.
-   Use the existing regional JSON; do not create duplicate tables
    without a demonstrated need.
-   No authentication, dashboard, payment, booking system, or
    unnecessary backend infrastructure.
-   Every visible control must have meaningful behavior.
-   Never use dead `href="#"` or `javascript:void(0)`.
-   Never invent a WhatsApp number or contact URL.
-   Do not change unrelated files or existing working behavior.
-   Implement the requested pages; do not stop at a plan once source
    patterns are identified.

## 10. Google Antigravity skills workflow

The user estimates approximately 21 available skills. Verify actual
availability in the Antigravity environment instead of assuming all can
be used.

Prioritize relevant skills: 1. UI UX Pro Max --- design system and UI
quality. 2. Ponytail --- inspect its actual documentation and use only
supported, relevant capabilities. 3. Laravel best practices. 4. Tailwind
CSS development. 5. Testing best practices. 6. Relevant accessibility,
SEO, and frontend skills.

Read actual skill instructions before use. Select complementary skills;
do not invoke every skill indiscriminately or repeat audits that do not
change implementation. Follow real documented commands. Never claim a
skill was used unless it was accessed and applied. Do not install skills
or dependencies without approval.

## 11. Verification and acceptance checklist

-   [ ] Full homepage implemented.
-   [ ] Valid city pages resolve from the existing regional data.
-   [ ] Valid district pages resolve with correct parent hierarchy.
-   [ ] Breadcrumbs and internal regional links work.
-   [ ] Invalid region slugs return an appropriate 404.
-   [ ] Mobile navigation and interactive components work.
-   [ ] No fake contacts or dead CTA links.
-   [ ] Light Premium K3 palette is consistent across all three page
    types.
-   [ ] Space Grotesk and IBM Plex Sans are loaded consistently.
-   [ ] Responsive layout has no horizontal overflow.
-   [ ] Contrast is measured or explicitly reported as unverified.
-   [ ] Relevant Laravel tests pass.
-   [ ] `npm run build` succeeds.
-   [ ] No unauthorized dependency changes, installations, migrations,
    or deployments.
-   [ ] `/root/trainingkota` remains unchanged.
-   [ ] Final report lists files changed, reused source patterns, skills
    used, tests, build results, and remaining blockers.

## 12. Required final report

When implementation is complete, report: 1. What was implemented for
homepage, city page, and district page. 2. Which TrainingKota
files/patterns were reused and adapted. 3. Confirmation that the source
project was not modified. 4. Skills actually used and any unavailable
skills. 5. Routes and regional data behavior. 6. Design and responsive
behavior. 7. SEO/accessibility checks. 8. Laravel test and Vite build
results. 9. Missing business information or remaining blockers.

Implement in `/root/pelatihan-p3k` only. Do not deploy or publish the
site.
