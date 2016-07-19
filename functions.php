<?php


add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}


function tasLoadFonts() {
    $output = "<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>";
    $output .= "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css'>";
    echo $output;
}
add_action('wp_head','tasLoadFonts');



/**
 * 5 Featured service widget to show business  lines on homepage.
 */
add_action( 'widgets_init', 'tas_widgets_init');
function tas_widgets_init()
{
    register_widget("tas_spacious_service_widget");
}
class tas_spacious_service_widget extends WP_Widget {
    function __construct() {
        $widget_ops = array( 'classname' => 'tas_widget_service_block', 'description' => __( 'TAS Display 5 pages on a single line. Best for Business Top or Bottom sidebar.', 'spacious' ) );
        $control_ops = array( 'width' => 200, 'height' =>250 );
        parent::__construct( false, $name = __( 'TAS: TG: Services', 'spacious' ), $widget_ops, $control_ops);
    }

   function form( $instance ) {
        for ( $i=0; $i<6; $i++ ) {
            $var = 'page_id'.$i;
            $defaults[$var] = '';
        }
        $instance = wp_parse_args( (array) $instance, $defaults );
        for ( $i=0; $i<6; $i++ ) {
            $var = 'page_id'.$i;
            $var = absint( $instance[ $var ] );
        }
        ?>
        <?php for( $i=0; $i<6; $i++) { ?>
            <p>
                <label for="<?php echo $this->get_field_id( key($defaults) ); ?>"><?php _e( 'Page', 'spacious' ); ?>:</label>
                <?php wp_dropdown_pages( array( 'show_option_none' =>' ','name' => $this->get_field_name( key($defaults) ), 'selected' => $instance[key($defaults)] ) ); ?>
            </p>
            <?php
            next( $defaults );// forwards the key of $defaults array
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        for( $i=0; $i<6; $i++ ) {
            $var = 'page_id'.$i;
            $instance[ $var] = absint( $new_instance[ $var ] );
        }

        return $instance;
    }

    function widget( $args, $instance ) {
        extract( $args );
        extract( $instance );

        global $post;
        $page_array = array();
        for( $i=0; $i<6; $i++ ) {
            $var = 'page_id'.$i;
            $page_id = isset( $instance[ $var ] ) ? $instance[ $var ] : '';

            if( !empty( $page_id ) )
                array_push( $page_array, $page_id );// Push the page id in the array
        }
        $get_featured_pages = new WP_Query( array(
            'posts_per_page' 			=> -1,
            'post_type'					=>  array( 'page' ),
            'post__in'		 			=> $page_array,
            'orderby' 		 			=> 'post__in'
        ) );
        echo $before_widget; ?>
        <?php
        $j = 1;
        while( $get_featured_pages->have_posts() ):$get_featured_pages->the_post();
            $page_title = get_the_title();
            $service_class = "tg-one-fifth six-services";
            ?>
            <div class="<?php echo $service_class; ?>"><a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
                <?php
                if ( has_post_thumbnail() ) {
                    echo'<div class="service-image">'.get_the_post_thumbnail( $post->ID, array(125,125) ).'</div>';
                }
                ?>
                <?php echo $before_title; ?><?php echo $page_title; ?></a><?php echo $after_title; ?>
                <p><?php echo get_post_meta( get_the_ID() , 'tas_subtitle', true ); ?></p>
                </a>
            </div>
            <?php $j++; ?>
        <?php endwhile;
        // Reset Post Data
        wp_reset_query();
        ?>
        <?php
        echo $after_widget;
    }
}

/*
 * Items to support the Business Lines pages
 */

add_action( 'widgets_init', 'tas_register_sidebars' );
function tas_register_sidebars() {
    /* Register the 'business_lines' sidebar. */
    register_sidebar(
        array(
            'id'            => 'tas_business_lines_sidebar',
            'name'          => __( 'Business Lines Sidebar' ),
            'description'   => __( 'Sidebar for pages with the Business Lines Pages.' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );
}

/*
 * Custom Metabox: subtitle and color of second border
 * TODO: Would really only like to show this for the Business Lines pages.
 */
/* Adds a meta box to the post edit screen */
add_action( 'add_meta_boxes', 'tas_add_page_custom_box' );
function tas_add_page_custom_box() {
    $screens = array( 'page' );
    foreach ( $screens as $screen ) {
        add_meta_box(
            'tas_subtitle_menu',            // Unique ID
            'TAS Settings',      // Box title
            'tas_subtitle_render',  // Content callback
            $screen                      // post type
        );
    }
}
function tas_subtitle_render( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'tas_nonce' );
    $subtitle_value = get_post_meta( $post->ID, 'tas_subtitle', true );
    $tas_border_hex_value = get_post_meta( $post->ID, 'tas_border_hex', true );
    ?>
    <p><strong><label for="tas_subtitle"> Subtitle </label></strong></p>
    <input type="text" name="tas_subtitle" id="tas_subtitle" value="<?php echo $subtitle_value ?>">
    <p><strong><label for="tas_border_hex"> Hex Code For Border </label></strong></p>
    <p>Must start with a #.</p>
    <input type="text" name="tas_border_hex" id="tas_border_hex" value="<?php echo $tas_border_hex_value ?>">
    <?php
}
add_action( 'save_post', 'tas_save_postdata' );
function tas_save_postdata( $post_id ) {
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'tas_nonce' ] ) && wp_verify_nonce( $_POST[ 'tas_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    if ( array_key_exists('tas_subtitle', $_POST ) ) {
        update_post_meta( $post_id,
            'tas_subtitle',
            sanitize_text_field($_POST['tas_subtitle'])
        );
    }
    if ( array_key_exists('tas_border_hex', $_POST ) ) {
        update_post_meta( $post_id,
            'tas_border_hex',
            tas_sanitize_hex_color($_POST['tas_border_hex'])
        );
    }
}
function tas_sanitize_hex_color($color) {
        if ( '' === $color )
            return '';
    //to-do: default to color in theme.

        // 3 or 6 hex digits, or the empty string.
        if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
            return $color;
    }



?>