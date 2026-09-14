# Representative code samples

These files are curated extracts that explain the engineering approach used in the private production project.

They are intentionally:

- abridged;
- generalized;
- detached from production configuration;
- free of personal data and deployment details;
- presented for technical review rather than installation.

## Samples

| Directory | Focus |
|---|---|
| \`portfolio-content-model/\` | Custom post type and hierarchical taxonomy |
| \`featured-projects/\` | Ordered project query and resilient card output |
| \`portfolio-gallery/\` | Gallery metadata, layouts, and safe persistence |
| \`comment-antispam/\` | Layered no-CAPTCHA comment validation |
| \`portfolio-frontend/\` | Normalized project facts and gallery view data |

## Important

These samples are not a drop-in WordPress package. Production code includes additional integration, translations, styles, scripts, compatibility behavior, and private configuration that are outside the scope of this public case study.

The examples favor clarity and the decisions relevant to a code review. WordPress escaping and validation are shown at the boundaries where they matter.
