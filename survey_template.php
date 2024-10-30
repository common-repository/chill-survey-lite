<?php /* Template Name: csi*/
$settings = new CsiSettings();
$survey = new CsiSurvey();
$options = $settings->get_csi_options();
$bg_color = '#000000';
$text_color = '#ffffff';
if(isset($options['bg_color'])){
    $bg_color = $options['bg_color'];
}
if(isset($options['text_color'])){
    $text_color = $options['text_color'];
}
if(isset($options['logo'])){
    $logo = $options['logo'];
}else{
    $logo = false;
}
$body_style = (isset($options->bg_image)) ? 'background-image:url('.$options['bg_image'].');' : '';
$body_style .= 'background-image:url('.$options['bg_image'].');background-color:'.$bg_color.';color:'.$text_color.';';
$play_btn_style =  'background-color: '.$text_color.';';
$play_btn_i_style = 'color:'.$bg_color.';';
$restart_style = 'background-color: '.$text_color.';color: '.$bg_color.';';
$main_container_style = 'border:10px solid '.$text_color.';';
?>
<!doctype html>
<html class="no-js">
<head>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
    <meta charset="utf-8" />
    <meta content='initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport' />
    <?php wp_head();?>
</head>
<body style="<?php echo $body_style;?>">
<div data-steps="<?php echo count($survey->questions);?>" data-homeurl="<?php echo get_home_url();?>" id="home" class="main-container container-fluid">
    <?php
    if($logo != false){ ?>
        <div class="row">
            <div class="col-md-12">
                <div class="logo">
                    <img src="<?php echo $logo;?>">
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <h1><?php echo $survey->start_text;?></h1>
            <div onclick="slidequiz('#home','#step1');" class="play-btn" style="<?php echo $play_btn_style;?>">
                <i style="<?php echo $play_btn_i_style;?>" class="fa fa-play"></i>
            </div>
        </div>
    </div>
</div>
<?php
$qcounter = 0;
$steps = array();
foreach($survey->questions as $kq => $question){
    $nrisposte = count($question)-1;
    $qcounter++;
    $steps[] = 'step'.$qcounter;?>
    <div data-nrisposte="<?php echo $nrisposte;?>" id="step<?php echo $qcounter;?>" class="main-container container-fluid steps">
        <div class="row">
            <div class="col-md-12 text-center" style="<?php echo $main_container_style;?>">
                <?php
                if($qcounter > 1){?>
                    <h4 onclick="slideback('#step<?php echo $qcounter;?>','#step<?php echo $qcounter-1;?>');" class="text-left"><i class="fa fa-chevron-left"></i> <?php _e("BACK","csi");?></h4>
                <?php } ?>
                <h1><?php echo str_replace('\\','',$question['domanda']);?></h1>
                <div class="stars">
                    <?php for($i = 0; $i<$nrisposte;$i++){?>
                        <div class="star">
                            <i data-risposta1="<?php echo $question[0];?>" data-risposta2="<?php echo $question[1];?>" data-risposta3="<?php echo $question[2];?>" data-risposta4="<?php echo $question[3];?>" data-risposta5="<?php echo $question[4];?>" onclick="slidequiz('#step<?php echo $qcounter;?>','#step<?php echo $qcounter+1;?>',<?php echo $i+1;?>,this);" class="fa fa-star-o"></i>
                            <br>
                            <h3><?php echo $question[$i];?></h3>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div id="step<?php echo $qcounter+1;?>" class="main-container container-fluid steps">
    <div class="row">
        <div class="col-md-12 text-center" style="<?php echo $main_container_style;?>">
            <div class="smile">
                <i style="font-size:200px;color:<?php echo $text_color;?>;" class="fa fa-smile-o"></i>
            </div>
            <h1 style="font-size:60px;margin:20px auto;"><strong><?php _e("THANK YOU!","csi");?></strong></h1>
            <h3 style="text-transform: uppercase;font-size:34px;"><?php echo $survey->final_text;?></h3>
            <div id="restart" class="restart" style="<?php echo $restart_style;?>" href=""><?php echo $survey->restart_text;?></div>
        </div>
    </div>
</div>
<div class="footer-anonimo text-center">
    <h4><?php _e("This survey is completely anonymous","csi");?></h4>
</div>
</body>
<footer>
  <?php wp_footer();?>
</footer>
</html>
