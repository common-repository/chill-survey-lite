<?php
global $my_db_version;
$my_db_version = '1.0';
function csi_install() {
    global $wpdb;
    global $my_db_version;
    $table_name = $wpdb->prefix.'csi_options';
    $table_name2 = $wpdb->prefix.'csi_questions';
    $table_name3 = $wpdb->prefix.'csi_results';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        survey_id int(11) NOT NULL,
        name VARCHAR(255) NOT NULL,
        value VARCHAR(255) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;
    CREATE TABLE $table_name2 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        questions LONGTEXT NOT NULL,
        start_text VARCHAR(255) NOT NULL,
        final_text VARCHAR(255) NOT NULL,
        restart_text VARCHAR(255) NOT NULL,
        survey_id int(11) NOT NULL,
        UNIQUE KEY id (id)
    )$charset_collate;
    CREATE TABLE $table_name3 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        survey_id mediumint(9) NOT NULL,
        answers VARCHAR(255) NOT NULL,
        time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY id (id)
    )$charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    update_option( 'my_csi_db_version', $my_db_version );
}
function csi_uninstall(){
  global $wpdb;
  global $my_db_version;
  $table_name = $wpdb->prefix.'csi_options';
  $table_name2 = $wpdb->prefix.'csi_questions';
  $table_name3 = $wpdb->prefix.'csi_results';
  $charset_collate = $wpdb->get_charset_collate();
  $sql = "DROP TABLE IF EXISTS $table_name,$table_name2,$table_name3";
  delete_option('my_csi_db_version');
  $wpdb->query($sql);
}
