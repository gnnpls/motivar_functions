<?php
/*
Plugin Name: Wordpress Admin tools
Plugin URI: https://www.motivar.io
Description: Hide unwanted texts for clients and run custom php codes and shortcodes (for developers mostly)
Version: 1.3.4
Author: Giannopoulos Nikolaos
Author URI: https://www.motivar.io
Text Domain:       github-updater
GitHub Plugin URI: https://github.com/gnnpls/motivar_functions
GitHub Branch:     master
*/
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
$path=plugin_dir_path(__FILE__).'../motivar_functions_child';
/*global things to check*/
require_once('global_sites_code.php');
//admin php file
if (is_admin()) {
    if (get_option('motivar_functions_debug')) {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }
}
require_once('admin/admin_functions.php');
/*end of gloabl things*/

    function motivar_functions_theme_enqueue_styles()
    {
        wp_enqueue_style('motivar-design', plugin_dir_url(__FILE__) . '../motivar_functions_child/guest/mystyle.css', array(), '', 'all');
        wp_enqueue_script('motivar-myscript', plugin_dir_url(__FILE__) . '../motivar_functions_child/guest/myscript.js', array(), array(), true);
    }


/*check if motivar child exists*/
if (file_exists($path)) {
    //custom shortcodes
    require_once($path.'/guest/custom_shortcodes.php');
    /*custom post_types*/
    require_once($path.'/custom_types/post_types.php');
    require_once($path.'/custom_types/tax_types.php');
    require_once($path.'/email_functions.php');
    require_once($path.'/cron_functions.php');
    require_once($path.'/custom_widgets.php');
    add_action('wp_enqueue_scripts', 'motivar_functions_theme_enqueue_styles', 20);
}
else
{
$zip = new ZipArchive;
if (file_exists(plugin_dir_path(__FILE__).'/motivar_functions_child.zip'))
{
if ($zip->open(plugin_dir_path(__FILE__).'/motivar_functions_child.zip') === TRUE) {
    $zip->extractTo(plugin_dir_path(__FILE__).'/../');
    $zip->close();
}
}
}


if (!is_admin()) {
    add_action('wp_footer', 'add_this_script_footer');
}

if (get_option('motivar_functions_admin_only')) {
    add_action('init', 'motivar_functions_redirect');
}


function motivar_functions_redirect()
{
    // Current Page
    global $pagenow;

    // Check to see if user in not logged in and not on the login page
    if ($pagenow != 'wp-login.php' && !is_user_logged_in()) {
        auth_redirect();
    }
}




function add_this_script_footer()
{
?>
<script>
<?php
    if (get_option('motivar_functions_google')) {
        echo base64_decode(get_option('motivar_functions_google'));
    }
    if (get_option('motivar_functions_hotjar')) {
        echo base64_decode(get_option('motivar_functions_hotjar'));
    }
?>
</script>
<?php
}




//custom_login_css
function motivar_functions_login()
{
    wp_enqueue_style('login-style', plugin_dir_url(__FILE__) . 'login/login_style.css', array(), '', 'all');

}

add_action('login_enqueue_scripts', 'motivar_functions_login', 20);
function motivar_functions_login_url()
{
    return 'https://motivar.io';
}
function motivar_functions_login_title()
{
    return "Web Services Corfu";
}
add_filter('login_headerurl', 'motivar_functions_login_url');
add_filter('login_headertitle', 'motivar_functionsp_login_title');


/* Hide WP version strings from scripts and styles
 * @return {string} $src
 * @filter script_loader_src
 * @filter style_loader_src
 */

function motivar_functions_remove_wp_version_strings($src)
{
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if (!empty($query['ver']) && $query['ver'] === $wp_version) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'motivar_functions_remove_wp_version_strings');
add_filter('style_loader_src', 'motivar_functions_remove_wp_version_strings');

/* Hide WP version strings from generator meta tag */
function motivar_functions_remove_version()
{
    return '';
}
add_filter('the_generator', 'motivar_functions_remove_version');