function get_feedback_stars(rate){
  var i = 0;
  var stars = '';
  var difference = 0;
  var count = 0;
  jQuery("#csi_rating").val(rate);
  for(i=1;i<=rate;i++){
    stars += '<div onclick="get_feedback_stars('+i+');" class="star"><i class="fa fa-star"></i></div>';
    count++;
  }
  difference = 5 - rate;
  for(i=difference;i>0;i--){
    count = count+1;
    stars += '<div onclick="get_feedback_stars('+count+');" class="star"><i class="fa fa-star-o"></i></div>';
  }
  jQuery("#stars").html(stars);
};
