<?php
/*
Plugin Name: WP Smart Banner
Plugin URI: https://www.3eighty.nl/smartbanner
Description: A customisable WordPress smart app banner for iOS and Android.
Version: 1.0.0
Author: Yoeri Dekker
Author URI: https://www.3eighty.nl
Text Domain: wp-smartbanner
Domain Path: /languages
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WP_Smartbanner
{

    /** @var string The plugin version number. */
    var $version = '1.0.0';

    /** @var array The plugin settings array. */
    var $settings = [];

    /** @var array The plugin options array. */
    var $options = [];

    /** @var array Storage for class instances. */
    var $instances = [];

    /** @var array Storage for general meta fields. */
    var $general_meta = [
        'smartbanner:title'                 => 'app_name',
        'smartbanner:author'                => 'author_name',
        'smartbanner:price'                 => 'price',
        'smartbanner:button'                => 'view_label',
        'smartbanner:close-label'           => 'close_label'
    ];

    /** @var array Storage for ios meta fields. */
    var $ios_meta = [
        'smartbanner:button-url-apple'      => 'apple_app_store_url',
        'smartbanner:icon-apple'            => 'apple_app_store_icon_url',
        'smartbanner:price-suffix-apple'    => 'apple_app_store_tagline',
    ];

    /** @var array Storage for android meta fields. */
    var $android_meta = [
        'smartbanner:button-url-google'     => 'google_play_store_url',
        'smartbanner:icon-google'           => 'google_play_store_icon_url',
        'smartbanner:price-suffix-google'   => 'google_play_store_tagline',
    ];

    /** @var array Storage for default meta values. */
    public static $defaults = [];

    /**
     * __construct
     *
     * A dummy constructor to ensure WP_Smartbanner is only setup once.
     *
     * @date	23/06/12
     * @since	1.0.0
     *
     * @param	void
     * @return	void
     */
    function __construct()
    {
		// Do nothing.
    }

    /**
     * initialize
     *
     * Sets up the WP_Smartbanner plugin.
     *
     * @date	03/11/20202
     * @since	1.0.0
     *
     * @param	void
     * @return	void
     */
    function initialize()
    {

		// Define constants.
        $this->define('WP_SMARTBANNER', true);
        $this->define('WP_SMARTBANNER_PATH', plugin_dir_path(__FILE__));
        $this->define('WP_SMARTBANNER_BASENAME', plugin_basename(__FILE__));
        $this->define('WP_SMARTBANNER_VERSION', $this->version);
		
		// Define settings.
        $this->settings = [
            'name'      => _x('WP Smartbanner', 'backend', 'wp-smartbanner'),
            'slug'      => dirname(ACF_BASENAME),
            'version'   => WP_SMARTBANNER_VERSION,
            'basename'  => WP_SMARTBANNER_BASENAME,
            'path'      => WP_SMARTBANNER_PATH,
            'file'      => __FILE__,
            'url'       => plugin_dir_url(__FILE__),
        ];

        // Include admin.
		if( is_admin() ) {
            require_once 'includes/admin/options_page.php';
            new WP_Smartbanner_Options();
        }
		
		// Add actions.
        add_action( 'init', [$this, 'init'], 5 );
        add_action( 'wp_head', [ $this, 'wp_head_meta' ], 99 );
        add_action( 'wp_enqueue_scripts', [$this,'smartbanner_scripts'] );
        add_action( 'admin_enqueue_scripts', [$this,'smartbanner_admin_scripts'] );
    }

    public function smartbanner_scripts(){
        if( $this->has_setting('url') ){
            wp_enqueue_style( 'smartbanner', $this->get_setting('url') . 'assets/css/smartbanner.css' );
            wp_enqueue_script( 'smartbanner', $this->get_setting('url') . 'assets/js/smartbanner.js' , [], $this->get_setting('version'), true );
        }
    }

    public function smartbanner_admin_scripts( $hook ) {
        if( 'toplevel_page_smartbanner' === $hook && $this->has_setting('url') ){
            wp_enqueue_script( 'smartbanner-admin', $this->get_setting('url') . 'assets/js/smartbanner-admin.js', ['jquery'], $this->get_setting('version'), true );
        }
    }

    public function wp_head_meta(){

        $platforms = [];

        if( $this->has_option('show_on_ios') ){
            $platforms[] = 'ios';
            foreach( $this->ios_meta as $key => $option ){
                if( $this->has_option( $option ) ){
                    printf('<meta name="%s" content="%s">%s', $key, $this->get_option($option), PHP_EOL );
                }
            }
        }

        if( $this->has_option('show_on_android') ){
            $platforms[] = 'android';
            foreach( $this->android_meta as $key => $option ){
                if( $this->has_option( $option ) ){
                    printf('<meta name="%s" content="%s">%s', $key, $this->get_option($option), PHP_EOL );
                }
            }
        }

        if( !empty($platforms) ){
            echo '<!-- Start SmartBanner configuration -->' . PHP_EOL;
            echo '<meta name="smartbanner:disable-positioning" content="true">' . PHP_EOL;
            foreach( $this->general_meta as $key => $option ){
                if( $this->has_option( $option ) ){
                    printf('<meta name="%s" content="%s">%s', $key, $this->get_option($option), PHP_EOL );
                }
            }
            printf('<meta name="smartbanner:enabled-platforms" content="%s">%s', implode(',', $platforms ), PHP_EOL );
            echo '<!-- End SmartBanner configuration -->' . PHP_EOL;
        }

    }

    /**
     * init
     *
     * Completes the setup process on "init" of earlier.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	void
     * @return	void
     */
    function init()
    {
		
		// Bail early if called directly from functions.php or plugin file.
        if (!did_action('plugins_loaded')) {
            return;
        }

        // Load textdomain file.
        $this->wp_smartbanner_load_textdomain();

        // Define options.
        $this->options = get_option('wp_smartbanner_options_fields');

        // Set translations for default values.
        self::$defaults = [
            'app_name'                  => _x('Title', 'widget', 'wp-smartbanner'),
            'author_name'               => _x('Author', 'widget', 'wp-smartbanner'),
            'price'                     => _x('Price', 'widget', 'wp-smartbanner'),
            'view_label'                => _x('Open', 'widget', 'wp-smartbanner'),
            'close_label'               => _x('Close', 'widget', 'wp-smartbanner'),
            'apple_app_store_tagline'   => _x('Tagline', 'widget', 'wp-smartbanner'),
            'google_play_store_tagline' => _x('Tagline', 'widget', 'wp-smartbanner'),
        ];
        
		// Update url setting. Allows other plugins to modify the URL (force SSL).
        //acf_update_setting('url', plugin_dir_url(__FILE__));
		
		
        //echo '<pre>'; var_dump([$this->settings, $this->options]); die;

    }

    /**
     * wp_smartbanner_load_textdomain
     *
     * Loads the plugin's translated strings similar to load_plugin_textdomain().
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $locale The plugin's current locale.
     * @return	void
     */
    public function wp_smartbanner_load_textdomain( $domain = 'wp-smartbanner' ) {
        
        // Get locale.
        $locale = determine_locale();

        // Create .mo filename.
        $mofile = $domain . '-' . $locale . '.mo';
        
        // Try to load from the languages directory first.
        if( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile ) ) {
            return true;
        }
        
        // Load from plugin lang folder.
        return load_textdomain( $domain, WP_SMARTBANNER_PATH . 'languages/' . $mofile );
    }

    /**
     * define
     *
     * Defines a constant if doesnt already exist.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The constant name.
     * @param	mixed $value The constant value.
     * @return	void
     */
    function define($name, $value = true)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * has_setting
     *
     * Returns true if a setting exists for this name.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The setting name.
     * @return	boolean
     */
    function has_setting($name)
    {
        return isset($this->settings[$name]);
    }

    /**
     * get_setting
     *
     * Returns a setting or null if doesn't exist.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The setting name.
     * @return	mixed
     */
    function get_setting($name)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : null;
    }

    /**
     * update_setting
     *
     * Updates a setting for the given name and value.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The setting name.
     * @param	mixed $value The setting value.
     * @return	true
     */
    function update_setting($name, $value)
    {
        $this->settings[$name] = $value;
        return true;
    }

    /**
     * has_option
     *
     * Returns true if an option exists for this name.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The option name.
     * @return	boolean
     */
    function has_option($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * get_option
     *
     * Returns option or null if doesn't exist.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The option name.
     * @return	mixed
     */
    function get_option($name)
    {
        $value = isset($this->options[$name]) ? $this->options[$name] : null;
        if( empty( $value ) && isset( self::$defaults[ $name ] )  ){
            return self::$defaults[ $name ];
        }
        return $value;
    }

    /**
     * set_option
     *
     * Sets option for the given name and value.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $name The option name.
     * @param	mixed $value The option value.
     * @return	void
     */
    function set_option($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * get_instance
     *
     * Returns an instance or null if doesn't exist.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $class The instance class name.
     * @return	object
     */
    function get_instance($class)
    {
        $name = strtolower($class);
        return isset($this->instances[$name]) ? $this->instances[$name] : null;
    }

    /**
     * new_instance
     *
     * Creates and stores an instance of the given class.
     *
     * @date	03/11/2020
     * @since	1.0.0
     *
     * @param	string $class The instance class name.
     * @return	object
     */
    function new_instance($class)
    {
        $instance = new $class();
        $name = strtolower($class);
        $this->instances[$name] = $instance;
        return $instance;
    }

}

/*
 * WP_Smartbanner
 *
 * The main function responsible for returning the one true acf Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $wp_smartbanner = wp_smartbanner(); ?>
 *
 * @date	03/11/2020
 * @since	1.0.0
 *
 * @param	void
 * @return	WP_Smartbanner
 */
function wp_smartbanner()
{
    global $wp_smartbanner;
	
	// Instantiate only once.
    if (!isset($wp_smartbanner)) {
        $wp_smartbanner = new WP_Smartbanner();
        $wp_smartbanner->initialize();
    }
    return $wp_smartbanner;
}

// Instantiate.
wp_smartbanner();
