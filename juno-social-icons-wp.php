<?php
/**
 * Plugin Name: Juno Social Icons
 * Author: Carlos Suárez
 * Description: Plugin para añadir iconos sociales a Wordpress mediante shortcode [juno-social-icons]
 */

 // Hook que se inicializa cuando se activa un plugin. Aquí se crea la tabla utilizada por el plugin
 register_activation_hook( __FILE__, 'juno_init_db_social_data' );
 // Hook que se inicializa cuando se desactiva un plugin. Aquí elimina la tabla utilizada por el plugin
 register_deactivation_hook( __FILE__, 'juno_drop_table' );
 // Define el shortcode que pinta el plugin
 add_shortcode( 'juno-social-icons', 'juno_show_social_icons' );
 // Se inserta la librería de Font Awesome para los iconos
// add_action( 'wp_enqueue_scripts', 'juno_load_css' );
 // Añade una página al menú de administración de wordpress. La acción admin_menu se ejecuta después de colocar el menú básico del administrador. 
 // Por tanto, al añadir nuestra función juno_social_icons_menu en éste punto de ejecución, podemos insertar nuestra página de configuración para el plugin.
 add_action( 'admin_menu', 'juno_social_icons_menu' ); 

 add_action('init', 'juno_register_script');

 function juno_social_icons_menu() {

    // La función add_menu_page  añade una nueva entrada en el menú. Para ello, espera como parámetros el título de la página, 
    // título del menú, rol que tiene acceso, identificador único de la página, 
    // función encargada de pintar el código del formulario (descrito en el siguiente apartado) y el icono en el panel.
    add_menu_page( 
        'Configuración - Juno Social Icons', // Título de la página
        'Juno Social Icons', // Menú del título
        'administrator', // Rol con permisos de acceso 
        'juno_social_icons_settings', // slug de la página de opciones (Id)
        'juno_social_icons_settings_paje', // Función que pinta la página de configuración del plugin
        'dashicons-twitter' // Icono del menú
    );
 }

 // Html del plugin
 function juno_show_social_icons() {

    // Obtenemos los registros de la tabla
    $data = get_data_social_icons();

     ob_start();
     ?>
     <div class="wrap">
        <?php
        // Por cada registro se crea un campo URL a rellenar
        foreach($data as $el) {
            if ($el->social_url != null 
                && $el->social_url != "") {
            ?>
                <a href="<?php echo $el->social_url ?>" target="_blank" class="juno-a">
                    <i class="juno-i fa <?php echo $el->fa_icon_class ?>" title="<?php echo $el->social_title ?>"></i>
                </a>
            <?php
            }
        }
        ?>
    </div>
    <?php
     return ob_get_clean();
 }


  // Html de configuración del plugin
  function juno_social_icons_settings_paje() {

    // Si va el submit en el POST, se están guardando los datos
    if (!empty($_POST) && $_POST['submit'] != "") {
        set_data_social_icons($_POST);
    }

    // Obtenemos los registros de la tabla
    $data = get_data_social_icons();
    

    ?>
        <div class="wrap">
            <h2>Setting - Juno Social Icons</h2>
            <div class="wrap" style="margin:20px;">
                <form action="<?php get_the_permalink() ?>" method="POST"> <!-- El action lo hacemos a la misma página, así que recibimos el POST en esta misma función y guardamos los datos en caso de que vengan -->
                    <table>
                        <?php
                            // Por cada registro se crea un campo URL a rellenar
                            foreach($data as $el){
                                ?>
                                    <tr>
                                        <td><label><b><?php echo $el->social_title ?></b></label></td>
                                        <td><input type="url" name="<?php echo $el->social_name ?>" value="<?php echo $el->social_url ?>" placeholder="URL" style="width:350px"></td>
                                    </tr>
                                <?php
                            }
                        ?>
                    </table>
                    <input type="hidden" name="submit" value="">
                    <?php submit_button(); ?>
                </form>

            </div>
        </div>
    <?php
}

 function get_data_social_icons() {
    // Necesario para usar la función dbDelta() y ejecutar las querys
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb; // Clase conectora de bbdd wordpress. Contiene un conjunto de funciones utilizadas para interactuar con una base de datos
    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla

    return $wpdb->get_results("SELECT * FROM $tableName", $output = OBJECT);
 }

 function set_data_social_icons($post) {
    
    // Necesario para usar la función dbDelta() y ejecutar las querys
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb; // Clase conectora de bbdd wordpress. Contiene un conjunto de funciones utilizadas para interactuar con una base de datos
    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla
    $upd = "";

    foreach($post as $field => $value){
        if ($field != "submit") {
            $val = esc_url_raw($value); // Se sanea la url
            $wpdb->update( $tableName, array("social_url"=>"$val"), array( "social_name" => "$field") );
        }
     }
 }

 // Carga el css de Font Awesome
 function juno_register_script() {
    wp_register_style( 'load-fa-awesome', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
    wp_enqueue_style( 'load-fa-awesome' );
    wp_register_style( 'juno-social-icons', plugins_url('public/css/juno-social-icons.css', __FILE__));
    wp_enqueue_style( 'juno-social-icons' );
 }


 // Crea la tabla y carga los datos. Se ejecuta al activar el plugin
 function juno_init_db_social_data() {
    juno_drop_table();
    juno_create_table();
    juno_insert_rows();
 }

 // Crea la tabla utilizada por el plugin
 function juno_create_table() {
    
    // Necesario para usar la función dbDelta() y ejecutar las querys
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb; // Clase conectora de bbdd wordpress. Contiene un conjunto de funciones utilizadas para interactuar con una base de datos
    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla
    $charset_collate = $wpdb->get_charset_collate(); // Charset de la bbdd

    // SQL de creación de tabla
    $query = "CREATE TABLE 
            $tableName (
                id TINYINT(2) NOT NULL AUTO_INCREMENT,
                social_name VARCHAR(25) NOT NULL,
                social_title VARCHAR(25) NOT NULL, 
                fa_icon_class VARCHAR(25) NOT NULL,
                social_url VARCHAR(500),
                UNIQUE(id)
            ) $charset_collate";

    // Se ejecuta la query
    dbDelta($query);

 }

 // Elimina la tabla si existe. Se realiza de esta manera porque dbDelta() no soporta drop ni deletes
 function juno_drop_table() {
    global $wpdb;
    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla
    $sql = "DROP TABLE IF EXISTS $tableName;";
    $wpdb->query($sql);
}

// Inserta los registros con los datos de las redes sociales a mostrar
function juno_insert_rows() {

    // Necesario para usar la función dbDelta() y ejecutar las querys
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    global $wpdb; // Clase conectora de bbdd wordpress. Contiene un conjunto de funciones utilizadas para interactuar con una base de datos
    $tableName = $wpdb->prefix . 'juno_social_elements'; // Nombre de la tabla

    $query = "INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('facebook', 'Facebook', 'fa-facebook',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('twitter', 'Twitter', 'fa-twitter',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('instagram', 'Instagram', 'fa-instagram',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('youtube', 'Youtube', 'fa-youtube',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('spotify', 'Spotify', 'fa-spotify',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('soundcloud', 'Soundcloud', 'fa-soundcloud',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('apple-music', 'Apple Music', 'fa-music',null);
    INSERT INTO $tableName (social_name, social_title, fa_icon_class, social_url) VALUES ('amazon', 'Amazon', 'fa-amazon',null);";

    // Se ejecuta la query
    dbDelta($query);
}
