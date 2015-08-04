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


    extract( shortcode_atts( array(
        'url' => null,
        'slug' => sanitize_title( get_the_title( $post->ID ) )
        ), $atts )
    );

    if ( empty( $atts['url'] ) ){

        $native_readme = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . $slug . '/readme.txt';
        $readme_string = ( file_exists( $native_readme ) ) ? file_get_contents( $native_readme ) : null;

    } else {

        $tmp_readme = wp_remote_get( $atts['url'] );
        $readme_string = $tmp_readme['body'];
    }

    if ( empty( $readme_string ) ) {
        $html = "Unable to find readme for {$slug}.";
    } else {

        $markdown = Markdown( $readme_string );
        $options = get_option('prtp_settings');

        $image_captions = explode( PHP_EOL, trim( prtp_get_string_between( $readme_string, '== Screenshots ==', '== Changelog ==' ) ) );

        $images_with_captions = array();
        $tmp = array();
        $i = 1;

        foreach( $image_captions as $caption ){
            $tmp = array(
                'id' => $slug . '_screenshot-' . $i . '.png',
                'url' => plugin_dir_url( ( dirname( dirname( __FILE__ ) ) ) ) . $slug . '/_assets/screenshot-' . $i . '.png',
                'width' => '',
                'height' => '',
                'caption' => $caption,
                'alt' => esc_attr( $caption )
                );
            $images_with_captions[] = $tmp;
            $i++;
        }

        // Build HTML
        $images_html = null;
        foreach( $images_with_captions as $image ) {
            $images_html .= '<figure id="' . $image['id'] . '" style="width: ' . $image['width'] . ';" class="wp-caption alignleft">';
            $images_html .= '<figcaption class="wp-caption-text">' . $image['caption'] . '</figcaption>';
            $images_html .= '<a href="' . $image['url'] . '">';
            $images_html .= '<img class="size-full ' . $image['id'] . '" src="' . $image['url'] . '" alt="' . $image['alt'] . '" width="' . $image['width'] . '" height="' . $image['height'] . '"></a>';

            $images_html .= '</figure>';
        }


        if ( ! empty( $options['tabs'] ) && $options['tabs'] == 1 ){
            $tmp = explode( '<h2>Changelog</h2>', $markdown );
            $changelog = $tmp[1];

            $readme_sections = array(
                array(
                    'name' => 'Images',
                    'content' => $images_html
                    ),
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

            $image_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . sanitize_title( get_the_title( $post->ID ) );
            $screenshots = glob( plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . sanitize_title( get_the_title( $post->ID ) ) . "/screenshot-*.png", GLOB_BRACE );

            if ( ! empty( $screenshots ) ){
                $readme_sections[] = array(
                    'name' => 'Screenshots',
                    'content' => prtp_get_string_between( $markdown, '<h2>Screenshots</h2>', '<h2>Changelog</h2>' )
                );
            }

            $tabs = null;
            $content = null;
            $i = 0;

            foreach( $readme_sections as $section ){
                $clean_name = str_replace( ' ', '-', strtolower( $section['name'] ) );
                $tabs .= '<li><a href="#' . $clean_name . '" data-toggle="tab">' . $section['name'] . '</a></li>';
                $content .= '<div id="' . $clean_name . '" class="tab-pane">' . $section['content'];
                if ( $section['name'] == 'Screenshots' ){
                    while ( $i <= count( $screenshots ) - 1 ) {
                        $i++;
                        $content .= '<img src="' . $image_url . '/screenshot-' . $i . '.png" />';
                    }
                }
                $content .= '</div>';
            }

            $html = '<div class="prtp-tabs"><ul>' . $tabs . '</ul><div>'.$content.'</div></div>';
        } else {
            $html = $markdown;
        }
    }

    return '<div class="prtp-container">'.$html.'</div>';
}
add_shortcode('parse_readme', 'plugin_readme_to_post');
