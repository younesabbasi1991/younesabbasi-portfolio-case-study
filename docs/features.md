# Feature inventory

This document maps visible product behavior to the underlying engineering work.

## Portfolio content system

- REST-enabled Portfolio custom post type.
- Hierarchical portfolio categories.
- Native editor support for title, excerpt, body, featured image, revisions, ordering, and comments.
- Structured fields for dates, status, client, location, and live project URL.
- Sortable Media Library gallery.
- Per-image horizontal or vertical presentation mode.
- Safe update-or-delete behavior for optional metadata.

## Portfolio frontend

- Responsive three-column archive cards with tablet and mobile fallbacks.
- Category filtering enhanced with Isotope.
- Dedicated taxonomy archive pages.
- Pagination for project archives.
- Single-project cover and long-form case study.
- Project facts table.
- External project link.
- Responsive gallery with Fancybox viewing.
- Image-orientation classes for mixed media.
- Discussion area using native WordPress comments.
- Shared default thumbnail behavior.

## Homepage

- Profile-driven hero content.
- Animated professional statistics.
- Manually ordered featured portfolio projects.
- Service overview.
- “Why work with me” value section.
- Latest article slider.
- Reusable calls to action.

The featured-project component validates project IDs, removes duplicates, limits the query to six projects, and preserves the configured order.

## Site settings

Custom Settings API screens support:

- profile and biography;
- languages;
- skills and progress values;
- knowledge items;
- services;
- social links;
- navigation and profile links;
- recommendations and ratings;
- education and work history;
- contact form configuration.

Repeatable items can be reordered. Input is sanitized by type, ratings are constrained to valid values, and media references are stored as attachment IDs.

## Frontend interaction

The project uses focused libraries for specific enhancement jobs:

| Library | Responsibility |
|---|---|
| Isotope | Portfolio filtering and layout |
| Swiper | Sliders and carousels |
| Fancybox | Project media viewing |
| Anime.js | Interface animation |
| Swup | Page transitions |
| Smooth Scrollbar | Desktop scroll behavior |
| Progressbar.js | Skill visualization |

Primary navigation and content are server rendered; these libraries enhance rather than define the core information architecture.

## Security and resilience

- Nonce validation for administrative metadata writes.
- Capability checks before saving project data.
- Autosave and revision guards.
- Status allowlist and field-specific sanitization.
- Comment honeypot and signed form timing.
- Comment rate limiting and URL-count checks.
- Escaping at output boundaries.
- Thumbnail fallback for missing project media.

## Performance and maintainability

- Small helper classes isolate portfolio formatting and image behavior.
- \`no_found_rows\` is used when result counts are unnecessary.
- Meta and taxonomy caches are enabled for card collections.
- Images are lazy loaded and decoded asynchronously.
- Admin assets are loaded only in the relevant editor context.
- Repeated homepage sections are implemented as template parts.
- Content-model responsibilities remain independent of the theme.
