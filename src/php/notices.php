<?php if (!defined('ABSPATH')) { exit; }

// adds "settings" link in plugins.php page 
add_filter('plugin_action_links_debrandify/debrandify.php', function($links) {
    // Build and escape the URL.
    $url = esc_url(add_query_arg('page', 'debrandify', get_admin_url() . 'admin.php'));

    // Create the link.
    $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

    // Adds the link to the end of the array.
    array_push($links, $settings_link);
    return $links;
});

// adds other links in plugins.php page
add_filter('plugin_row_meta', function($plugin_meta, $plugin_file, $plugin_data) {
    if (isset($plugin_data['TextDomain']) && 'debrandify' == $plugin_data['TextDomain']) {
        $plugin_meta['dbrdify-github'] = sprintf(
            '<a href="%s" target="_blank">🐙 %s</a>',
            'https://github.com/morceaudebois/debrandify', esc_html__('GitHub')
        );

        $plugin_meta['dbrdify-donate'] = sprintf(
            '<a href="%s" target="_blank" class="debrandify-donation-button"> ✨ %s</a>', 
            'https://ko-fi.com/tahoe', esc_html__('Donate', 'debrandify')
        );
        
        unset($plugin_meta[2]); // remove default website link
    } return $plugin_meta;
}, 10, 3);

// manages banners
if (get_option('dbrdify_installBanner') == 'toBeTriggered') {
    add_action('admin_notices', function() { ?>
        <div class="notice notice-success is-dismissible" style="display: flex; flex-direction: row; align-items: center;">

            <img src="<?php echo plugin_dir_url(__FILE__) . 'src/images/bin.png'?>" alt="" style="max-height: 70px; height: auto; margin: 10px 15px 10px 0px">

            <p><?php _e('Thank you for installing <b>Debrandify</b>! You can start getting rid of WordPress\' branding right away.', 'debrandify')?><br>
        
            <a class="button" href="<?php menu_page_url('debrandify')?>" style="margin-top: 8px"><?php _e('Visit settings page', 'debrandify') ?></a></p>
            
        </div> 
    <?php });

    update_option('dbrdify_installBanner', 'triggered');

// change to +30 days to debug notice
} else if (get_option('dbrdify_installDate') < strtotime('-30 days') && empty(get_option('dbrdify_usedNotice'))) {

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
                <?php _e('You\'ve been using Debrandify for a while now, I hope you like it! If so, please consider giving a review or donating, that would help a lot 😊', 'debrandify')?><br>
        
                <a class="button" href="https://wordpress.org/plugins/debrandify/#reviews" style="margin-top: 8px; margin-right: 5px;"><?php _e('Review', 'debrandify') ?></a>
                <a class="button" href="https://ko-fi.com/tahoe" style="margin-top: 8px"><?php _e('Donate', 'debrandify') ?></a>
            </p>

            <!-- added button manually instead of with 
            .is-dismissible otherwise onclick doesn't work -->
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Ignore this notification.</span>
            </button>
        </div> 
    <?php });
}