<?php 
/**
 * @package DeWordPressify
 * @version 1.0
 */
/*
    Plugin Name: DeWordPressify
    Version: 1.0
    Author: Tahoe Beetschen
    Author URI: https://tahoe.be
    Plugin URI: https://github.com/morceaudebois/DeWordPressify
    Domain Path: /languages
    Description: DeWordPressify lets you remove WordPress' branding and replace it with your own!

    License: GPL2
*/

// activation
function init() {
    // adds default options if missing
    if (!get_option('dwpify_adminbar_logo')) {
        foreach (getDefaultOptions() as $key => $value) {
            add_option($key, $value);
        }
    }

    // same but for global multisite options 
    if (is_multisite() && !get_site_option('dwpify_adminbar_logo')) {
        foreach (getDefaultOptions() as $key => $value) {
            add_site_option($key, $value);
        }
    }

    // to know when to trigger notices
    if (!get_option('dwpify_installBanner')) { update_option('dwpify_installBanner', 'toBeTriggered'); }
    if (!get_option('dwpify_installDate')) { update_option('dwpify_installDate', time()); }
}

include(plugin_dir_path(__FILE__) . 'functions.php');
include(plugin_dir_path(__FILE__) . 'settings.php');

add_action('init', function() {
    init();

    everywhere(); // triggers on whole site
    add_action('admin_init', 'wp_admin'); // triggers in wp-admin
    add_action('login_init', 'loginPage'); // triggers on login page

     // triggers when user logged in
    if (is_user_logged_in()) user_logged_in();
    
    load_plugin_textdomain('dewordpressify', false, dirname(plugin_basename(__FILE__ )) . '/languages/');

    add_action('admin_init', function() {
        
        // triggers right after activation
        if (is_admin()) {
            if (get_option('installBanner') == 'toBeTriggered') {
                add_action('admin_notices', function() { ?>
                    <div class="notice notice-success is-dismissible" style="display: flex; flex-direction: row; align-items: center;">
        
                        <img src="<?php echo plugin_dir_url(__FILE__) . 'assets/bin.png'?>" alt="" style="max-height: 70px; height: auto; margin: 10px 15px 10px 0px">
    
                        <p><?php _e('Thank you for installing <b>DeWordPressify</b>! You can start getting rid of WordPress\' branding right away.', 'dewordpressify')?><br>
                    
                        <a class="button" href="<?php menu_page_url('dewordpressify')?>" style="margin-top: 8px"><?php _e('Visit settings page', 'dewordpressify') ?></a></p>
                        
                    </div> 
                <?php });
        
                update_option('installBanner', 'triggered');
            // change to +30 days to debug notice
            } else if (get_option('dwpify_installDate') < strtotime('-30 days') && !get_option('dwpify_usedNotice')) {

                add_action('admin_notices', function() { ?>

                    <script type="text/javascript">
                        window.addEventListener('DOMContentLoaded', () => {
                            document.querySelector('#used_banner .notice-dismiss').onclick = function() {
                                document.querySelector('#used_banner').remove();

                                fetch('http://localhost/wordpress/wp-admin/admin-ajax.php', {
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
        
                        <img src="<?php echo plugin_dir_url(__FILE__) . 'assets/bin.png'?>" alt="" style="max-height: 70px; height: auto; margin: 10px 15px 10px 0px">
    
                        <p style="margin-right: 10px">
                            <?php _e('You\'ve been using DeWordPressify for a while now, I hope you like it! If so, please consider giving a review or donating, that would help a lot 😊', 'dewordpressify')?><br>
                    
                            <a class="button" href="#" style="margin-top: 8px; margin-right: 5px;"><?php _e('Review', 'dewordpressify') ?></a>
                            <a class="button" href="#" style="margin-top: 8px"><?php _e('Donate', 'dewordpressify') ?></a>
                        
                        </p>

                        <!-- added button manually instead of with 
                        .is-dismissible otherwise onclick doesn't work -->
                        <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text">Ignore this notification.</span>
                        </button>
                        
                    </div> 
                <?php });
            }
        }
    });    
});

function wp_admin() {
    add_filter('admin_footer_text', function($defaultString) {
        if (checkOption('thank_you')) {
            $theString = checkOption('thank_you_string', true);
            echo $theString ? $theString : $defaultString;
        }
    }, 11);
    
    add_filter('update_footer', function($defaultString) {
        if (checkOption('footer_version')) {
            $theString = checkOption('footer_version_string', true);
            echo $theString ? $theString : $defaultString;
        }
    }, 11);

    if (!checkOption('dashboard_news')) {
        function rm_dahsboardnews() {
            remove_meta_box('dashboard_primary', get_current_screen(), 'side');
        }

        add_action('wp_network_dashboard_setup', 'rm_dahsboardnews', 20);
        add_action('wp_user_dashboard_setup',    'rm_dahsboardnews', 20);
        add_action('wp_dashboard_setup',         'rm_dahsboardnews', 20);
    }
}

function user_logged_in() {
    if (!checkOption('adminbar_logo')) {
        add_action('wp_before_admin_bar_render', function() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        }, 0);
    }
}

function loginPage() {
    add_action('login_head', function() { ?>
        <style type="text/css">
            <?php
                switch(checkOption('login_logo', true)) {
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
                            background-image: url('<?php echo esc_url(wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full' )[0]) ?>')
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

            /* better centered login form */
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

function everywhere() {

    if (!checkOption('smileys')) {
        // source https://gist.github.com/netmagik/88e004b17e4cc43d04b6#file-disable-emoji-in-wordpress
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );	
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        
        // Remove from TinyMCE
        add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );

        function disable_emojis_tinymce($plugins) {
            if (is_array($plugins)) return array_diff($plugins, array('wpemoji'));
            return array();
        }
    }

    if (!checkOption('rss')) {
        remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
        remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
        remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
        remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
        remove_action( 'wp_head', 'index_rel_link' ); // index link
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
        remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
        remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version

        function disableRss() {
            wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
        }
           
        add_action('do_feed', 'disableRss', 1);
        add_action('do_feed_rdf', 'disableRss', 1);
        add_action('do_feed_rss', 'disableRss', 1);
        add_action('do_feed_rss2', 'disableRss', 1);
        add_action('do_feed_atom', 'disableRss', 1);
        add_action('do_feed_rss2_comments', 'disableRss', 1);
        add_action('do_feed_atom_comments', 'disableRss', 1);
    }

    if (!checkOption('comments')) {
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
        function df_disable_comments_status() { return false; }
        add_filter('comments_open', 'df_disable_comments_status', 20, 2);
        add_filter('pings_open', 'df_disable_comments_status', 20, 2);

        // Hide existing comments
        add_filter('comments_array', function() {
            return array();
        }, 10, 2);

        // Remove comments page in menu
        add_action('admin_menu', function() {
            remove_menu_page('edit-comments.php');
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
    }

    if (!checkOption('svg')) {
        add_filter('upload_mimes', function() {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        });
    }

    if (!is_null(checkOption('email_from', true))) {
        add_filter( 'wp_mail_from_name', function() {
            return checkOption('email_from', true);
        } );
    } 

    if (!is_null(checkOption('email_username', true))) {
        add_filter('wp_mail_from', function() { // Function to change email address
            return checkOption('email_username', true) . parse_url(get_site_url(), PHP_URL_HOST);
        });
    }

    if (!checkOption('css')) {
        remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
        remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
    }

    if (!checkOption('head')) {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }

    if (!checkOption('wp_embed')) {
        add_action('wp_footer', function() {
            wp_dequeue_script('wp-embed');
        });
    }

    if (!checkOption('block_library')) {
        add_action('wp_enqueue_scripts', function() {
            // // remove block library css
            wp_dequeue_style('wp-block-library');
            // // remove comment reply JS
            wp_dequeue_script('comment-reply');
        });
    }

    if (!checkOption('wp_themes')) {
        define('CORE_UPGRADE_SKIP_NEW_BUNDLED', true);
    }
}

add_action('wp_ajax_used_notice', 'addUsedNoticeOption');
add_action('wp_ajax_nopriv_used_notice', 'addUsedNoticeOption');

function addUsedNoticeOption() {
    add_option('dwpify_usedNotice', 'closed');
}