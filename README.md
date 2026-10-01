# Multi-Campus College Recruitment Platform

A WordPress build for a college with several campuses. It covers programs, campuses, admissions info, faculty, events and recruitment campaigns. Most of the pages are built from custom ACF blocks, so the marketing team can put together landing pages without needing a developer.

Stack: WordPress, PHP, ACF Pro, Gutenberg, JavaScript, SCSS. The design comes from Figma.

![Homepage](docs/screenshots/01-homepage.jpg)

## What's in the repo

- `wp-content/plugins/mcrp-core` holds the content side: post types, taxonomies, ACF fields, the inquiry form handling, a small REST API and a demo content importer.
- `wp-content/themes/mcrp-theme` holds the front end: templates, the ACF blocks, block patterns, SCSS and JS.

I kept the content model in a plugin so a future redesign or theme switch doesn't touch the programs, inquiries or URLs.

## Content types

- **Programs** (`/programs/`): duration, intakes, tuition (domestic and international), courses, admission requirements, career outcomes, the campuses that offer it, instructors
- **Campuses** (`/campuses/`): address, contact details, admissions email, hours, stats, amenities
- **Instructors** (`/faculty/`): position, credentials, home campus
- **Events** (`/events/`): open houses, info sessions, tours, webinars. Each one has an "add to calendar" .ics download.
- **Testimonials** and **FAQs**: not public pages, they're only pulled into blocks and templates
- **Inquiries**: private. This is where submissions from the request-info forms end up.

Taxonomies: area of study, credential, delivery mode, event type, FAQ category.

## Blocks

Each block lives in its own folder under `themes/mcrp-theme/blocks/` with a `block.json`, `fields.php` and `render.php`. `inc/blocks.php` loads them automatically, so adding a block means adding a folder.

The blocks:

- Hero, which can include an inquiry form
- Program Finder, with search and filters, or a hand-picked list. It can be locked to an area of study or a campus for campaign pages.
- Campus Cards
- Stats
- Testimonials
- FAQ Accordion
- Upcoming Events
- Faculty Grid
- CTA Banner
- Admissions Steps
- Media & Text
- Inquiry Form
- Section, a wrapper for regular core blocks

Most blocks share a background and spacing option. There are also patterns for the homepage, admissions, international students and an open house campaign page, plus a "Campaign Landing Page" template with no navigation.

## Inquiries / lead tracking

- Forms submit through the REST API (`/wp-json/mcrp/v1/inquiries`). If JS is off they fall back to a normal POST.
- UTM parameters are saved in a cookie on the first visit, so if someone fills in a form a few days later the lead is still credited to the right campaign.
- Each lead is emailed to admissions and to the selected campus's inbox. You can also send leads to a CRM through a webhook URL in the settings page, or hook into `mcrp_lead_created`.
- The admin has an inquiries list with source and campaign columns, a campus filter and a CSV export.

## Setup

You need WordPress 6.4+, PHP 8.1+ and ACF Pro (not included, it's a paid plugin).

1. Copy the plugin and theme into `wp-content`.
2. Activate ACF Pro, then MCRP Core, then the MCRP Campus theme.
3. Re-save permalinks.
4. Optional: import the demo content (a fake college called Northbridge) from **Tools > Recruitment Demo**, or with `wp mcrp seed`. Run `wp mcrp seed --reset` to start over.
5. Fill in the apply URL, phone number and lead emails under **Recruitment** in the admin menu.

For local dev I used wp-env. Put the ACF Pro zip next to `.wp-env.json` and run:

```
npx @wordpress/env start
npx @wordpress/env run cli wp mcrp seed
```

### Building the CSS/JS

The compiled files are already in `assets/dist`. You only need this if you change the SCSS or JS:

```
cd wp-content/themes/mcrp-theme
npm install
npm run build   # or: npm run watch
```

## Notes

- Colours, fonts and spacing come from the Figma styles. They're set in `theme.json` (so the editor only shows the brand colours) and in `assets/src/scss/abstracts/_tokens.scss`.
- ACF field groups are registered in PHP instead of saved in the database, so they live in git. Field keys follow `field_mcrp_{group}_{name}`, which lets the patterns and the demo importer reference them.
- Templates use `mcrp_get()` instead of `get_field()` so the site doesn't fatal if ACF is deactivated. Without ACF Pro the saved blocks still render on the front end (from the block data); you just can't edit them in the editor.
- Programs, events, campuses and FAQs output JSON-LD.

### Things I'd still like to do

- Make the lead notification email editable from the admin
- Replace the Google Maps iframe on campus pages with a proper map
- Add PHPUnit tests for the lead handling

More screenshots are in `docs/screenshots`.
