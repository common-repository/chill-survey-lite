<?php
/*
Plugin Name: Chill Survey Lite
Version: 1.0
Author: Logic Image Srl
Description: The coolest solution to create online surveys, customer satisfaction interviews and sentiment analytics.You can create questions and analyze the users response. Please read the documentation before using the plugin. 
Author URI: https://www.logicimage.it
*/
define( 'CSI_PATH', plugin_dir_path( __FILE__ ) );
define( 'CSI_URL', plugin_dir_url( __FILE__ ) );
require_once( CSI_PATH . 'classes/survey.php' );
require_once( CSI_PATH . 'classes/settings.php' );
require_once( CSI_PATH . 'tables.php' );
add_action( 'admin_enqueue_scripts', 'enqueue_csi_script' );
function enqueue_csi_script(){
    wp_enqueue_script( 'mycsimainscript', plugins_url('js/csi.js', __FILE__ ), array( 'jquery' ), false, true );
}
//---------START ADDING COLORPICKER TO SETTING PAGE-----------------

add_action( 'admin_enqueue_scripts', 'csi_enqueue_color_picker' );
function csi_enqueue_color_picker( $hook_suffix ) {
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'csi_color_picker', plugins_url('js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
function load_csi_admin_style($hook) {
  $possible_hooks = array('toplevel_page_csi_options','chill-survey_page_csi_questions','chill-survey_page_csi_questions','chill-survey_page_csi_results','chill-survey_page_csi_feedback','chill-survey_page_csi_questions','chill-survey_page_csi_questions','chill-survey_page_csi_results','chill-survey_page_csi_feedback');
  if(in_array($hook,$possible_hooks)) {
    wp_enqueue_style( 'custom_csi_admin_css', plugins_url('css/admin.css', __FILE__) );
    wp_enqueue_script( 'mycsifascript', "https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css", array( 'jquery' ), false, true );
  }
}
add_action( 'admin_enqueue_scripts', 'load_csi_admin_style' );
//------------COLOR PICKER END---------------------------------------

//------------CREATING OR UPDATING TABLES-----------------------------

register_activation_hook( __FILE__, 'csi_install' );
register_uninstall_hook( __FILE__, 'csi_uninstall' );
function csi_update_db_check() {
    global $my_db_version;
    if ( get_site_option( 'my_csi_db_version' ) != $my_db_version ) {
        csi_install();
    }
}
function get_csi_language_files(){
    load_plugin_textdomain( 'csi', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}
add_action( 'plugins_loaded', 'csi_update_db_check' );
add_action( 'plugins_loaded', 'get_csi_language_files' );


//---------------------TABLES END-------------------------------------

//DISPLAYING SETTINGS PAGE
$csi_settings = new CsiSettings(true);

function save_csi(){
    global $wpdb;
    $survey = new CsiSurvey();
    $steps = array();
    $qcounter = 0;
    foreach($survey->questions as $kq => $question) {
        $qcounter++;
        $steps[] = 'step' . $qcounter;
    }
    $ok = true;
    foreach($steps as $step){
        if(sanitize_text_field($_POST[$step]) === null){
            $ok = false;
        }
    }
    if($ok){
        $answers = array();
        foreach ($steps as $step){
            $answers[$step] = sanitize_text_field($_POST[$step]);
        }
        $answers = base64_encode(serialize($answers));
        $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_results(survey_id,answers) VALUES (%d,%s)",1,$answers));
    }
    die();
}
add_action( 'wp_ajax_save_csi', 'save_csi' );
add_action( 'wp_ajax_nopriv_save_csi', 'save_csi' );

// -------------- REGISTER FRONTEND STYLES AND SCRIPTS -------------
function load_csi_script(){
  wp_register_script('survey_template',plugins_url('js/survey_template.js',__FILE__), array('jquery'));
  wp_enqueue_script('survey_template');
  wp_register_script('csi_bootstrap_script',plugins_url('js/bootstrap.min.js', __FILE__), array('jquery'));
  wp_enqueue_script('csi_bootstrap_script');
}
add_action('wp_enqueue_scripts','load_csi_script');

function load_csi_style(){
  wp_register_style('csi_bootstrap_style',plugins_url('css/bootstrap.min.css', __FILE__));
  wp_enqueue_style('csi_bootstrap_style');
  wp_register_style('csi_main_style',plugins_url('css/style.css', __FILE__));
  wp_enqueue_style('csi_main_style');
  wp_register_style('csi_fontawesome',plugins_url('css/font-awesome.min.css', __FILE__));
  wp_enqueue_style('csi_fontawesome');
}
add_action('wp_enqueue_scripts','load_csi_style',999999);

//ADDING CUSTOM TEMPLATE
add_action( 'template_include', 'csi_redirect_template' );
function csi_redirect_template( $template ) {
    $plugindir = dirname( __FILE__ );
    if ( is_page_template( 'survey_template.php' )) {
        $template = $plugindir . '/survey_template.php';
    }
    return $template;
}
