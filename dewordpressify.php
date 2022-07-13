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
register_activation_hook(__FILE__, function($network_wide) {
  add_option('banner', 'toBeTriggered');
  add_option('installDate', time());
});



include(plugin_dir_path(__FILE__) . 'functions.php');
include(plugin_dir_path(__FILE__) . 'settings.php');

add_action('init', function() {
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
            } else if (get_option('installDate') < strtotime('+30 days') && !get_option('usedNotice')) {

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
    $options = get_option('dwpify_general');

    add_filter('admin_footer_text', function($defaultString) {
        if (!is_null(replaceableString('general', 'thank_you', 'thankyou_string'))) {
            echo replaceableString('general', 'thank_you', 'thankyou_string');
        } else {
            echo $defaultString;
        }
    }, 11);

    add_filter('update_footer', function($defaultString) {
        if (!is_null(replaceableString('general', 'footer_version', 'version_string'))) {
            echo replaceableString('general', 'footer_version', 'version_string');
        } else {
            echo $defaultString;
        }
    }, 11);

    if (isset($options['dashboard_news']) && !empty($options['dashboard_news'])) {
        function rm_dahsboardnews() {
            remove_meta_box('dashboard_primary', get_current_screen(), 'side');
        }

        add_action('wp_network_dashboard_setup', 'rm_dahsboardnews', 20);
        add_action('wp_user_dashboard_setup',    'rm_dahsboardnews', 20);
        add_action('wp_dashboard_setup',         'rm_dahsboardnews', 20);
    }
}

function user_logged_in() {
    $options = get_option('dwpify_general');

    if (isset($options['adminbar_logo']) && !empty($options['adminbar_logo'])) {
        function admin_bar_remove_logo() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        }
    
        add_action('wp_before_admin_bar_render', 'admin_bar_remove_logo', 0);
    }
}

function loginPage() {
    $options = get_option('dwpify_general');

    if (isset($options['login_logo'])) {
        
        function remove_logo() {
            $options = get_option('dwpify_general'); 
            
            // why does it need to be redeclared?????? ?>

            <style type="text/css">
                <?php

                    switch($options['login_logo']) {
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
        <?php }
            
        add_action('login_head', 'remove_logo');
    }
}

function everywhere() {
    $options = get_option('dwpify_general');

    if (isset($options['emojis']) && !empty($options['emojis'])) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
    }

    if (isset($options['rss']) && !empty($options['rss'])) {
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

    if (isset($options['comments']) && !empty($options['comments'])) {
        // Disable support for comments and trackbacks in post types
        function df_disable_comments_post_types_support() {
            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                if(post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        }
        add_action('admin_init', 'df_disable_comments_post_types_support');

        // Close comments on the front-end
        function df_disable_comments_status() {
            return false;
        }
        add_filter('comments_open', 'df_disable_comments_status', 20, 2);
        add_filter('pings_open', 'df_disable_comments_status', 20, 2);

        // Hide existing comments
        function df_disable_comments_hide_existing_comments($comments) {
            return array();
        }
        add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

        // Remove comments page in menu
        function df_disable_comments_admin_menu() {
            remove_menu_page('edit-comments.php');
        }
        add_action('admin_menu', 'df_disable_comments_admin_menu');

        // Redirect any user trying to access comments page
        function df_disable_comments_admin_menu_redirect() {
            global $pagenow;
            
            if ($pagenow === 'edit-comments.php') {
                wp_redirect(admin_url()); exit;
            }
        }
        add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

        // Remove comments metabox from dashboard
        function df_disable_comments_dashboard() {
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        }
        add_action('admin_init', 'df_disable_comments_dashboard');

        // Remove comments links from admin bar
        function df_disable_comments_admin_bar() {
            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        }
        add_action('init', 'df_disable_comments_admin_bar');
    }

    $options = get_option('dwpify_email');
    
    if (isset($options['email_from']) and !empty($options['email_from'])) {
        add_filter( 'wp_mail_from_name', function($original_email_from) {
            $options = get_option('dewordpressify_settings');
            return $options['email_from'];
        } );
    } 

    if (isset($options['email_username']) and !empty($options['email_username'])) {
        // Function to change email address
        add_filter('wp_mail_from', function() {
            $options = get_option('dewordpressify_settings');
            return $options['email_username'] . parse_url(get_site_url(), PHP_URL_HOST);
        });
    }

    $options = get_option('dwpify_advanced');

    if (isset($options['css']) && !empty($options['css'])) {
        remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
        remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
    }

    if (isset($options['head']) && !empty($options['head'])) {
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }
}


add_action('wp_ajax_used_notice', 'addUsedNoticeOption');
add_action('wp_ajax_nopriv_used_notice', 'addUsedNoticeOption');

function addUsedNoticeOption() {
    add_option('usedNotice', 'closed');
}