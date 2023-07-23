<?php if (!defined('ABSPATH')) { exit; }
/**
 * @package Debrandify
 * @version 1.1.0
 */
/*
    Plugin Name: Debrandify
    Version: 1.1.0
    Author: Tahoe Beetschen
    Author URI: https://tahoe.be
    Plugin URI: https://github.com/morceaudebois/debrandify
    Domain Path: languages/
    Text Domain: debrandify
    Description: Debrandify is a simple WordPress plugin that lets you hide WordPress' branding and replace it with yours as well as make your site lighter.

    License: GPL2
*/

include(plugin_dir_path(__FILE__) . '/src/php/functions.php');
include(plugin_dir_path(__FILE__) . '/src/php/settings.php');

// activation
function dbrdify_init() {
    // adds default options if missing
    
    if (!get_option('dbrdify_adminbar_logo')) {
        foreach (dbrdify_getDefaultOptions() as $key => $value) {
            add_option($key, $value);
        }
    }

    // same but for global multisite options 
    if (is_multisite() && !get_site_option('dbrdify_adminbar_logo')) {
        foreach (dbrdify_getDefaultOptions() as $key => $value) {
            add_site_option($key, $value);
        }
    }

    // to know when to trigger notices
    if (!get_option('dbrdify_installBanner')) { update_option('dbrdify_installBanner', 'toBeTriggered'); }
    if (!get_option('dbrdify_installDate')) { update_option('dbrdify_installDate', time()); }
}

add_action('init', function() {

    dbrdify_init();

    dbrdify_everywhere(); // triggers on whole site
    add_action('admin_init', 'dbrdify_wp_admin'); // triggers in wp-admin
    add_action('login_init', 'dbrdify_loginPage'); // triggers on login page

     // triggers when user logged in
    if (is_user_logged_in()) dbrdify_user_logged_in();
    
    load_plugin_textdomain('debrandify', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    add_action('admin_init', function() {
        // triggers right after activation
        if (is_admin())  include(plugin_dir_path(__FILE__) . 'src/php/notices.php');
    });    
});

function dbrdify_wp_admin() {
    add_filter('admin_footer_text', function($defaultString) {
        if (dbrdify_checkOption('thank_you')) {
            $theString = dbrdify_checkOption('thank_you_string', true);
            echo empty($theString) ? $defaultString : esc_html($theString);
        }
    }, 11);
    
    add_filter('update_footer', function($defaultString) {
        $allowed_tags = array(
            'a' => array(
                'href' => array()
            )
        );

        if (dbrdify_checkOption('footer_version')) {
            $theString = dbrdify_checkOption('footer_version_string', true);
            echo empty($theString) ? wp_kses($defaultString, $allowed_tags) : esc_html($theString);
        }
    }, 11);

    if (!dbrdify_checkOption('dashboard_news')) {
        function dbrdify_rm_dashboardnews() {
            remove_meta_box('dashboard_primary', get_current_screen(), 'side');
        }

        add_action('wp_network_dashboard_setup', 'dbrdify_rm_dashboardnews', 20);
        add_action('wp_user_dashboard_setup',    'dbrdify_rm_dashboardnews', 20);
        add_action('wp_dashboard_setup',         'dbrdify_rm_dashboardnews', 20);
    }

    if (is_plugin_active('elementor/elementor.php') && !dbrdify_checkOption('elementor_overview')) {
        add_action('wp_dashboard_setup', function() {
            remove_meta_box( 'e-dashboard-overview', 'dashboard', 'normal');
        }, 40);
    }
}

function dbrdify_user_logged_in() {
    if (!dbrdify_checkOption('adminbar_logo')) {
        add_action('wp_before_admin_bar_render', function() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        }, 0);
    }
}

function dbrdify_loginPage() {
    add_action('login_head', function() { ?>
        <style type="text/css">
            <?php
                switch(dbrdify_checkOption('login_logo', true)) {
                    case 'site_logo':
                        add_filter('login_headerurl', function() {
                            return get_bloginfo('url');
                        });

                        add_filter('login_headertext', function() {
                            return get_bloginfo('name');
                        }); ?>

                        .login h1 a { 
                            overflow: visible;
                            padding: unset;
                            background-size: contain;
                            background-position: center;
                            width: 85%;
                            background-image: url('<?php echo esc_url(wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full')[0]) ?>')
                        } /* changes logo */

                    <?php break;
                    case 'site_title':
                        add_filter('login_headerurl', function() {
                            return get_bloginfo('url'); // changes URL
                        });

                        add_filter('login_headertext', function() {
                            return get_bloginfo('name'); // changes link content
                        }); ?>

                        .login h1 a { 
                            background: unset;
                            text-indent: unset;
                            height: unset;
                            overflow: visible;
                            padding: unset;
                            width: 80%;
                            font-size: 24px;
                        } /* Makes link visible */
                        
                    <?php break;
                    case 'none': ?>
                        .login h1 a { display: none }
                    <?php break;
                }
            ?>
        </style>
    <?php });
}

function dbrdify_everywhere() {

    if (!dbrdify_checkOption('smileys')) {
        // source https://gist.github.com/netmagik/88e004b17e4cc43d04b6#file-disable-emoji-in-wordpress
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');	
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');	
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        
        // Remove from TinyMCE
        add_filter('tiny_mce_plugins', function($plugins) {
            if (is_array($plugins)) return array_diff($plugins, array('wpemoji'));
            return array();
        });
    }

    if (!dbrdify_checkOption('rss')) {
        remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
        remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
        remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
        remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
        remove_action('wp_head', 'index_rel_link'); // index link
        remove_action('wp_head', 'parent_post_rel_link', 10, 0); // prev link
        remove_action('wp_head', 'start_post_rel_link', 10, 0); // start link
        remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
        remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version

        add_action('feed_links_show_posts_feed', '__return_false', - 1);
        add_action('feed_links_show_comments_feed', '__return_false', - 1);

        function dbrdify_disableRss() {
            wp_die(
                __('No feed available, please visit the ', 'debrandify') .
                '<a href="' . esc_url(home_url('/')) . '">' .
                __('homepage', 'debrandify') .
                '</a> !'
            );
        }

        add_action('do_feed', 'dbrdify_disableRss', 1);
        add_action('do_feed_rdf', 'dbrdify_disableRss', 1);
        add_action('do_feed_rss', 'dbrdify_disableRss', 1);
        add_action('do_feed_rss2', 'dbrdify_disableRss', 1);
        add_action('do_feed_atom', 'dbrdify_disableRss', 1);
        add_action('do_feed_rss2_comments', 'dbrdify_disableRss', 1);
        add_action('do_feed_atom_comments', 'dbrdify_disableRss', 1);
    }

    // here because if it's in admin, it doesn't work on login page
    if (!dbrdify_checkOption('wordpress-tab-suffix')) {
        add_filter('admin_title', 'dbrdify_removeSuffix', 99);
        add_filter('login_title', 'dbrdify_removeSuffix', 99);
        
        function dbrdify_removeSuffix($origtitle) {
            // weird em dash encoding
            return str_replace(' &#8212; WordPress', '', $origtitle);
        }
    }

    if (!dbrdify_checkOption('comments')) {
        // Disable support for comments and trackbacks in post types
        add_action('admin_init', function() {
            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                if(post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });

        // Close comments on the front-end
        function dbrdify_df_disable_comments_status() { return false; }
        add_filter('comments_open', 'dbrdify_df_disable_comments_status', 20, 2);
        add_filter('pings_open', 'dbrdify_df_disable_comments_status', 20, 2);

        // Hide existing comments
        add_filter('comments_array', function() {
            return array();
        }, 10, 2);

        // Remove comments page in menu
        add_action('admin_menu', function() {
            remove_menu_page('edit-comments.php');
            remove_submenu_page('options-general.php', 'options-discussion.php');
        });

        // Redirect any user trying to access comments page
        add_action('admin_init', function() {
            global $pagenow;
            
            if ($pagenow === 'edit-comments.php') {
                wp_redirect(admin_url()); exit;
            }
        });

        // Remove comments metabox from dashboard
        add_action('admin_init', function() {
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        });

        // Remove comments links from admin bar
        add_action('init', function() {
            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        });

        add_action('wp_before_admin_bar_render', function() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('comments');
        });
    }

    if (!empty(dbrdify_checkOption('email_from', true))) {
        add_filter('wp_mail_from_name', function() {
            return dbrdify_checkOption('email_from', true);
        });
    } 

    if (!empty(dbrdify_checkOption('email_username', true))) {
        add_filter('wp_mail_from', function() { // Function to change email address
            return dbrdify_checkOption('email_username', true) . '@' . parse_url(get_site_url(), PHP_URL_HOST);
        });
    }

    if (!dbrdify_checkOption('css')) {
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
    }
    
    if (!dbrdify_checkOption('head')) {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }

    if (!dbrdify_checkOption('wp_embed')) {
        add_action('wp_footer', function() {
            wp_dequeue_script('wp-embed');
        });
    }

    if (!dbrdify_checkOption('block_library')) {
        add_action('wp_enqueue_scripts', function() {
            // // remove block library css
            wp_dequeue_style('wp-block-library');
            // // remove comment reply JS
            wp_dequeue_script('comment-reply');
        });
    }

    if (dbrdify_checkOption('svg')) {
        // Shamelessly stolen here https://wpengine.com/resources/enable-svg-wordpress/
        
        // Allow SVG
        add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
            global $wp_version;

            if ($wp_version !== '4.7.1') return $data;
        
            $filetype = wp_check_filetype($filename, $mimes);
        
            return [
                'ext'             => $filetype['ext'],
                'type'            => $filetype['type'],
                'proper_filename' => $data['proper_filename']
            ];
        }, 10, 4);
        
        add_filter('upload_mimes', function($mimes) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        });
        
        add_action('admin_head', function() {
            echo '<style type="text/css">
                .attachment-266x266, .thumbnail img {
                    width: 100% !important;
                    height: auto !important;
                }
                </style>';
        });
    }

    if (dbrdify_checkOption('centerLogin')) {
        add_action('login_head', function() { ?>
            <style type="text/css">
                /* centered login form */
                @media screen and (min-height: 550px) {
                    body {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        flex-direction: column;
                    }
    
                    #login {
                        padding: 20px 0;
                        margin: unset;
                    }
    
                    .login form {
                        margin-top: unset;
                    }
                }
            </style>
        <?php });
    }

    if (!dbrdify_checkOption('restAPI')) {
        add_filter('rest_authentication_errors', function() {
            return new WP_Error('rest_disabled', __('The WordPress REST API has been disabled.', 'debrandify'), array('status' => rest_authorization_required_code()));
        });
    }

    if (!dbrdify_checkOption('jquery')) {
        if (!dbrdify_is_login_form() && !is_admin()) {
            wp_deregister_script('jquery');
        }
    }
}

add_action('wp_ajax_used_notice', 'dbrdify_addUsedNoticeOption');
add_action('wp_ajax_nopriv_used_notice', 'dbrdify_addUsedNoticeOption');

function dbrdify_addUsedNoticeOption() { update_option('dbrdify_usedNotice', 'closed'); }