<?php

/**
 * Run the following code once the theme is set-up, php includes and
 * additional code can go here.
 *
 * @since 0.1-alpha
 * @author Zane Matthew
 */
function prtp_theme_setup(){

    /**
     * Load the markdown library if it isn't already loaded.
     */
    if ( ! function_exists( 'Markdown' ) ) require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/markdown.php';
}
add_action('after_setup_theme', 'prtp_theme_setup');


/**
 * Enqueue scripts and stylesheets
 *
 * @since 0.1-alpha
 * @author Zane Matthew
 */
function prtp_enqueue_scripts(){

    $options = get_option('prtp_settings');

    if ( ! empty( $options['tabs'] ) && $options['tabs'] == 1 )
        wp_enqueue_script( 'jquery-ui-tabs' );

    wp_enqueue_script( 'plugin-readme-to-post-script' );
    wp_enqueue_style( 'plugin-readme-to-post-style' );
}
add_action('wp_enqueue_scripts','prtp_enqueue_scripts');


/**
 * The shortcode that returns the parsed readme and/or creates tabs.
 *
 * If the Post Title is the same as the plugin and the plugin lives on this
 * install it is derived automatticaly if not it uses the url passed in.
 *
 * @since 0.1-alpha
 * @author Zane Matthew
 * @param $url The full URL to the readme file to parse
 */
function plugin_readme_to_post( $atts ) {

    global $post;

    $native_readme = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . sanitize_title( get_the_title( $post->ID ) ) . '/readme.txt';
    $readme_string = ( file_exists( $native_readme ) ) ? file_get_contents( $native_readme ) : null;

    extract( shortcode_atts( array(
        'readme' => $readme_string,
        'url' => null
        ), $atts )
    );

    if ( empty( $readme_string ) ) return;

    if ( ! empty( $url ) ){
        $tmp_readme = wp_remote_get( $url );
        $readme_string = $tmp_readme['body'];
    }

    $markdown = Markdown( $readme_string );
    $options = get_option('prtp_settings');

    if ( ! empty( $options['tabs'] ) && $options['tabs'] == 1 ){
        $tmp = explode( '<h2>Changelog</h2>', $markdown );
        $changelog = $tmp[1];

        $readme_sections = array(
            array(
                'name' => 'Description',
                'content' => prtp_get_string_between( $markdown, '<h2>Description</h2>', '<h2>Installation</h2>' )
            ),
            array(
                'name' => 'Installation',
                'content' => prtp_get_string_between( $markdown, '<h2>Installation</h2>', '<h2>Frequently Asked Questions</h2>' )
            ),
            array(
                'name' => 'FAQ',
                'content' => prtp_get_string_between( $markdown, '<h2>Frequently Asked Questions</h2>', '<h2>Screenshots</h2>' )
                ),
            array(
                'name' => 'Changelog',
                'content' => $changelog
                )
            );

        $tabs = null;
        $content = null;
        $i = 0;

        foreach( $readme_sections as $section ){
            $clean_name = str_replace( ' ', '-', strtolower( $section['name'] ) );
            $tabs .= '<li><a href="#' . $clean_name . '" data-toggle="tab">' . $section['name'] . '</a></li>';
            $content .= '<div id="' . $clean_name . '" class="tab-pane">' . $section['content'] . '</div>';
        }

        $html = '<div id="prtp-tabs"><ul>' . $tabs . '</ul><div>'.$content.'</div></div>';
    } else {
        $html = $markdown;
    }

    return '<div class="prtp-container">'.$html.'</div>';
}
add_shortcode('parse_readme', 'plugin_readme_to_post');