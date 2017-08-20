<?php

if (!class_exists('Account_Suspended')) {
    class Account_Suspended
    {
        /**
         * Plugin basename
         *
         * @since 1.0.0
         * @var string
         */
        protected $basename;

        /**
         * HTTP status code
         *
         * @since 1.0.0
         * @var integer
         */
        protected $http_status = 451;

        /**
         * Redirect destination
         *
         * @since 1.0.0
         * @var integer
         */
        protected $destination;

        /**
         * Plugin settings
         *
         * @since 1.0.0
         * @var array
         */
        protected $settings = [];

        /**
         * Plugin slug
         *
         * @since 1.0.0
         * @var string
         */
        protected $slug = 'boone-account-suspended';

        /**
         * Plugin version
         *
         * @since 1.0.0
         * @var string
         */
        protected $version = '1.0.0';

        /**
         * Plugin instance (callable)
         *
         * @since 1.0.0
         * @var string
         */
        protected static $instance = null;

        /**
         * Construct functionality
         *
         * @since 1.0.0
         * @method __construct
         */
        public function __construct()
        {
            // Set plugin settings
            $this->settings = get_option('bsas_settings');

            // Set plugin basename
            $this->basename = plugin_basename(BSAS_PATH . $this->slug . '.php');

            // Activate plugin when new blog is added
            add_action('wpmu_new_blog', [$this, 'activate_new_site']);

            // Check for updates
            add_action('admin_init', [$this, 'check_update']);

            // Check if account suspension is active
            if (isset($this->settings) && $this->settings['activated']) {
                // Set destination for non-authorized users
                $this->destination = wp_login_url() . '?mode=suspended';

                // Get HTTP status code from settings
                $this->http_status = apply_filters('validate_http_status', $this->settings['http_status']);

                // Set login message for non-admin users
                add_filter('login_message', [$this, 'login_message']);

                // Initialize
                add_action('init', [$this, 'init']);

                // Redirect
                add_action('init', [$this, 'redirect'], 9);
            }
        }

        /**
         * Runs on plugin activation
         *
         * @since 1.0.0
         * @method activate
         * @param  boolean $networked If we are using a multisite install
         */
        public static function activate($networked)
        {
            if (function_exists('is_multisite') && is_multisite()) {
                if ($networked) {
                    // Get all blog IDs
                    $ids = self::get_blog_ids();

                    foreach($ids as $id) {
                        switch_to_blog($id);
                        self::single_activate($networked);
                        restore_current_blog();
                    }
                } else {
                    self::single_activate();
                }
            } else {
                self::single_activate();
            }
        }

        /**
         * Runs on activation of new site on multisite install
         *
         * @since 1.0.0
         * @method activate_new_site
         * @param  integer $id Blog ID
         */
        public function activate_new_site($id)
        {
            if (did_action('wpmu_new_blog') !== 1) {
                return;
            }

            switch_to_blog($id);
            self::single_activate();
            restore_current_blog();
        }

        /**
         * Backtime for HTTP responses
         *
         * @since 1.0.0
         * @method calculate_backtime
         * @return integer
         */
        public function calculate_backtime() {
            // Default backtime
            $backtime = 3600;

            return $backtime;
        }

        /**
         * If current request is from a search bot
         *
         * @since 1.0.0
         * @method check_search_bots
         * @return boolean
         */
        public function check_search_bots()
        {
            // By default, we're not a bot
            $is_search_bots = false;

            // But, just in case, let's make sure
            $bots = apply_filters('bsas_search_bots', [
                'Abacho' => 'AbachoBOT',
                'Accoona' => 'Acoon',
                'AcoiRobot' => 'AcoiRobot',
                'Adidxbot' => 'adidxbot',
                'AltaVista robot' => 'Altavista',
                'Altavista robot' => 'Scooter',
                'ASPSeek' => 'ASPSeek',
                'Atomz' => 'Atomz',
                'Bing' => 'bingbot',
                'BingPreview' => 'BingPreview',
                'CrocCrawler' => 'CrocCrawler',
                'Dumbot' => 'Dumbot',
                'eStyle Bot' => 'eStyle',
                'FAST-WebCrawler' => 'FAST-WebCrawler',
                'GeonaBot' => 'GeonaBot',
                'Gigabot' => 'Gigabot',
                'Google' => 'Googlebot',
                'ID-Search Bot' => 'IDBot',
                'Lycos spider' => 'Lycos',
                'MSN' => 'msnbot',
                'MSRBOT' => 'MSRBOT',
                'Rambler' => 'Rambler',
                'Scrubby robot' => 'Scrubby',
                'Yahoo' => 'Yahoo'
            ]);

            $is_search_bots = (bool) preg_match('~(' . implode('|', array_values($bots)) . ')~i', $_SERVER['HTTP_USER_AGENT']);

            return $is_search_bots;
        }

        /**
         * Checks for updates to the plugin
         *
         * @since 1.0.0
         * @method check_update
         */
        public function check_update()
        {
            // Check for plugin updates
            if (!version_compare($this->version, BSAS_VERSION, '=')) {
                self::activate((is_multisite() && is_plugin_active_for_network($this->basename)));
            }
        }

        /**
         * Checks if the user role is permissable
         *
         * @since 1.0.0
         * @method check_user_role
         * @return boolean
         */
        public function check_user_role()
        {
            $user = wp_get_current_user();
            $user_roles = (!empty($user->roles) && is_array($user->roles)) ? $user->roles : [];
            $allowed_roles = ['administrator'];

            $is_allowed = (bool) array_intersect($user_roles, $allowed_roles);

            return $is_allowed;
        }

        /**
         * Runs on plugin deactivation
         *
         * @since 1.0.0
         * @method deactivate
         * @param  boolean $networked If we are using a multisite install
         */
        public static function deactivate($networked)
        {
            if (function_exists('is_multisite') && is_multisite()) {
                if ($networked) {
                    // Get all blog IDs
                    $ids = self::get_blog_ids();

                    foreach($ids as $id) {
                        switch_to_blog($id);
                        self::single_deactivate($networked);
                        restore_current_blog();
                    }
                } else {
                    self::single_deactivate();
                }
            } else {
                self::single_deactivate();
            }
        }

        /**
         * Default settings for plugin
         *
         * @since 1.0.0
         * @method default_settings
         * @return array
         */
        public function default_settings()
        {
            return [
                'activated' => 0,
                'http_status' => 451,
                'bypass_search_bots' => 1,
                'notice' => __('Account Suspended', $this->slug),
            ];
        }

        /**
         * Return array of blog IDs
         *
         * @since 1.0.0
         * @method get_blog_ids
         * @return array
         */
        private static function get_blog_ids()
        {
            global $wpdb;

            return $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE archived = %d AND spam = %d AND deleted = %d", [0, 0, 0]));
        }

        /**
         * Get instance of plugin class
         *
         * @since 1.0.0
         * @method get_instance
         * @return class
         */
        public static function get_instance()
        {
            if (self::$instance === null) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Get settings for plugin
         *
         * @since 1.0.0
         * @method get_settings
         * @return array
         */
        public function get_settings()
        {
            return $this->settings;
        }

        /**
         * Get slug for plugin
         *
         * @since 1.0.0
         * @method get_slug
         * @return string
         */
        public function get_slug()
        {
            return $this->slug;
        }

        /**
         * Runs on plugin initialization
         *
         * @since 1.0.0
         * @method init
         */
        public function init()
        {
            // DRY, so KISS
            $php_self = $_SERVER['PHP_SELF'];

            // A lot of conditions to check
            if (
                (!$this->check_user_role()) &&
                !strstr($php_self, 'wp-cron.php') &&
                !strstr($php_self, 'wp-login.php') &&
                !(strstr($php_self, 'wp-admin/') && !is_user_logged_in()) &&
                !strstr($php_self, 'wp-admin/admin-ajax.php') &&
                !strstr($php_self, 'wp-admin/async-upload.php') &&
                !(strstr($php_self, 'upgrade.php') && $this->check_user_role()) &&
                !strstr($php_self, '/plugins/') &&
                !strstr($php_self, 'xmlrpc.php') &&
                !$this->check_search_bots() &&
                !(defined('WP_CLI') && WP_CLI)
            ) {
                // HTTP headers
                $protocol = !empty($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], ['HTTP/1.1', 'HTTP/1.0']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
                $charset = get_bloginfo('charset') ? get_bloginfo('charset') : 'UTF-8';
                $http_status = (int) apply_filters('bsas_status_code', $this->http_status);
                $backtime_seconds = $this->calculate_backtime();
                $backtime = (int) apply_filters('bsas_backtime', $backtime_seconds);

                // Meta content
                $title = 'Account Suspended';
                $title = apply_filters('bsas_meta_title', $title);

                $robots = 'index, follow';
                $robots = apply_filters('bsas_meta_robots', $robots);

                $author = get_bloginfo('name');
                $author = apply_filters('bsas_meta_author', $author);

                $description = sprintf('%s - %s', get_bloginfo('name'), get_bloginfo('description'));

                $keywords = __('Maintenance Mode', $this->slug);
                $keywords = apply_filters('bsas_meta_keywords', $keywords);

                // Script files
                $wp_scripts = wp_scripts();

                $scripts = [
                    'jquery' => '//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery' . BSAS_ASSETS_SUFFIX . '.js'
                ];
                $scripts = apply_filters('bsas_scripts', $scripts);

                // Stylesheet files
                $styles = [
                    'main' => BSAS_STYLES_URL . 'main' . BSAS_ASSETS_SUFFIX . '.css'
                ];
                $styles = apply_filters('bsas_styles', $styles);

                // Content
                nocache_headers();
                ob_start();

                header('Content-Type: text/html; charset=' . $charset);
                header($protocol . ' ' . $http_status . ' Service Unavailable', true, $http_status);
                header('Retry-After: ' . $backtime);

                // Load account suspended page
                if (file_exists(get_stylesheet_directory() . '/account-suspended.php')) {
                    include_once(get_stylesheet_directory() . '/account-suspended.php');
                } else if (file_exists(get_template_directory() . "/account-suspended.php")) {
                    include_once(get_template_directory() . '/account-suspended.php');
                } else if (file_exists(WP_CONTENT_DIR . '/account-suspended.php')) {
                    include_once(WP_CONTENT_DIR . '/account-suspended.php');
                } else { // load from plugin `views` folder
                    include_once(BSAS_VIEWS_PATH . 'suspended.php');
                }

                exit();
            }
        }

        /**
         * Sets login message for suspended accounts after redirect
         *
         * @since 1.0.0
         * @method login_message
         * @return string
         */
        public function login_message()
        {
            if (isset($_GET['mode']) && $_GET['mode'] === 'suspended') {
                $message = sprintf('<p class="message">%s</p>', $this->settings['notice']);
                return $message;
            }
        }

        /**
         * Redirect user to another page
         *
         * @since 1.0.0
         * @method redirect
         * @return mixed
         */
        public function redirect()
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return null;
            }

            if (!is_user_logged_in() || !is_admin()) {
                return null;
            }

            if ($this->check_user_role()) {
                return null;
            }

            $destination = stripslashes($this->destination);
            wp_redirect($destination, 302);
            exit;
        }

        /**
         * When activated, this activates the plugin on a single blog in a
         * multisite install
         *
         * @since 1.0.0
         * @method single_activate
         */
        public static function single_activate()
        {
            global $wpdb;

            // Get options from database
            $options = get_option('bsas_settings');

            // Get our default options
            $default_options = self::get_instance()->default_settings();

            // On install, add options to database
            if (empty($options)) {
                add_option('bsas_settings', $default_options);
            }

            // Add version to database
            update_option('bsas_version', self::get_instance()->version);
        }

        /**
         * When activated, this activates the plugin on a single blog in a
         * multisite install
         *
         * @since 1.0.0
         * @method single_deactivate
         */
        public static function single_deactivate()
        {
            // Get options from database
            $options = get_option('bsas_settings');

            // Check if version is there, too
            $version = get_option('bsas_version');

            // On uninstall, delete options to database
            if (!empty($options)) {
                delete_option('bsas_settings');
            }

            if (!empty($version)) {
                delete_option('bsas_version');
            }
        }
    }
}
