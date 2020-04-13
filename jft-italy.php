<?php
/*
Plugin Name: JFT Italy
Plugin URI: https://wordpress.org/plugins/fetch-jft/
Author: Patrick J NERNA
Description: This is a plugin that fetches the Just For Today from NAWS and puts it on your site Simply add [jft] shortcode to your page. Fetch JFT Widget can be added to your sidebar or footer as well.
Version: 1.0.0
Install: Drop this directory into the "wp-content/plugins/" directory and activate it.
*/
/* Disallow direct access to the plugin file */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Sorry, but you cannot access this page directly.');
}

function jft_italy_func( $atts ){

    date_default_timezone_set ( 'Europe/Rome' );
    $today = date("Y-m-d");
    $jft_post = get_posts(array(
        'numberposts'	=> -1,
        'post_type'		=> 'post',
        'meta_key'		=> 'solo_per_oggi_date',
        'meta_value'	=> $today
    ));
    $todays_jft =  get_post_field('post_content',  $jft_post[0]->ID);
    return $todays_jft;
}

function jft_italy_widget_func( $atts ){

    date_default_timezone_set ( 'Europe/Rome' );
    $today = date("Y-m-d");
    $jft_post = get_posts(array(
        'numberposts'	=> -1,
        'post_type'		=> 'post',
        'meta_key'		=> 'solo_per_oggi_date',
        'meta_value'	=> $today
    ));
    $todays_jft_array = [];
    $todays_jft_array['excerpt'] = get_post_field('post_excerpt',  $jft_post[0]->ID);
    $todays_jft_array['url'] = get_post_field('guid',  $jft_post[0]->ID);
    $todays_jft_array['title'] = get_post_field('post_title',  $jft_post[0]->ID);

    return $todays_jft_array;
}

// create [jft] shortcode
add_shortcode( 'jft_italy', 'jft_italy_func' );

/** START Fetch JFT Widget **/
// register jft_italy_Widget
add_action( 'widgets_init', function(){
    register_widget( 'jft_italy_Widget' );
});

class jft_italy_Widget extends WP_Widget {
    /**
     * Sets up a new Fetch JFT widget instance.
     *
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'jft_italy_widget',
            'description' => 'Displays the Italian Just For Today',
        );
        parent::__construct( 'jft_italy_widget', 'JFT Italy', $widget_ops );
    }

    /**
     * Outputs the content for the current Fetch JFT widget instance.
     *
     *
     * @jft_italy_func gets and parses the jft
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Area Meetings Dropdown widget instance.
     */

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $get_jft = jft_italy_widget_func( $atts );
        echo '<a href="' . $get_jft['url'] . '">' . $get_jft['title'] . '</a><br/><br/>';
        echo $get_jft['excerpt'];
        echo '&nbsp;&nbsp;<a href="' . $get_jft['url'] . '">Leggi di più →</a><br/><br/>';
        echo $args['after_widget'];
    }
    /**
     * Outputs the settings form for the Fetch JFT widget.
     *
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Title', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_attr_e( 'Title:', 'text_domain' ); ?>
            </label>

            <input
                    class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                    type="text"
                    value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * Handles updating settings for the current Fetch JFT widget instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}
/** END Fetch JFT Widget **/

?>
