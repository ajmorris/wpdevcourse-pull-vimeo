<?php
/*
Plugin Name: Pull in Vimeo Videos
Plugin URI: https://ajmorris.me
Description: Pulls in videos from my vimeo channel
Version: 0.1.0
Author: iThemes
Author URI: https://ithemes.com
Text Domain: ithemes-vimeo
*/

function vimeo_url() {

  $vimeo_feed_xml = get_transient( 'itms_get_vimeo_feed_xml' );
  // $vimeo_feed_xml = false;
  if ( $vimeo_feed_xml == false ) {
    $get_vimeo_feed = wp_remote_get( 'https://vimeo.com/api/v2/ithemestraining/videos.xml' );
    $vimeo_feed_xml = wp_remote_retrieve_body( $get_vimeo_feed );
    set_transient( 'itms_get_vimeo_feed_xml', $vimeo_feed_xml, 2 * DAY_IN_SECONDS );
  }


  return maybe_unserialize( $vimeo_feed_xml );

}

function display_videos() {

        // This function is setup to reorder the XML file based on the
        // number of plays rather than date order.
        function cmp($a, $b) {
            if ((int)$a->stats_number_of_plays[0] == (int)$b->stats_number_of_plays[0]) {
                return 0;
            }
            return ((int)$a->stats_number_of_plays[0] > (int)$b->stats_number_of_plays[0]) ? -1 : 1;
        }
        $xml = simplexml_load_string( vimeo_url() );
        $post_content = 5;
        $videos = array();
        foreach($xml->video as $v) {
            $videos[] = $v;
        }
        usort($videos, "cmp");
        $output = '<div id="vimeo_embeds" class="vimeo-embeds">';
        $output .= '<div class="video-links">';
        $output .= '<h3>Most Viewed Video Tutorials</h3>';
        $output .= '<ul class="icon videos">';
        for ( $i = 0; $i < absint( $post_content ); $i++ ) {
            $output .= '<li><a class="video" id="' . $videos[$i]->id . '" href="' . $videos[$i]->url . '">' . $videos[$i]->title . '</a></li>';
        }

        $output .= '</ul>';
        $output .= '</div>';
        $output .= '<div class="video-playable">';
        $output .= '<div id="video_player" class="flex-video vimeo">';
        $output .= '' . apply_filters( 'the_content', $videos[0]->url ) . '';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
}

add_shortcode( 'vimeo-videos', 'display_videos' );


function aj_enqueue_script() {
    wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'videos.js', array('jquery'), '1.0' );
}
add_action('wp_enqueue_scripts', 'aj_enqueue_script');
