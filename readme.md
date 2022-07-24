<h1 align="center">
  <a href="https://tahoe.be"><img src="https://raw.githubusercontent.com/morceaudebois/dewordpressify/master/assets/dewordpressify.png" width="80%" alt=""></a>
</h1>

DeWordPressify is an extension for WordPress that lets you hide all of WordPress' branding, from the logo on the login page to mentions of it in the dashboard or in emails. You can replace it with your company's branding and make your site look entirely your own.

DeWordPressify also has some advanced functions that let you easily disable built-in features of WordPress you might not be using, like comments, emojis, RSS feed and more, thus making your site lighter.

## 👨‍💻 Things to do

- [x] Delete data on uninstall
- [ ] Better multisite compatibility?
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

## 😮 Random things to know

- When installed, DeWordPressify also takes care of centering the WordPress form on the login page.

- If strings are edited in the main file, you can regenerate the pot file by installing wp-cli and running `wp i18n make-pot . languages/dwpify.pot`.