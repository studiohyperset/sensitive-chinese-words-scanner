# The Great Firewords of China (WordPress Plugin)

Scan your website for words and phrases that the Chinese government considers sensitive. Edit or remove content the plugin identifies, and decrease the chance your site will be blocked by the Great Firewall of China. If your site’s already being blocked, this plugin can help you discover possible reasons why.

## FAQ
The Great Firewords of China WordPress plugin works in three ways:

1. It scans database content.
2. It scans theme and plugin content.
3. It actively monitors new pages, posts, and comments and alerts you when any sensitive words are added to your site.

Please use your best judgement when editing any content the GFW plugin identifies as sensitive. The plugin relies on [@jasonqng](https://github.com/jasonqng)'s [list of sensitive Chinese keywords](https://github.com/jasonqng/chinese-keywords), which contains several generic terms such as “it,” “admin,” and “gov.” Your site won’t necessarily run afoul of the Chinese authorities just because our plugin identifies a sensitive keyword.

## Case Study

To learn how we used this plugin to help a global business intelligence company launch its marketing site on the Chinese mainland, [click here](https://studiohyperset.com/how-do-i-launch-a-chinese-website/?utm_source=GitHub&utm_medium=GFW-Repo).

## Installation Instructions
1. Install and activate the plugin as you would any WordPress plugin. (If you’re unfamiliar with installing WordPress plugins, please read [this page](https://codex.wordpress.org/Managing_Plugins) from the Codex).

2. You’ll see a new top-level admin menu titled “GFW.” Visit GFW > Overview to learn how the plugin works and to scan your site.

## Changelog
- 1.1 (11/22/17)

  WP 4.9 includes a new file editor, which allows users to edit any file at any directory level. This release of the GFW plugin takes advantage of this new feature. Users can now link directly from the file scan screen to theme and plugin files that contain banned keywords. We hope this makes editing these files easier.

- 1.0 (8/29/17)
  
  Initial release.

## Developer Notes
- Files are encoded with [GB2312](https://en.wikipedia.org/wiki/GB_2312), but some characters will only work with [GB18030](https://en.wikipedia.org/wiki/GB_18030) encoding.
- Some words include regex elements. Thes words must be [escaped](https://en.wikipedia.org/wiki/Escape_character). Regex phrases should be inserted into their specific files.
