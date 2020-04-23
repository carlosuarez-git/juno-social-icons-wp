<?php
/**
 * Plugin Name: Juno Social Icons
 * Author: Carlos Suárez
 * Description: Plugin para añadir iconos sociales a Wordpress mediante shortcode [juno-social-icons]
 */

 // Hook que se inicializa cuando se activa un plugin. Aquí se crea la tabla utilizada por el plugin
 register_activation_hook( __FILE__, 'createTabla' );
 // Define el shortcode que pinta el plugin
 add_shortcode( 'juno-social-icons', 'showJunoSocialIcons' );
 // Se inserta la librería de Font Awesome para los iconos
 add_action( 'wp_enqueue_scripts', 'enqueue_load_fa' );

 // Html del plugin
 function showJunoSocialIcons() {
     ob_start();
     ?>
     <i class="fab fa-wordpress"></i>
     <?php
     return ob_get_clean();
 }

 // Carga el css de Font Awesome
 function enqueue_load_fa() {
     wp_enqueue_style( 'load-fa', 'https://use.fontawesome.com/releases/v5.3.1/css/all.css' );
 }

 // Crea la tabla utilizada por el plugin
 function createTabla() {
    
    // Necesario para usar la función dbDelta() y ejecutar las querys
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // Clase conectora de bbdd wordpress. Contiene un conjunto de funciones utilizadas para interactuar con una base de datos
    global $wpdb;

    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla
    $charset_collate = $wpdb->get_charset_collate(); // Charset de la bbdd

    // Borra la tabla del plugin
    remove_table();

    // SQL de creación de tabla
    $query = "CREATE TABLE 
            $tableName (
                id TINYINT(2) NOT NULL AUTO_INCREMENT,
                social_name VARCHAR(25) NOT NULL,
                social_title VARCHAR(25) NOT NULL, 
                fa_icon_class VARCHAR(25) NOT NULL,
                social_url VARCHAR(500), 
                icon_order TINYINT(2),
                UNIQUE(id)
            ) $charset_collate";

    // Se ejecuta la query
    dbDelta($query);

    $query = "INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('instagram', 'Instagram', 'fa-instagram',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('facebook', 'Facebook', 'fa-facebook',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('twitter', 'Twitter', 'fa-twitter',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('spotify', 'Spotify', 'fa-spotify',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('soundcloud', 'Soundcloud', 'fa-soundcloud',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('apple-music', 'Apple Music', 'fa-music',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('youtube', 'Youtube', 'fa-youtube',null,null);
                INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url, icon_order) VALUES ('instagram', 'Instagram', 'fa-instagram',null,null);";

    // Se ejecuta la query
    dbDelta($query);
 }

 // Elimina la tabla si existe. Se realiza de esta manera porque dbDelta() no soporta drop ni deletes
 function remove_table() {
    global $wpdb;
    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla
    $sql = "DROP TABLE IF EXISTS $tableName;";
    $wpdb->query($sql);
}