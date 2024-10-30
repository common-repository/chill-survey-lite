<?php
class CsiSurvey {
    public $id;
    public $start_text;
    public $final_text;
    public $restart_text;
    public $questions;

    public function __construct($id = null) {
        global $wpdb;
        if($id == null){
           $this->id = 1;
           $this->start_text = stripslashes($wpdb->get_results("SELECT start_text FROM ".$wpdb->prefix."csi_questions WHERE survey_id = 1")[0]->start_text);
           $this->final_text = stripslashes($wpdb->get_results("SELECT final_text FROM ".$wpdb->prefix."csi_questions WHERE survey_id = 1")[0]->final_text);
           $this->restart_text = stripslashes($wpdb->get_results("SELECT restart_text FROM ".$wpdb->prefix."csi_questions WHERE survey_id = 1")[0]->restart_text);
           $this->questions = array();
           $questions = $wpdb->get_results("SELECT questions FROM ".$wpdb->prefix."csi_questions WHERE survey_id = 1");
           if(is_array($questions) && !empty($questions)){
               $questions = unserialize(base64_decode($questions[0]->questions));
               foreach($questions as $kqs => $question){
                 if(is_array($question) && !empty($question)){
                   foreach($question as $kq => $q){
                     $questions[$kqs][$kq] = stripslashes($q);
                   }
                 }
               }
               $this->questions = $questions;
           }
       }
    }
}
