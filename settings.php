<?php

class dwpifyOptions {
	
	private $options_general;
    private $options_email;
    private $options_advanced;
	
	public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_plugin_page(){
        add_options_page(
            'DeWordPressify',
            'DeWordPressify',
            'manage_options',
            'dewordpressify',
            array($this, 'settings_dom')
       );
    }

    public function settings_dom() {
        $this->options_general = get_option('dwpify_general');
		$this->options_email = get_option('dwpify_email');
		$this->options_advanced = get_option('dwpify_advanced');

        $email_Screen = (isset($_GET['action']) && 'email' == $_GET['action']) ? true : false;
        $advanced_Screen = (isset($_GET['action']) && 'advanced' == $_GET['action']) ? true : false; ?>
       
        <div class="wrap">
            <h1><?php _e('DeWordPressify Settings', 'dewordpressify') ?></h1>
            
            <h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url('admin.php?page=dewordpressify'); ?>" class="nav-tab<?php if (! isset($_GET['action']) || isset($_GET['action']) && 'email' != $_GET['action']  && 'advanced' != $_GET['action']) echo ' nav-tab-active'; ?>">
                    <?php esc_html_e('General', 'dewordpressify') ?>
                </a>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'email'), admin_url('admin.php?page=dewordpressify'))); ?>" class="nav-tab<?php if ($email_Screen) echo ' nav-tab-active'; ?>">
                    <?php esc_html_e('Email', 'dewordpressify') ?>
                </a> 

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'advanced'), admin_url('admin.php?page=dewordpressify'))); ?>" class="nav-tab<?php if ($advanced_Screen) echo ' nav-tab-active'; ?>">
                    <?php esc_html_e('Advanced', 'dewordpressify') ?>
                </a>        
			</h2>
    
        	 <form method="post" action="options.php"> 
                <?php if ($email_Screen) { //   settings_fields('dwpify_general');
					settings_fields('dwpify_email');
					do_settings_sections('dwpify_setting_email');
					submit_button();
				} elseif($advanced_Screen) {
					settings_fields('dwpify_advanced');
					do_settings_sections('dwpify_setting_advanced');
					submit_button();
				} else { 
					settings_fields('dwpify_general');
					do_settings_sections('dwpify_setting_general');
					submit_button(); 
				} ?>
			</form>
        </div>
        
        <br>
        <hr><br><br>
        
        <img src="<?php echo plugin_dir_url(__FILE__) . 'assets/dewordpressify.png' ?>" alt="dewordpressify banner" id="dwpify_banner">

        <p><?php _e('Made in France with ❤️ by ', 'dewordpressify') ?> <a href="https://tahoe.be">Tahoe Beetschen</a></p>

        <p><?php _e('If you like DeWordPressify, please consider ', 'dewordpressify') ?> <a href="#"><?php _e('giving it a review', 'dewordpressify') ?></a> <?php _e('or', 'dewordpressify') ?> <a href="#"><?php _e('donating', 'dewordpressify') ?></a>. <br>
        <?php _e('This is what motivates me to keep it updated and create new projects as an indie developer 😊', 'dewordpressify') ?></p> 
        
    <?php }

    public function register_settings() { // register settings
        // General settings
        register_setting(
            'dwpify_general', // Option group
            'dwpify_general', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section( // __() & _e() thingies are used for translation
            'general_section', // ID
            __('General settings', 'dewordpressify'), // Title
            array($this, 'print_section_info'), // Callback
            'dwpify_setting_general' // Page
        );

        add_settings_field(
            'adminbar_logo',
            __('WordPress admin bar logo', 'dewordpressify'),
            array($this, 'adminbar_logo_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'thank_you',
            __('Thank you sentence in admin footer', 'dewordpressify'),
            array($this, 'thank_you_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'footer_version',
            __('WordPress version in admin footer', 'dewordpressify'),
            array($this, 'footer_version_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'dashboard_news',
            __('"News and events" widget on dashboard', 'dewordpressify'),
            array($this, 'dashboard_news_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'smileys',
            __('Integrated smileys', 'dewordpressify'),
            array($this, 'smileys_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'rss',
            __('Integrated RSS feed', 'dewordpressify'),
            array($this, 'rss_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'comments',
            __('Comments', 'dewordpressify'),
            array($this, 'comments_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        add_settings_field(
            'login_logo',
            __('Login logo image', 'dewordpressify'),
            array($this, 'login_logo_callback'), 
            'dwpify_setting_general',
            'general_section'
        );

        

        
        // Email settings
        register_setting(
            'dwpify_email', // Option group
            'dwpify_email', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'email_section', // ID
            __('Email Settings', 'dewordpressify'), // Title
            array($this, 'print_section_info'), // Callback
            'dwpify_setting_email' // Page
        );  

        add_settings_field(
            'email_from',
            __('"From" text of emails sent by your site.', 'dewordpressify'),
            array($this, 'email_from_callback'), 
            'dwpify_setting_email',
            'email_section'
        );

        add_settings_field(
            'email_username',
            __('Username of the email adress that sends from your site.', 'dewordpressify'),
            array($this, 'email_username_callback'), 
            'dwpify_setting_email',
            'email_section'
        );
        
        
        // Advanced settings
        register_setting(
            'dwpify_advanced', // Option group
            'dwpify_advanced', // Option name
            array($this, 'sanitize') // Sanitize
        );
        add_settings_section(
            'advanced_section', // ID
            __('Advanced Settings', 'dewordpressify'), // Title
            array($this, 'print_advanced_info'), // Callback
            'dwpify_setting_advanced' // Page
        );     
        
        add_settings_field(
            'css',
            __('Global inline styles', 'dewordpressify'),
            array($this, 'css_callback'), 
            'dwpify_setting_advanced',
            'advanced_section'
        );

        add_settings_field(
            'head',
            __('Unnecessary code in head tag', 'dewordpressify'),
            array($this, 'head_callback'), 
            'dwpify_setting_advanced',
            'advanced_section'
        );
    }


	public function print_section_info() {
        //your code...
	}

    public function print_advanced_info() {
        echo '<p>' . __('There settings may break your site. If you don\'t know what they do, you probably shouldn\'t tinker with them.', 'dewordpressify') . '</p>';
	}

    public function printCheckbox($group, $key) {
        $group_name = 'options_' . $group;
        // var_dump($this->$group_name[$key]);
        
        printf(
            "<label class='switch'>
                <input id='${key}' name='dwpify_${group}[${key}]' value='yes' %s type='checkbox' />
                <span class='slider round span'></span>
            </label>",
            
            // (!$this->$group_name || array_key_exists($key, $this->$group_name )) ? 'checked' : ''
            (isset($this->$group_name[$key]) && $this->$group_name[$key] == 'yes') ? 'checked' : ''
        );
    }

    public function adminbar_logo_callback() {
        $this->printCheckbox('general', 'adminbar_logo');
    }

    public function thank_you_callback() {
        $this->printCheckbox('general', 'thank_you');

        printf(
            '<input type="text" id="thankyou_string" name="dwpify_general[thankyou_string]" value="%s" placeholder="' . __('Your own string', 'dewordpressify') . '"/>',
            isset($this->options_general['thankyou_string']) ? esc_attr($this->options_general['thankyou_string']) : ''
        );
    }

    public function footer_version_callback() { 
        $this->printCheckbox('general', 'footer_version');

        printf(
            '<input type="text" id="version_string" name="dwpify_general[version_string]" value="%s" placeholder="' . __('Your own string', 'dewordpressify') . '"/>',
            isset($this->options_general['version_string']) ? esc_attr($this->options_general['version_string']) : ''
        );
    }

    public function dashboard_news_callback() {
        $this->printCheckbox('general', 'dashboard_news');
    }

    public function smileys_callback() {
        $this->printCheckbox('general', 'smileys');
    }

    public function rss_callback() {
        $this->printCheckbox('general', 'rss');
    }

    public function comments_callback() {
        $this->printCheckbox('general', 'comments');
    }

    public function login_logo_callback() {
        $options = $this->options_general ? $this->options_general['login_logo'] : 'wp_logo' ?>
 
         <select name="dwpify_general[login_logo]">
             <option value="wp_logo" <?php selected($options, "wp_logo"); ?>>
                 <?php _e('Default WordPress logo', 'dewordpressify') ?>
             </option>
 
             <option value="site_logo" <?php selected($options, "site_logo"); ?>>
                 <?php _e('Site logo (if there is one)', 'dewordpressify') ?>
             </option>
 
             <option value="site_title" <?php selected($options, "site_title"); ?>>
                 <?php _e('Site title', 'dewordpressify') ?>
             </option>
 
             <option value="none" <?php selected($options, "none"); ?>>
                 <?php _e('Hide', 'dewordpressify') ?>
             </option>
         </select>
     <?php }


    public function email_from_callback() {
        printf(
            '<input type="text" id="from_string" name="dwpify_email[from_string]" value="%s" placeholder="' . __('From field', 'dewordpressify') . '"/>',
            isset($this->options_email['from_string']) ? esc_attr($this->options_email['from_string']) : ''
        );
    }

    public function email_username_callback() {
        printf(
            '<input type="text" id="email_string" name="dwpify_email[email_string]" value="%s" placeholder="' . __('First part of email', 'dewordpressify') . '"/>',
            isset($this->options_email['email_string']) ? esc_attr($this->options_email['email_string']) : ''
        );
    }

    public function css_callback() {
        $this->printCheckbox('advanced', 'css');
    }

    public function head_callback() {
        $this->printCheckbox('advanced', 'head');
    }

   public function sanitize($input)  {
        $new_input = array();
        $toBeVerified = array('adminbar_logo', 'thank_you', 'thankyou_string', 'footer_version', 'version_string', 'email_username', 'email_from', 'head', 'css', 'comments', 'rss', 'smileys', 'login_logo', 'dashboard_news', 'from_string', 'email_string');

        foreach ($toBeVerified as &$value) {
            if (isset($input[$value])) $new_input[$value] = sanitize_text_field($input[$value]);
            // if (empty($input[$value])) {
            //     $new_input[$value] = false;
            // } else {
            //     sanitize_text_field($input[$value]);
            // }
        }
        // error_log(print_r($new_input, true));
        return $new_input;
    }
}

// initalises settings page (probably?)
if (is_admin()) $settings_page = new dwpifyOptions();

// adds script.js to settings page
add_action('admin_enqueue_scripts', function($hook_suffix) {
    // if not settings page
    if ($hook_suffix != 'settings_page_dewordpressify') return;

    $handle = 'dewordpressify';
    wp_register_script($handle, plugin_dir_url(__FILE__) . '/script.js');
    wp_enqueue_script($handle);
    wp_register_style($handle, plugin_dir_url(__FILE__) . '/style.css');
    wp_enqueue_style($handle);
});

?>