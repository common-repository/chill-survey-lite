<?php
class CsiSettings
{
    public function __construct($first = null)
    {
        if($first != null){
            // Aggiungo la pagina al menu di amministrazione
            add_action('admin_menu', array(&$this, 'setupAdminMenus'));
        }
    }

    public function setupAdminMenus()
    {
      add_menu_page("Chill Survey", "Chill Survey", 'manage_options', 'csi_options', array(&$this, 'optionsPage'));
      if(get_locale() == "it_IT"){
          add_submenu_page( 'csi_options',"Domande", "Domande", 'manage_options', 'csi_questions', array(&$this,'questionsPage'));
          add_submenu_page( 'csi_options',"Risultati","Risultati", 'manage_options', 'csi_results', array(&$this,'resultsPage'));
      }else{
            add_submenu_page( 'csi_options',"Questions", "Questions", 'manage_options', 'csi_questions', array(&$this,'questionsPage'));
            add_submenu_page( 'csi_options',"Results","Results", 'manage_options', 'csi_results', array(&$this,'resultsPage'));
        }
        add_submenu_page( 'csi_options',"Feedback","Feedback", 'manage_options', 'csi_feedback', array(&$this,'feedbackPage'));
    }

    public function get_csi_tabs(){ ?>
      <div class="tabs">
        <div class="tab <?php echo (sanitize_text_field($_GET['page']) == 'csi_options') ? 'active' : '';?>"><a href="?page=csi_options"><?php _e("OPTIONS","csi");?></a></div>
        <div class="tab <?php echo (sanitize_text_field($_GET['page']) == 'csi_questions') ? 'active' : '';?>"><a href="?page=csi_questions"><?php _e("QUESTIONS","csi");?></a></div>
        <div class="tab <?php echo (sanitize_text_field($_GET['page']) == 'csi_results') ? 'active' : '';?>"><a href="?page=csi_results"><?php _e("RESULTS","csi");?></a></div>
        <div class="tab <?php echo (sanitize_text_field($_GET['page']) == 'csi_feedback') ? 'active' : '';?>"><a href="?page=csi_feedback"><?php _e("FEEDBACK","csi");?></a></div>
      </div>
    <?php
    }

    public function csipremium_banner(){ ?>
      <div onclick="location.href='https://www.chillsurvey.com';" style="background-image:url(<?php echo plugins_url('/images/banner.gif', dirname(__FILE__));?>);" class="csipremium-banner"></div>
    <?php
    }

    public function optionsPage() {
        $this->get_csi_tabs(); ?>
        <div class="wrap csi-admin-wrap">
        <h1 class="csi-admin-title"><?php _e('Survey Options','csi');?></h1>
        <?php
        if(sanitize_text_field($_POST['save_csi']) && isset($_POST['save_csi'])) {
            $this->save_csi_options();
        }
        $options = $this->get_csi_options();
        ?>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php _e('Survey title (a dedicated page will be created. No one can see this title on the survey page, but we need it to link the survey to an url)','csi');?>*:
                    </th>
                    <td>
                        <input required type="text" name="csi_page" value="<?php echo get_the_title($options['page_id']);?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Logo:
                    </th>
                    <td>
                        <?php
                        if(isset($options['logo']) && !empty($options['logo'])){
                            echo '<span style="width:100px;height:100px;background-image:url('.$options['logo'].');background-size:contain;background-position:center;background-repeat: no-repeat;display:block;"></span>';
                        }
                        ?>
                        <input type="file" id="csi_logo" name="csi_logo">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Background image (if empty the background will be the color chosen)','csi');?>:
                    </th>
                    <td>
                        <?php
                        if(isset($options['bg_image']) && !empty($options['bg_image'])){
                            echo '<span style="width:100px;height:100px;background-image:url('.$options['bg_image'].');background-size:contain;background-position:center;background-repeat: no-repeat;display:block;"></span>';
                        }
                        ?>
                        <input type="file" id="csi_background_image" name="csi_background_image">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Background color','csi');?>:
                    </th>
                    <td>
                        <?php
                        $bg_color_value = '#000000';
                        if(isset($options['bg_color']) && !empty($options['bg_color'])){
                            $bg_color_value = $options['bg_color'];
                        }
                        ?>
                        <input type="text" required class="csi_background_color" name="csi_background_color" value="<?php echo $bg_color_value;?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Text color','csi');?>:
                    </th>
                    <td>
                        <?php
                        $text_color_value = '#ffffff';
                        if(isset($options['text_color']) && !empty($options['text_color'])){
                            $text_color_value = $options['text_color'];
                        }
                        ?>
                        <input type="text" required value="<?php echo $text_color_value;?>" class="csi_background_color" name="csi_text_color">
                    </td>
                </tr>
            </table>
            <input class="csi-admin-button" type="submit" name="save_csi" value="<?php _e('SAVE','csi');?>">
            <button class="csi-admin-button"><a target="_blank" href="<?php echo get_the_permalink($options['page_id']);?>"><?php echo __("Show survey","csi");?></a></button>
        </form>
      </div>
      <?php
      $this->csipremium_banner();
    }

    public function questionsPage(){
        $this->get_csi_tabs(); ?>
        <!--          DOMANDE QUESTIONARIO  !-->
        <div class="wrap csi-admin-wrap">
        <h1 class="csi-admin-title"><?php _e('Survey Questions','csi');?></h1>
        <?php
        if(sanitize_text_field($_POST['save_csi_questions']) !== null && isset($_POST['save_csi_questions'])){
          $this->save_csi_questions();
        }
        $survey = new CsiSurvey();
        $options = $this->get_csi_options();
        ?>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php _e('Start text','csi');?>:
                    </th>
                    <td>
                        <textarea name="start_text"><?php echo $survey->start_text;?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Final text','csi');?>:
                    </th>
                    <td>
                        <textarea name="final_text"><?php echo (!empty($survey->final_text)) ? $survey->final_text : '';?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Redirect text','csi');?>:
                    </th>
                    <td>
                        <input type="text" name="restart_text" value="<?php echo $survey->restart_text;?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Questions (empty answers won\'t show in the survey)','csi');?>:<br>
                    </th>
                    <td id="qcontainer">
                        <?php
                        if(is_array($survey->questions) && !empty($survey->questions)){
                            $margin = 'style="margin-top:0;"';
                            $counter = 0;
                            foreach ($survey->questions as $kq => $question){
                                $question_counter = $counter+1;
                                echo '<p class="questions-paragraph" '.$margin.'><input class="question" type="text" name="question['.$kq.'][domanda]" placeholder="'.__("Question","csi").' '.$question_counter.'" value="'.$question['domanda'].'"><br>';
                                for($i = 0;$i<5;$i++){
                                  $answer_counter = $i+1;
                                    echo '<input class="answer" type="text" name="question['.$kq.'][]" placeholder="'.__("Answer","csi").' '.$answer_counter.'" value="'.$question[$i].'">';
                                }
                                if($counter > 0){
                                    echo '<button class="csi-admin-button" id="removeQuestion">'.__("DELETE","csi").'</button>';
                                }
                                echo '</p>';
                                $counter++;
                                $margin = '';
                                if($counter == count($survey->questions)){
                                    echo '<button class="csi-admin-button newquestion" id="newquestion">'.__("New question","csi").'</button>';
                                }
                            }
                        }else{ ?>
                            <p class="questions-paragraph">
                                <input class="question" type="text" name="question[0][domanda]" placeholder="<?php _e("Question","csi");?> 1"><br>
                                <input class="answer" type="text" name="question[0][]" placeholder="<?php _e("Answer","csi");?> 1">
                                <input class="answer" type="text" name="question[0][]" placeholder="<?php _e("Answer","csi");?> 2">
                                <input class="answer" type="text" name="question[0][]" placeholder="<?php _e("Answer","csi");?> 3">
                                <input class="answer" type="text" name="question[0][]" placeholder="<?php _e("Answer","csi");?> 4">
                                <input class="answer" type="text" name="question[0][]" placeholder="<?php _e("Answer","csi");?> 5">
                            </p>
                            <button class="csi-admin-button newquestion" id="newquestion"><?php _e("New question",'csi');?></button>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3><input type="checkbox" name="delete_csi_results"> <?php _e("Delete old results (check if you've edited an existing survey questions, to have a new series of results)","csi");?></h3>
            <input class="csi-admin-button" type="submit" name="save_csi_questions" value="<?php _e('SAVE','csi');?>">
            <button class="csi-admin-button"><a target="_blank" href="<?php echo get_the_permalink($options['page_id']);?>"><?php echo __("Show survey","csi");?></a></button>
        </form>
        <script>
        jQuery(function() {
            var questionsContainer = jQuery('#qcontainer');
            var i = jQuery('#qcontainer p').size();
            var j = i+1;

            jQuery('#newquestion').live('click', function() {
                jQuery(this).remove();
                jQuery('<p class="questions-paragraph"><input class="question" type="text" name="question['+i+'][domanda]" value="" placeholder="<?php _e("Question","csi");?> '+j+'" /><br><input placeholder="<?php _e("Answer","csi");?> 1" class="answer" type="text" name="question['+i+'][]" value="" /><input placeholder="<?php _e("Answer","csi");?> 2" class="answer" type="text" name="question['+i+'][]" value="" /><input placeholder="<?php _e("Answer","csi");?> 3" class="answer" type="text" name="question['+i+'][]" value="" /><input placeholder="<?php _e("Answer","csi");?> 4" class="answer" type="text" name="question['+i+'][]" value="" /><input placeholder="<?php _e("Answer","csi");?> 5" class="answer" type="text" name="question['+i+'][]" value="" /><button class="csi-admin-button" id="removeQuestion"><?php _e("DELETE","csi");?></button></p><button class="csi-admin-button newquestion" id="newquestion"><?php _e("NEW QUESTION","csi");?></span>').appendTo(questionsContainer);
                i++;
                j++;
                return false;
            });

            jQuery('#removeQuestion').live('click', function() {
                if (i > 1) {
                    jQuery(this).parents('p').remove();
                    i--;
                    j--;
                }
                return false;
            })
        });
        </script>
      </div>
    <?php
    $this->csipremium_banner();
   }

    public function resultsPage(){
        $survey = new CsiSurvey();
        global $wpdb;
        $this->get_csi_tabs(); ?>
        <div class="wrap csi-admin-wrap">
        <div id="custom-id">
            <h1 class="csi-admin-title"><?php _e("Results","csi");?></h1>
            <form action="" method="post">
                <label for="widget_csi_dal"><?php _e("From","csi");?> </label><input type="date" name="widget_csi_dal" id="widget_csi_dal">
                <label for="widget_csi_al"><?php _e("To","csi");?> </label><input type="date" name="widget_csi_al" id="widget_csi_al">
                <button style="width:25%;text-align: center;padding:5px;background-color:#0085ba;color:white;margin-top:5px;cursor:pointer;" type="submit" value="filter" name="filtro_date_widget_csi" id="filtro_date_widget_csi"><?php _e("Filter","csi");?></button>
            </form>
            <?php
            if(sanitize_text_field($_POST['filtro_date_widget_csi']) == 'filter'){
                $dal = sanitize_text_field($_POST['widget_csi_dal']).' 00:00:00';
                $al = sanitize_text_field($_POST['widget_csi_al']).' 23:59:59';
                $myquery = "SELECT * FROM ".$wpdb->prefix."csi_results WHERE time >= '$dal' AND time <= '$al' AND survey_id = 1 ORDER BY time DESC";
            } else{
                $myquery = 'SELECT * FROM '.$wpdb->prefix.'csi_results WHERE survey_id = 1 ORDER BY time DESC';
            }
            $risultati = $wpdb->get_results($myquery);
            $nrisultati = count($risultati);
            echo '<h2>'.__("Total surveys completed","csi").': '.$nrisultati.'</h2>';
            if(is_array($risultati) && !empty($risultati)){
                ?>
                <div class="tables">
                    <table class="mytable" style="width:100%;margin-top:20px;">
                        <thead style="background-color:#dcd9d9;">
                        <tr>
                            <?php
                            $steps = array();
                            $qcounter = 0;
                            foreach($survey->questions as $kq => $question) {
                                $qcounter++;
                                echo '<th>'.$question['domanda'].'</th>';
                            }
                            ?>
                            <th><?php _e("Date","csi");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($risultati as $risultato){
                            $qc = 1;
                            $answers = unserialize(base64_decode($risultato->answers));
                            echo '<tr>';
                            foreach($survey->questions as $kq => $question) {
                              $text_answer = $question[intval($answers['step'.$qc]-1)];
                              $qc++;
                              echo '<td>'.$text_answer.'</td>';
                            }
                            $diff = count($survey->questions) - count($answers);
                            if($diff > 0){
                                for($i=0;$i<$diff;$i++){
                                    echo '<td>\</td>';
                                }
                            }
                            echo '<td>'.$risultato->time.'</td>';
                            echo '</tr>';
                        } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
      </div>
    <?php
    $this->csipremium_banner();
    }

    public function feedbackPage(){
        $this->get_csi_tabs(); ?>
        <div class="wrap csi-admin-wrap">
        <?php
        if(sanitize_text_field($_POST['csi_send_feedback']) !== null && isset($_POST['csi_send_feedback'])){
            $to = 'simone.devita@logicimage.it';
            $subject = 'Feedback CSI';
            $headers = array('Content-Type: text/html; charset=UTF-8','From: Plugin CSI <info@logicimage.it>');
            $message = 'Nuovo feedback per il plugin CSI: <br>';
            $message .= 'Nome: '.sanitize_text_field($_POST['csi_name_feedback']).'<br>';
            $message .= 'Valutazione: '.sanitize_text_field($_POST['csi_rating']).'<br>';
            $message .= 'Email: '.sanitize_text_field($_POST['csi_email_feedback']).'<br>';
            $message .= 'Messaggio: '.sanitize_text_field($_POST['csi_message_feedback']).'<br>';
            wp_mail( $to, $subject, $message, $headers );
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e("Thank you! Your message has been sent","csi");?></p>
            </div>
            <?php
        } ?>
        <h1 class="csi-admin-title"><?php _e("Leave a feedback","csi");?></h1>
        <h2><?php _e("Do you like our plugin? Have you any advice to improve it?","csi");?></h2>
        <form method="post">
            <label style="display:block;width:300px;margin:15px 0;" for="csi_name_feedback"><?php _e("Name","csi");?>:</label>
            <input style="display:block;width:300px;margin:15px 0;" name="csi_name_feedback">
            <label style="display:block;width:300px;margin:15px 0;" for="csi_email_feedback">Email:</label>
            <input style="display:block;width:300px;margin:15px 0;" type="email" name="csi_email_feedback">
            <label style="display:block;width:300px;margin:15px 0;" for="csi_rating"><?php _e("How do you rate our plugin?","csi");?>:</label>
            <input type="hidden" id="csi_rating" name="csi_rating" value="">
            <div id="stars" class="stars">
              <div onclick="get_feedback_stars(1);" class="star">
                <i class="fa fa-star-o"></i>
              </div>
              <div onclick="get_feedback_stars(2);" class="star">
                <i class="fa fa-star-o"></i>
              </div>
              <div onclick="get_feedback_stars(3);" class="star">
                <i class="fa fa-star-o"></i>
              </div>
              <div onclick="get_feedback_stars(4);" class="star">
                <i class="fa fa-star-o"></i>
              </div>
              <div onclick="get_feedback_stars(5);" class="star">
                <i class="fa fa-star-o"></i>
              </div>
            </div>
            <label style="display:block;width:300px;margin:15px 0;" for="csi_message_feedback"><?php _e("Message","csi");?>:</label>
            <textarea style="display:block;width:300px;margin:15px 0;" name="csi_message_feedback"></textarea>
            <input class="csi-admin-button" type="submit" name="csi_send_feedback" value="<?php _e("Send feedback","csi");?>">
        </form>
      </div>
    <?php
    $this->csipremium_banner();
    }

    private function save_csi_options(){
        global $wpdb;
        if(sanitize_text_field($_POST['save_csi']) !== null && isset($_POST['save_csi'])){
            $old_page = (isset($this->get_csi_options('page_id')['page_id'])) ? $this->get_csi_options('page_id')['page_id'] : 0;
            $old_logo = (isset($this->get_csi_options('logo')['logo'])) ? $this->get_csi_options('logo')['logo'] : 0;
            $old_bg_image = (isset($this->get_csi_options('bg_image')['bg_image'])) ? $this->get_csi_options('bg_image')['bg_image'] : 0;
            $wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix."csi_options",0));
            //SAVING THE PAGE
            if(sanitize_text_field($_POST['csi_page']) !== null && sanitize_text_field($_POST['csi_page']) != get_the_title($old_page)){
              $survey_page = wp_insert_post(array(
               'post_title'     => sanitize_text_field($_POST['csi_page']),
               'post_type'      => 'page',
               'comment_status' => 'closed',
               'ping_status'    => 'closed',
               'post_content'   => '',
               'post_status'    => 'publish',
               'post_author'    => get_current_user_id(),
               'menu_order'     => 0,
              ));
              if ( $survey_page && ! is_wp_error( $survey_page ) ){
                update_post_meta( $survey_page, '_wp_page_template', 'survey_template.php' );
              }
                $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%d,%d)",'page_id',$survey_page,1));
            }else{
                $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%d,%d)",'page_id',$old_page,1));
            }
            //SAVING THE LOGO
            $logo = $_FILES['csi_logo'];
            if($logo['size'] > 0 && sanitize_file_name($_FILES['csi_logo']['name']) !== null){
                $upload_overrides = array('test_form' => false);
                $uploaded = wp_handle_upload($logo, $upload_overrides);
                // Error checking using WP functions
                if(is_wp_error($uploaded)){
                    echo __("Error uploading file","csi").': '. $uploaded->get_error_message();
                }else{
                    $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%s,%d)",'logo',$uploaded['url'],1));
                }
            }else{
                if($old_logo !== 0){
                    $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%s,%d)",'logo',$old_logo,1));
                }
            }
            //SAVING THE BACKGROUND
            $background_image = $_FILES['csi_background_image'];
            if($background_image['size'] > 0 && sanitize_file_name($_FILES['csi_background_image']['name']) !== null){
                $upload_overrides = array('test_form' => false);
                $bg_uploaded = wp_handle_upload($background_image, $upload_overrides);
                // Error checking using WP functions
                if(is_wp_error($bg_uploaded)){
                    echo __("Error uploading file","csi").': '. $bg_uploaded->get_error_message();
                }else{
                    $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%s,%d)",'bg_image',$bg_uploaded['url'],1));
                }
            }else{
              if($old_bg_image !== 0){
                $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%s,%d)",'bg_image',$old_bg_image,1));
              }
            }
            if(sanitize_text_field($_POST['csi_background_color']) !== null && !empty(sanitize_text_field($_POST['csi_background_color']))) {
                $background = sanitize_text_field($_POST['csi_background_color']);
                $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%s,%d)",'bg_color',$background,1));
            }
            //SAVING THE TEXT COLOR
            if(sanitize_text_field($_POST['csi_text_color']) !== null && !empty(sanitize_text_field($_POST['csi_text_color']))) {
                $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_options(name, value, survey_id) VALUES (%s,%s,%d)", 'text_color', sanitize_text_field($_POST['csi_text_color']),1));
            }
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e("Options saved succesfully","csi");?></p>
            </div>
            <?php
        }
    }
    private function save_csi_questions(){
        global $wpdb;
        $survey = new CsiSurvey();
        $toCheck = array('start_text','final_text','restart_text','question');
        $ok = true;
        foreach ($toCheck as $c){
            if(sanitize_text_field($_POST[$c]) === null || empty(sanitize_text_field($_POST[$c]))) {
                $ok = false;
            }
        }
        if($ok){
            $old_questions = $wpdb->get_results("SELECT questions FROM ".$wpdb->prefix."csi_questions WHERE survey_id = 1");
            if(!empty($old_questions)){
              $old_questions = $old_questions[0]->questions;
            }
            $wpdb->query($wpdb->prepare("TRUNCATE TABLE ".$wpdb->prefix."csi_questions",0));
            $start_text = sanitize_text_field($_POST['start_text']);
            $final_text = sanitize_text_field($_POST['final_text']);
            $restart_text = sanitize_text_field($_POST['restart_text']);
            $questions = $_POST['question'];
            if(is_array($questions)){
              foreach($questions as $knq => $nq){
                if(is_array($nq)){
                  foreach($nq as $kna => $na){
                    if(empty(sanitize_text_field($na))) {
                      unset($questions[$knq][$kna]);
                    }
                  }
                }
              }
            }
            $questions = base64_encode(serialize($questions));
            if($questions != $old_questions && !empty($old_questions)){
              if(sanitize_text_field($_POST['delete_csi_results']) !== null && sanitize_text_field($_POST['delete_csi_results']) == 'on'){
                $wpdb->query($wpdb->prepare("TRUNCATE TABLE ".$wpdb->prefix."csi_results",0));
              }
            }
            $wpdb->query( $wpdb->prepare("INSERT INTO ".$wpdb->prefix."csi_questions(start_text, final_text, restart_text, questions, survey_id) VALUES (%s,%s,%s,%s,%d)",$start_text, $final_text, $restart_text, $questions,1));
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e("Options saved succesfully","csi");?></p>
            </div>
            <?php
        }else{ ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e("All fields are required","csi");?></p>
            </div>
        <?php }
    }
    public function get_csi_options($option = null){
        global $wpdb;
        $csi_options = array();
        $results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."csi_options WHERE survey_id = 1");
        if(is_array($results) && !empty($results)){
            foreach ($results as $result){
                if($option == null || $option == $result->name)
                $csi_options[$result->name] = stripslashes($result->value);
            }
        }
        return $csi_options;
    }

}
?>
