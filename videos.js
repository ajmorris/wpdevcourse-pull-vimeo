jQuery(document).ready(function($){
  jQuery(".video").click(function (e){
    e.preventDefault();
    var id = this.id;
    jQuery("#video_player").html('<iframe src="//player.vimeo.com/video/' + id + '" \
            width="825" height="464" \
            frameborder="0" \
            webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');

  });

});
