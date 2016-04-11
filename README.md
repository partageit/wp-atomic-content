# WP atomic content

WP atomic content allows to create content blocks to use in posts, pages and custom post types.

## Getting started

1. create a new block in the WordPress admin interface (in the block menu), write some contents and publish it
2. copy the suggested shortcode
3. in a page, post, or whatever, paste the shortcode where you want to see the block's content
4. you're done

## The shortcode

### Basically

    [block id="my-block-name"]

This shortcode includes the content of the `my-block-name` block in the page.

If the block is not found, nothing happens, no errors, no additional spaces. Nothing.

### Spaced content

    [block id="my-block-name" spacebefore="1em" spaceafter="30px" /]

It is possible to set space before and after the content with `spacebefore` and `spaceafter` shortcode parameters.

Units are CSS compatible, so feel free to use `px`, `em`, `rem`, `cm`, ...

### Custom CSS classes

    [block id="my-block-name" class="my-class"]

Adding the `class` parameter, the CSS class is added to the block, in order to customize it.

It is possible to set many classes, with a space as separator.

You should note that a class named `atomic-content-block` is added to every included blocks, without any customization.

## Block content

The block editor is the same as the post editor. In other words, it is possible to format content (add paragraphs, titles, ...).

It is also possible to add shortcodes, even to include blocks.

So you can make blocks composed of blocks, but be careful to not make cyclic references (`a` includes `b` which includes `a`).

## PHP use

You can include blocks from PHP code (i.e. in theme or plugin code), without shortcode.

The PHP functions, named `atomicContent` works like the shortcode, with the same parameters.

The signature:

```php
string atomicContent($id, $classes = array(), $spaceBefore = null, $spaceAfter = null, $display = true)
```

When `$display` is `true`, the result is directly displayed. Otherwise it is returned as result of the function.

Sample:

```php
atomicContent("my-block-name", array("my-class"), "1em", "30px")
```

## Todo

- translations
- edit button in display mode, for allowed users
- add `Copy to clipboard` button in the blocks list and edition page
- widget to use blocks
- button in the HTML toolbar
- test recursive inclusions