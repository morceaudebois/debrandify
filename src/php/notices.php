<?php if (!defined('ABSPATH')) { exit; }

// adds "settings" link in plugins.php page 
add_filter('plugin_action_links_wp-debrand/wp-debrand.php', function($links) {
    // Build and escape the URL.
    $url = esc_url(add_query_arg('page', 'wp-debrand', get_admin_url() . 'admin.php'));

    // Create the link.
    $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

    // Adds the link to the end of the array.
    array_push($links, $settings_link);
    return $links;
});

// adds other links in plugins.php page
add_filter('plugin_row_meta', function($plugin_meta, $plugin_file, $plugin_data) {
    if (isset($plugin_data['TextDomain']) && 'wp-debrand' == $plugin_data['TextDomain']) {
        $plugin_meta['dwpify-github'] = sprintf(
            '<a href="%s" target="_blank">🐙 %s</a>',
            'https://github.com/morceaudebois/wp-debrand', esc_html__('GitHub')
        );

        $plugin_meta['dwpify-donate'] = sprintf(
            '<a href="%s" target="_blank" class="wp-debrand-donation-button"> ✨ %s</a>', 
            'https://ko-fi.com/tahoe', esc_html__('Donate', 'wp-debrand')
        );
        
        unset($plugin_meta[2]); // remove default website link
    } return $plugin_meta;
}, 10, 3);

// manages banners
if (get_option('wpdbrd_installBanner') == 'toBeTriggered') {
    add_action('admin_notices', function() { ?>
        <div class="notice notice-success is-dismissible" style="display: flex; flex-direction: row; align-items: center;">

            <img src="<?php echo plugin_dir_url(__FILE__) . 'src/images/bin.png'?>" alt="" style="max-height: 70px; height: auto; margin: 10px 15px 10px 0px">

            <p><?php _e('Thank you for installing <b>WP Debrand</b>! You can start getting rid of WordPress\' branding right away.', 'wp-debrand')?><br>
        
            <a class="button" href="<?php menu_page_url('wp-debrand')?>" style="margin-top: 8px"><?php _e('Visit settings page', 'wp-debrand') ?></a></p>
            
        </div> 
    <?php });

    update_option('wpdbrd_installBanner', 'triggered');

// change to +30 days to debug notice
} else if (get_option('wpdbrd_installDate') < strtotime('-30 days') && empty(get_option('wpdbrd_usedNotice'))) {

    add_action('admin_notices', function() { ?>

        <script type="text/javascript">
            window.addEventListener('DOMContentLoaded', () => {
                document.querySelector('#used_banner .notice-dismiss').onclick = function() {
                    document.querySelector('#used_banner').remove();

                    fetch('<?= get_option('siteurl') ?>/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: 'action=used_notice'
                    }).catch(console.error)

                };
            })
        </script>

        <div class="notice" id="used_banner" style="display: flex; flex-direction: row; align-items: center; position: relative;">
            <img src="<?php echo plugin_dir_url(__FILE__) . 'src/images/bin.png'?>" alt="" style="max-height: 70px; height: auto; margin: 10px 15px 10px 0px">

            <p style="margin-right: 10px">
                <?php _e('You\'ve been using WP Debrand for a while now, I hope you like it! If so, please consider giving a review or donating, that would help a lot 😊', 'wp-debrand')?><br>
        
                <a class="button" href="https://wordpress.org/plugins/wp-debrand/#reviews" style="margin-top: 8px; margin-right: 5px;"><?php _e('Review', 'wp-debrand') ?></a>
                <a class="button" href="https://ko-fi.com/tahoe" style="margin-top: 8px"><?php _e('Donate', 'wp-debrand') ?></a>
            </p>

            <!-- added button manually instead of with 
            .is-dismissible otherwise onclick doesn't work -->
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Ignore this notification.</span>
            </button>
        </div> 
    <?php });
}