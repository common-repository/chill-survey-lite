function slidequiz(from,to,vote = 0,element = false){
    var i = 0;
    var j = 0;
    var dataToPass = {};
    var counter = 0;
    var stars = '';
    var difference = 0;
    var nrisposte = jQuery(from).data("nrisposte");
    var stepsNumber = jQuery("#home").attr('data-steps');
    var homeUrl = jQuery("#home").attr('data-homeurl');

    jQuery(from).animate({left:"-2840px"},1000);

    setTimeout(function(){
        jQuery(from).addClass("relative").removeClass("absolute");
    },1000);

    jQuery(to).animate({left: "0px"},500).removeClass("relative").addClass("absolute");

    if(vote != 0){
        jQuery(from).val(vote);
        for(i=1;i<=vote;i++){
            stars += '<div class="star"><i data-risposta1="'+jQuery(element).data('risposta1')+'" data-risposta2="'+jQuery(element).data('risposta2')+'" data-risposta3="'+jQuery(element).data('risposta3')+'" data-risposta4="'+jQuery(element).data('risposta4')+'" data-risposta5="'+jQuery(element).data('risposta5')+'" onclick="slidequiz(\''+from+'\',\''+to+'\','+i+',this);" class="fa fa-star"></i><br><h3>'+jQuery(element).data('risposta'+i)+'</h3></div>';
        }
        difference = nrisposte-vote;
        if(difference > 0 ){
            for(i=vote+1;i<=nrisposte;i++){
                stars += '<div class="star"><i data-risposta1="'+jQuery(element).data('risposta1')+'" data-risposta2="'+jQuery(element).data('risposta2')+'" data-risposta3="'+jQuery(element).data('risposta3')+'" data-risposta4="'+jQuery(element).data('risposta4')+'" data-risposta5="'+jQuery(element).data('risposta5')+'" onclick="slidequiz(\''+from+'\',\''+to+'\','+i+',this);" class="fa fa-star-o"></i><br><h3>'+jQuery(element).data('risposta'+i)+'</h3></div>';
            }
        }
        jQuery(from+' .stars').html(stars)
    }

    for(j=0;j<stepsNumber;j++){
      counter++;
      dataToPass['step'+counter] = jQuery("#step"+counter).val();
    }
    dataToPass['action'] = "save_csi";
    console.log(dataToPass);

    if(from == '#step' + stepsNumber){
        jQuery.ajax({
            type: 'POST',
            url: homeUrl+"/wp-admin/admin-ajax.php",
            data: dataToPass,
            error: function(jqXHR, textStatus, errorThrown){
                console.error("The following error occured: " + textStatus);
            },
            success: function(data) {
                console.log('salvato');
                setTimeout(function(){
                  location.href=location.href;
                },10000);
            }
        })
    }
}

function slideback(from,to){
    jQuery(from).animate({left:"2840px"},1000);
    setTimeout(function(){
        jQuery(from).addClass("relative").removeClass("absolute");
    },1000);

    jQuery(to).animate({left: "0px"},500).removeClass("relative").addClass("absolute");
}
