# Read Next Card

A WordPress block that adds a "Further reading" card to a post, linking to another article. Pick the post yourself, or leave it on **Automatic** and the block finds the most closely related post for you.

**[▶ Try it live in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/matteszk/acme-demo-site/main/blueprint.json)**: a full demo site in your browser, nothing to install.

## Why

Internal links keep readers on your site, but writers rarely add them. When they do, the links are plain text that's easy to miss and that points at whatever the writer remembered. Read Next Card turns them into a clear, clickable card and can pick a relevant post automatically, so every article can point somewhere useful.

## How Automatic picks a post

When the block is set to **Automatic**, it:

1. Looks at the current post's **categories and tags**.
2. Fetches the 20 most recent published posts that share at least one of them.
3. **Scores** each candidate: one point for every category and tag it has in common.
4. Shows the **highest score**. On a tie, the newest post wins.

If no other post shares a category or tag, the block shows nothing on the front end rather than a random link.

For example, on the [demo site](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/matteszk/acme-demo-site/main/blueprint.json):

| Post | Its terms | Card shown | Why |
|---|---|---|---|
| How to pick the right chart | Guides, *dashboards*, *charts* | 5 dashboard mistakes to avoid | Shares Guides **and** *dashboards* (2 points); other Guides posts score 1 |
| Handling missing data | Guides, *data-quality* | Removing duplicate records with SQL | Shares Guides **and** *data-quality* (2 points) |
| Dashboards for executives | Guides, *executives* | How Northline Retail rebuilt their reporting | Picked by hand, which overrides Automatic |

## Features

- **A card, not a link.** Featured image, a "Further reading" label and the post title. The whole card is one link.
- **Automatic or manual.** Choose a post in the block sidebar, or let it pick the most related one.
- **Always current.** The block stores only the post ID (or 0 for Automatic) and renders on each page view, so title and image changes show up everywhere straight away.
- **Live preview** in the editor.
- **No dependencies.** No build step, no settings page.

## Getting started

1. Install and activate the plugin.
2. In a post, add the **Read Next Card** block.
3. In the block sidebar, under **Linked post**, keep **Automatic (most related)** or choose a specific post.

Automatic works best when your posts use categories and tags consistently.

## For developers

| What | Where |
|---|---|
| Block | `read-next-card/card`, dynamic |
| Attribute | `postId` (number; `0` means Automatic) |
| Matching function | `rnc_find_related( $post_id )`, returns a post ID or `0` |

**Styling.** The markup uses BEM-style classes, so a theme can restyle it without touching the plugin: `.rnc`, `.rnc__card`, `.rnc__image`, `.rnc__text`, `.rnc__kicker` and `.rnc__title`. The stylesheet is registered through `block.json`, so the card looks the same in the editor and on the page.

The plugin header sets `Update URI: false`, so WordPress never offers an "update" from an unrelated WordPress.org plugin with a similar name.

## Known limitations

- Automatic only considers categories and tags, and only the `post` post type.
- It only scores the 20 most recent matching posts, which keeps it fast on large sites but can miss an older, closer match.
- WordPress puts uncategorized posts in "Uncategorized," so two of them count as related.

## Background

I originally built this as a shortcode for a large corporate blog. This is a from-scratch rebuild as a block, adding the Automatic mode and a live editor preview.

## Requirements

WordPress 6.1+ and PHP 7.4+.

## License

GPL-2.0-or-later
