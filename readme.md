<h1 align="center">
  <a href="https://tahoe.be"><img src="https://raw.githubusercontent.com/morceaudebois/dewordpressify/master/src/images/dewordpressify.png" width="80%" alt=""></a>
</h1>

DeWordPressify is an extension for WordPress that lets you hide all of WordPress' branding, from the logo on the login page to mentions of it in the dashboard or in emails. You can replace it with your company's branding and make your site look entirely your own.

DeWordPressify also has some advanced functions that let you easily disable built-in features of WordPress you might not be using, like comments, emojis, RSS feed and more, thus making your site lighter.

<br>

<div align="center"><img src="https://raw.githubusercontent.com/morceaudebois/dewordpressify/master/src/images/screenshot.png" width="60%" alt="What DeWordPressify looks like"></div>

<br>

## 📚 User guide

### How to install DeWordPressify with custom default settings

You might want to install DeWordPressify on multiple websites without having to customise your settings on every single one of them. If so, there is an easy way!

1. Get ahold of the DeWordPressify plugin. You can either download a release from this repo or simply get it from the `/wp-content/plugins/` folder on a website where it's already installed.
2. Once you have the plugin, navigate to `/src/php/` in it and open the `functions.php` file.
3. The first thing you'll see should be the `getDefaultOptions()` function. This is where default options are defined, each line corresponding to a toggle/field of the DeWordPressify settings page.
4. Simply edit each line you want customised, `'yes'` being toggled and `'no'` being untoggled. 
5. Save the file and upload the plugin to your new site (either zip it and upload it through the WordPress dashboard, or put it in the `/wp-content/plugins/` folder)
6. Activate it and that's it! DeWordPressify should be initialised with your custom settings.

<br>

## 👨‍💻 Things to do

- [x] Delete data on uninstall
- [x] Better multisite compatibility?
- [x] ~~Hook for overriding default init settings~~ Guide to edit the default settings
- [x] Links in plugins page
- [x] WP Embed toggle
- [x] Block library toggle
- [x] Don't download new WP themes toggle
- [x] Allow SVGs upload toggle
- [x] Installation banner
- [x] "You've been using DeWordPressify for a while" banner
- [x] Logo on settings page?
- [ ] Links for banners and stuff
- [ ] Animated logo (mayyyyybe)
- [x] Translation file + french translation
- [ ] Publish on WordPress!

<br>

## 😮 Random things to know

- If strings are edited in the main file, you can regenerate the pot file by installing wp-cli and running `wp i18n make-pot . languages/dwpify.pot`.
