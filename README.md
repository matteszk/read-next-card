# Further Reading

A WordPress block that adds a "Further reading" card linking to another post. Pick the post yourself, or leave it on **Automatic** and the block finds the post that shares the most categories and tags.

Originally built in 2022 as a shortcode for a large corporate blog; rebuilt from scratch as a standalone block.

## Features

- **Card link:** featured image, a "Further reading" label and the post title. The whole card is one link.
- **Automatic mode:** picks the published post sharing the most categories and tags with the current post (newest wins a tie).
- **Always current:** the block stores only the post ID and renders on each view, so title and image changes show up automatically.
- **Live preview** in the editor.

## Usage

Add the **Further Reading** block to a post. Choose a post under **Linked post** in the block sidebar, or keep **Automatic**.

If Automatic finds nothing (the post has no categories or tags in common with others), the block renders nothing on the front end.

## Requirements

WordPress 6.1+ and PHP 7.4+. No build step.

## License

GPL-2.0-or-later
