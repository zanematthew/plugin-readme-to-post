<?php

/**
 * Return the contents between two strings
 *
 * @author Zane Matthew (borrowed from http://www.php.net/manual/en/function.strpos.php#74286)
 * @param $text The text we are searching from
 * @param $s1 The "start" string
 * @param $s2 The "end string"
 * @since 1.1
 */
function prtp_get_string_between( $text, $s1, $s2 ){
    $mid_url = "";
    $pos_s = strpos( $text, $s1 );
    $pos_e = strpos( $text, $s2 );
    for ( $i = $pos_s + strlen( $s1 ) ; ( ( $i < ($pos_e)) && $i < strlen($text) ) ; $i++ ) {
        $mid_url .= $text[$i];
    }
    return $mid_url;
}


function prtp_get_images( $slug=null ){

    echo plugins_url() . $slug . '_assets/screenshot-1.png';

}