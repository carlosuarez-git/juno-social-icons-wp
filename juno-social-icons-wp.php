<?php
/**
 * Plugin Name: Juno Social Icons
 * Author: Carlos Suárez
 * Description: Plugin para añadir iconos sociales a Wordpress mediante shortcode [juno-social-icons]
 */

// Define el shortcode que pinta el plugin
add_shortcode( 'juno-social-icons', 'showJunoSocialIcons' );
// Se inserta la librería de Font Awesome para los iconos
add_action( 'wp_enqueue_scripts', 'enqueue_load_fa' );

function showJunoSocialIcons() {
    ob_start();
    ?>
    <i class="fab fa-wordpress"></i>
    <?php
    return ob_get_clean();
}

function enqueue_load_fa() {
    wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.3.1/css/all.css' );
}