<?php

if (!class_exists('Account_Suspended_Admin')) {
    class Account_Suspended_Admin
    {
        /**
         * Plugin basename
         *
         * @since 1.0.0
         * @var string
         */
        protected $basename;

        /**
         * Plugin settings defaults
         *
         * @since 1.0.0
         * @var array
         */
        protected $defaults = [];

        /**
         * Suffix for all plugin hooks
         *
         * @since 1.0.0
         * @var string
         */
        protected $plugin_hooks_suffix = null;

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
            // Get instance of plugin
            $plugin = Account_Suspended::get_instance();

            // Now, get plugin settings and defaults
            $this->defaults = $plugin->default_settings();
            $this->settings = $plugin->get_settings();

            // Get the plugin slug
            $this->slug = $plugin->get_slug();

            // Set plugin basename
            $this->basename = plugin_basename(BSAS_PATH . $this->slug . '.php');

            // Load styles and scripts
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);

            // Add the options page and menu item
            add_action('admin_menu', [$this, 'add_plugin_menu']);

            // Add an action link pointing to the options page
            if (is_multisite() && is_plugin_active_for_network($this->basename)) {
                add_filter('network_admin_plugin_action_links_' . $this->basename, [$this, 'add_settings_link']);
            } else {
                add_filter('plugin_action_links_' . $this->basename, [$this, 'add_settings_link']);
            }

            // Add admin notices
            //add_action('admin_notices', [$this, 'admin_notices']);

            // Add text to footer
            add_filter('admin_footer_text', [$this, 'admin_footer_text'], 5);
        }

        /**
         * Adds page to admin menu
         *
         * @since 1.0.0
         * @method add_plugin_menu
         */
        public function add_plugin_menu()
        {
            $this->plugin_hooks_suffix = add_options_page(
                __('Account Suspended', $this->slug),
                __('Account Suspended', $this->slug),
                'manage_options',
                $this->slug,
                [$this, 'display_plugin_settings']
            );
        }

        /**
         * Add settings link
         *
         * @since 1.0.0
         * @method add_settings_link
         * @param  array $links Collection of links
         * @return array
         */
        public function add_settings_link($links)
        {
            return array_merge([
                'bsas_settings' => '<a href="' . admin_url('options-general.php?page=' . $this->slug) . '">' . __('Settings', $this->slug) . '</a>'
            ], $links);
        }

        /**
         * Displays text on the admin page footer
         *
         * @since 1.0.0
         * @method admin_footer_text
         * @param  string $text Overrides text to display
         */
        public function admin_footer_text($text)
        {
            $screen = get_current_screen();

            if ($this->plugin_hooks_suffix === $screen->id) {
                $text = sprintf(__('Developed with <span style="color: #dd455c">&#9829;</span> by Boone Software'));
            }

            return $text;
        }

        /**
         * Delete cache if any cache plugin is installed
         *
         * @method delete_cache
         */
        public function delete_cache()
        {
            // Super Cache plugin
            if (function_exists('wp_cache_clear_cache')) {
                wp_cache_clear_cache((is_multisite() && is_plugin_active_for_network($this->slug)) ? get_current_blog_id() : '');
            }

            // W3 Total Cache plugin
            if (function_exists('w3tc_pgcache_flush')) {
                w3tc_pgcache_flush();
            }
        }

        /**
         * Displays plugin settings page
         *
         * @since 1.0.0
         * @method display_plugin_settings
         */
        public function display_plugin_settings()
        {
            global $wp_roles;

            // Save settings
            $this->save_plugin_settings();

            // Show settings page
            include_once(BSAS_VIEWS_PATH . 'settings.php');
        }

        /**
         * Enqueue scripts for use in admin panel
         *
         * @since 1.0.0
         * @method enqueue_styles
         * @return
         */
        public function enqueue_scripts()
        {
            if (!isset($this->plugin_hooks_suffix)) {
                return;
            }

            $screen = get_current_screen();

            if ($this->plugin_hooks_suffix === $screen->id) {
                $wp_scripts = wp_enqueue_media();
                wp_enqueue_script($this->slug . '-admin', BSAS_SCRIPTS_URL . 'main' . BSAS_ASSETS_SUFFIX . '.js', ['jquery'], BSAS_VERSION);

                wp_localize_script($this->slug . '-admin', 'bsas_vars', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'plugin_url' => admin_url('options-general.php?page=' . $this->slug)
                ]);
            }
        }

        /**
         * Enqueue stylesheets for use in admin panel
         *
         * @since 1.0.0
         * @method enqueue_styles
         * @return
         */
        public function enqueue_styles()
        {
            if (!isset($this->plugin_hooks_suffix)) {
                return;
            }

            $screen = get_current_screen();

            if ($this->plugin_hooks_suffix === $screen->id) {
                $wp_scripts = wp_scripts();
                $ui = $wp_scripts->query('jquery-ui-core');
            }
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
         * Reset plugin settings
         *
         * @since 1.0.0
         * @method reset_settings
         */
        public function reset_settings()
        {
            try {
                // Check capabilities
                if (!current_user_can('manage_options')) {
                    throw new Exception(__('You do not have permission to access this resource.', $this->slug));
                }

                // Check nonce existance
                if (empty($_POST['_wpnonce'])) {
                    throw new Exception(__('The nonce field must not be empty.', $this->slug));
                }

                // Check nonce validation
                if (!wp_verify_nonce($_POST['_wpnonce'], 'bsas_update_options')) {
                    throw new Exception(__('Security check.', $this->slug));
                }

                $this->settings = $this->defaults;
                update_option('bsas_settings', $this->settings);

                if (defined('DOING_AJAX') && DOING_AJAX) {
                    wp_send_json_success();
                }
            } catch (Exception $e) {
                if (defined('DOING_AJAX') && DOING_AJAX) {
                    wp_send_json_error($e->getMessage());
                }
            }
        }

        /**
         * Save plugin settings to database
         *
         * @since 1.0.0
         * @method save_plugin_settings
         */
        public function save_plugin_settings()
        {
            try {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
                    if (!wp_verify_nonce($_POST['_wpnonce'], 'bsas_update_options')) {
                        wp_die(__('Security check.', $this->slug));
                    }

                    // It either exists in POST or not, so get that boolean as an integer value
                    $this->settings['activated'] = (int) isset($_POST['activated']);

                    // It either exists in POST or not, so get that boolean as an integer value
                    $this->settings['bypass_search_bots'] = (int) isset($_POST['bypass_search_bots']);

                    if (isset($_POST['notice'])) {
                        // Since we're accepting text, we need to filter it
                        $notice = apply_filters('validate_notice', $_POST['notice']);

                        // Save the option
                        $this->settings['notice'] = (!empty($notice)) ? $notice : 'Account Suspended';
                    }

                    if (isset($_POST['http_status'])) {
                        // Let's make sure we're using an acceptable status code
                        $http_status = apply_filters('validate_http_status', $_POST['http_status']);

                        // Save the option
                        $this->settings['http_status'] = $http_status;
                    }

                    // Delete cache
                    $this->delete_cache();

                    // Update settings
                    update_option('bsas_settings', $this->settings);

                    if (defined('DOING_AJAX') && DOING_AJAX) {
                        wp_send_json_success();
                    }
                }
            } catch (Exception $e) {
                if (defined('DOING_AJAX') && DOING_AJAX) {
                    wp_send_json_error($e->getMessage());
                }
            }
        }
    }
}
