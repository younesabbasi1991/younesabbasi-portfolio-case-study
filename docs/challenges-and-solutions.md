# Engineering challenges and solutions

## 1. Keeping portfolio data independent from the theme

**Challenge:** A personal portfolio evolves visually. Storing the project model inside one theme would tie durable content to a specific presentation layer.

**Solution:** The custom post type, taxonomy, project metadata, gallery data, and comment protection were moved into an MU plugin. The theme consumes this stable model through helpers and templates.

**Result:** A redesign can replace presentation without removing access to project records.

## 2. Supporting detailed case studies without a page builder dependency

**Challenge:** Projects need both editorial freedom and predictable, comparable project facts.

**Solution:** Native WordPress content stores the narrative while validated metadata stores dates, status, client, location, external URL, and gallery configuration.

**Result:** Editors keep the familiar WordPress workflow and templates receive dependable structured data.

## 3. Presenting mixed image orientations cleanly

**Challenge:** Portfolio evidence includes desktop captures, mobile captures, details, and banners with very different aspect ratios.

**Solution:** The gallery records an explicit horizontal or vertical layout per attachment. A frontend helper can infer a fallback orientation from image dimensions when no layout is saved.

**Result:** The gallery can preserve image legibility without forcing every asset into one crop.

## 4. Preserving editorial order on featured projects

**Challenge:** Featured work should follow a deliberate narrative rather than publish date or database order.

**Solution:** Selected IDs are normalized, deduplicated, capped, and passed to a query using \`post__in\` with \`orderby => post__in\`.

**Result:** The homepage renders projects in exactly the order chosen by the site owner.

## 5. Handling missing project images consistently

**Challenge:** A missing featured image could break card proportions or create inconsistent markup across archive and homepage views.

**Solution:** A dedicated thumbnail helper applies one fallback source, alt-text rules, lazy loading, and asynchronous decoding.

**Result:** Every project card retains a usable media area and consistent image attributes.

## 6. Reducing comment spam without adding CAPTCHA friction

**Challenge:** Public case studies benefit from discussion, but a personal site should not force every visitor through a CAPTCHA.

**Solution:** The form combines a nonce, honeypot, signed timestamp, completion-time window, link limit, and rate limit.

**Result:** Automated submissions face multiple independent checks while normal visitors see the standard comment experience.

## 7. Keeping repeatable admin settings maintainable

**Challenge:** Skills, services, recommendations, social links, and history entries require repeatable fields with different validation rules.

**Solution:** Each settings area owns its sanitization, repeaters are sortable, media values are stored as validated attachment IDs, and bounded values such as recommendation ratings are clamped to their valid range.

**Result:** The administration remains flexible without treating a complex array as trusted input.

## 8. Adding motion without hiding core content

**Challenge:** The visual design uses transitions, filters, sliders, and animation, but content must remain navigable and indexable.

**Solution:** WordPress renders semantic links and content first. JavaScript progressively enhances the interface with Swup, Isotope, Swiper, Fancybox, and Anime.js.

**Result:** Motion supports the experience without becoming the only path to the content.
