<?php
/*
 * Plugin Name: Wordpress StoryMap
 * Plugin URI: https://en-gb.wordpress.org/plugins/wp-storymap/
 * Description: Create your own stroymaps on wordpress
 * Author: Picbook
 * Version: 2.1
 * Author URI: https://picbook.es/
 */

//PLUGIN BACKEND
/********************************
 * Default configuration options
 ********************************/

register_activation_hook( __FILE__, 'storymap_pro_set_default_options_array' );

function storymap_pro_set_default_options_array() {
    storymap_pro_get_options();
}

function storymap_pro_get_options() {
    $options = get_option( 'storymap_pro_options', array() );

    $new_options['title'] = 'Your Strymap Title';
    $new_options['basemap'] = 'OpenStreetMap';
    $new_options['select_list_numbe_points'] = '1';
    $new_options['ga_account_name'] = 'Your Title';
    $new_options['track_outgoing_links'] = false;
    $new_options['height'] = 500;
    $new_options['width'] = 500;

    $merged_options = wp_parse_args( $options, $new_options );

    $compare_options = array_diff_key( $new_options, $options );
    if ( empty( $options ) || !empty( $compare_options ) ) {
        update_option( 'storymap_pro_options', $merged_options );
    }
    return $merged_options;
}



/**************************
 * Configuration page
 **************************/

//add_action( 'admin_init', 'storymap_admin_init' );
function storymap_pro_admin_init() {
  $options = storymap_pro_get_options();

  register_setting( 'storymap_pro_settings',
		'storymap_pro_options','storymap_pro_validate_options' );

	// Add a new settings section within the group
	add_settings_section( 'storymap_pro_main_section',
		'Main Settings', 'storymap_pro_setting_section_callback',
		'storymap_pro_settings_section' );


  add_settings_field( 'storymap_pro_title', 'Storymap user name',
  	'storymap_pro_display_storymap_title', 'storymap_pro_settings_section',
  	'storymap_pro_main_section', array( 'name' => 'title' ) );

  add_settings_field( 'select_list_basemap', 'Select Base Map', 'storymap_pro_select_list',
  	'storymap_pro_settings_section', 'storymap_pro_main_section',
    array( 'name' => 'basemap',
  	'choices' => array( 'Satellite', 'Streets', 'Hybrid' ) ) );

}


function storymap_pro_validate_options( $input ) {
    // Cycle through all text form fields and store their values
    // in the options array
    foreach ( array( 'select_list_basemap' ) as $option_name ) {
        if ( isset( $input[$option_name] ) ) {
            $input[$option_name] =
                sanitize_text_field( $input[$option_name] );
        }
    }

	return $input;
}

// Function to display text at the beginning of the main section
function storymap_pro_setting_section_callback() { ?>
	<p>This is the main configuration section for wordpress storymap.</p>
<?php }


// Function to render storymap title
function storymap_pro_display_storymap_title( $data = array()) {
	extract( $data );
	$options = storymap_pro_get_options();
	?>
	<input type="text" maxlength="20" name="storymap_pro_options[<?php echo $name; ?>]" value="<?php echo esc_html( $options[$name] ); ?>"/><br />

<?php }

// Function to render a text input field
function storymap_pro_display_text_field( $data = array()) {
	extract( $data );
	$options = storymap_pro_get_options();
	?>
	<input type="text" name="storymap_pro_options[<?php echo $name; ?>]" value="<?php echo esc_html( $options[$name] ); ?>"/><br />

<?php }


//Function to render text area
function storymap_pro_display_text_area( $data = array() ) {
	extract ( $data );
	$options = storymap_pro_get_options();
	?>
	<textarea type='text' name='storymap_pro_options[<?php echo $name; ?>]' rows='5' cols='30'><?php echo esc_html( $options[$name] ) ; ?></textarea>

  </script>
<?php }

// Function to display a list
function storymap_pro_select_list( $data = array() ) {
	extract ( $data );
	$options = storymap_pro_get_options();
	?>
	<select name='storymap_pro_options[<?php echo $name; ?>]'>
		<?php foreach( $choices as $item ) { ?>
			<option value="<?php echo $item; ?>" <?php selected( $options[$name] == $item ); ?>><?php echo $item; ?></option>;

		<?php } ?>
	</select>

<?php }

//function to upload media
function storymap_pro_media_lib_uploader() {
    //Core media script
    wp_enqueue_media();

    // Your custom js file
    wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'media-lib-uploader-js' );

    // load script only on story edit points page
    $current_screen = get_current_screen();
    if( $current_screen->id === "storymap_page_storymap-points-list" ) {
      wp_enqueue_style('storymap_pro_fontawesomepickerall', "https://use.fontawesome.com/releases/v5.5.0/css/all.css");
    }

    if( $current_screen->id === "storymap_page_storymap-pro-edit-points" ) {
      //script for map picker
      wp_enqueue_style('storymap_pro_leafletcss', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.css');
      wp_enqueue_style('storymap_pro_generalstyle', plugin_dir_url( __FILE__ ) . 'style.css');
      wp_enqueue_script('storymap_pro_leafletjs', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.js');
      wp_enqueue_style('storymap_pro_geosearchstyle', plugin_dir_url( __FILE__ ) . 'leaflet/leafletgeosearch.css');
      wp_enqueue_script('storymap_pro_searchplaces', plugin_dir_url( __FILE__ ) . 'leaflet/bundle.min.js');

      wp_register_script( 'map-coordinate-picker-js', plugins_url( 'leaflet/wp-storymap-coordinate-picker.js' , __FILE__ ), array('jquery') ,'',true);
      wp_enqueue_script('map-coordinate-picker-js');


      //color picker for markers
      wp_enqueue_style('storymap_pro_colorpalettecss', plugin_dir_url( __FILE__ ) . 'bootstrap/bootstrap-colorpalette.css');
      wp_enqueue_script('storymap_pro_colorpalettejs', plugin_dir_url( __FILE__ ) . 'bootstrap/bootstrap-colorpalette.js');

      //image picker shapes
      wp_enqueue_style('storymap_pro_shapepickercss', plugin_dir_url( __FILE__ ) . 'bootstrap/image-picker.css');
      wp_enqueue_script('storymap_pro_shapepickerjs', plugin_dir_url( __FILE__ ) . 'bootstrap/image-picker.js');

      //font awesome picker
      wp_enqueue_style('storymap_pro_fontawesomepickerall', "https://use.fontawesome.com/releases/v5.5.0/css/all.css");
      wp_enqueue_style('storymap_pro_fontawesomepickercss', plugin_dir_url( __FILE__ ) . 'font-awesome-picker/css/fontawesome-iconpicker.css');
      wp_enqueue_script('storymap_pro_fontawesomepickerjs', plugin_dir_url( __FILE__ ) . 'font-awesome-picker/js/fontawesome-iconpicker.js');


    }
}
add_action('admin_enqueue_scripts', 'storymap_pro_media_lib_uploader');




// Function with points options
function wp_sotrymap_pro_points_form($data = array()){
  extract ( $data );
  $options = storymap_pro_get_options();
  ?>
  <select name='storymap_pro_options[<?php echo $name; ?>]'>
    <?php foreach( $choices as $item ) { ?>
      <option value="<?php echo $item; ?>" <?php selected( $options[$name] == $item ); ?>><?php echo $item; ?></option>;
    <?php } ?>
  </select>

  <input id="image-url" type="text" name="image" />
  <input id="upload-button" type="button" class="button" value="Upload Image" />
  <?php
}

/**********************TABLE*****************/
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



/*********************END TABLE**************/



/*********************************
Save configuration settings
*********************************/
function storymap_pro_verify_admin_init(){
  add_action('admin_post_save', 'process_wp_storymap_pro_options');
}

function process_wp_storymap_pro_options(){

  //check that user has proper security level
  if(!current_user_can('manage_options')){
    wp_die('Not allowed');
  }

  //check if nonce filed configuration form is present
  check_admin_referer('wp_storymap_pro');

  //retrieve original plugin options array
  $options = storymap_pro_get_options();

  //loop text form fields and store values in options array
  foreach ( array( 'heigth', 'width' ) as $option_name ) {
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] =
				sanitize_text_field($_POST[$option_name]);
		}
	}

  // Store updated options array to database
	update_option( 'storymap_pro_options', $options );

  wp_redirect( add_query_arg( 'page', 'ch2pho-my-google-analytics', admin_url( 'options-general.php' ) ) );
	exit;
}


/**
 * CREATE SETTINGS MENÚ INTERFACE
 * ============================================================================
 *
 * Function called when the admin menu is constructed to add a new menu item
 * to the structure
 *
 */

add_action( 'admin_menu', 'storymap_pro_admin_menu' );

function storymap_pro_admin_menu () {
  //add_options_page('StoryMap Configuration', 'StoryMap', 'manage_options', 'storymap-config', 'storymap_config_page1');
  //Main menu
  add_menu_page('StoryMap Configuration', 'StoryMap', 'manage_options', 'storymap-pro-config', 'storymap_pro_config_page1',
    $icon_url = 'dashicons-location'
  );

  //Sub menu Stories


  //Sub menu points
  add_submenu_page('storymap-pro-config','List of Points', 'List of Points', 'manage_options', 'storymap-pro-points-list', 'storymap_pro_points_page_handler');
  add_submenu_page('storymap-pro-config','Add New Point', 'Add New Point', 'manage_options', 'storymap-pro-edit-points', 'storymap_pro_points_form_page_handler');

  //Sub menu stories
  add_submenu_page('storymap-pro-config','List of Stories', 'List of Stories', 'manage_options', 'storymap-pro-mystories-list', 'storymap_pro_my_stories_page_handler');
  add_submenu_page('storymap-pro-config','Add New Story', 'Add New Story', 'manage_options', 'storymap-pro-edit-stories', 'storymap_pro_my_stories_form_page_handler');

}

/**
  *Congiguration main menu
  */

function storymap_pro_config_page1() {
 // Retrieve plugin configuration options from database
 $options = storymap_pro_get_options();
 ?>

 <div id="wpstorymap-pro-general" class="wrap">
   <h2>WordPress StoryMap Pro</h2>
   <form name="storymap_pro_options_form_settings_api" method="post" action="options.php">
     <!--<input type="hidden" name="action" value="save_ch2pho_options" />-->
     <!-- Adding security through hidden referrer field -->
     <?php wp_nonce_field( 'ch2pho' ); ?>
     <?php settings_fields( 'storymap_pro_settings' ); ?>
     <?php do_settings_sections( 'storymap_pro_settings_section' ); ?>
     This is the perfect plugin to easily create your own storymaps on your WordrdPress site.
     In an interactive way, you will be able to define the characteristics off all your stories (title, basemap) and it’s associated points (title, description, image, position, zoom level)
     With a simple shortcode, they will automatically be displayed on the map.
     </br></br>
     Just add a shortcode to your page like this example: [wp_storymap_pro story_number="1"]
     </br>
     Replace the number by the number of your story.
     <!--<input type="submit" value="Submit" class="button-primary"/>-->
    </br></br>
    For personalized implementations or new functionalities, please contact with <a href="https://picbook.es">Picbook</a>
    </br><br>
    Visualize an example at <a href="https://www.picbook.es/info/?p=86">Picbook Wordpress StoryMap</a>
   </form>
 </div>
<?php }


/**
  *Congiguration sub menu
  */

//my stories menu
require_once('my-stories.php');
storymap_pro_my_stories();

//points table menu
require_once('points-table.php');
storymap_pro_table_points();






//PLUGIN FRONTEND
/**********************************
SHORTCODE STORYMAP
*********************************/



add_shortcode('wp_storymap_pro', 'storymap_pro_shortcode');
function storymap_pro_shortcode($atts){
    extract (shortcode_atts(array (
      'story_number' => ''
    ), $atts ));

      $output = '
                  <div class="row">
                    <div class="col-md-12" id="map-pro" data-value= ' . $story_number .'>
                    </div>
                  </div>';


    return $output;
}


add_action('wp_enqueue_script', 'storymap_pro_scripts_basic');
function storymap_pro_scripts_basic(){
  wp_enqueue_script('jquery');
  add_thickbox();
}

//add options to js file
//https://gist.github.com/wboykinm/5730504

//function to know data of selected storymap
function storymap_pro_current_story(){
  global $wpdb;
  $table_name = $wpdb->prefix . 'storymap_pro_my_stories';
  $myrows = $wpdb->get_results("SELECT*FROM $table_name");

  $json = array(
    'stories' =>array()
  );
  foreach($myrows as $row){
    $story = array(
      'properties' => array(
          'number' => $row->storyNumber,
          'name' => $row->name,
          'description' => $row->description,
          'image' => $row->image,
          'map' => $row->basemap,
          'height' => $row ->height,
          'show_line' => $row ->show_line
      )
    );
    array_push($json['stories'], $story);
  }

  wp_enqueue_script('pw-script2', plugin_dir_url( __FILE__ ) . 'js/pw-script.js');
	wp_localize_script('pw-script2', 'pw_script_vars2_pro', array(
			'stories' => $json
    )
  );
}

add_action('wp_enqueue_scripts', 'storymap_pro_current_story');

//function to load points in geojson
function storymap_pro_load_scripts_1(){
  global $wpdb;
  $options = storymap_pro_get_options();
  $table_name = $wpdb->prefix . 'storymap_pro_points';
  $myrows = $wpdb->get_results("SELECT*FROM $table_name");

  $geojson = array(
    'type' => 'FeatureCollection',
    'features' => array()
  );

  foreach($myrows as $row){
    //build $geojson
    $feature = array(
    'type' => 'Feature',
    'geometry' => array(
        'type' => 'Point',
        'coordinates' => array($row->lon,$row->lat)
    ),
    # Pass other attribute columns here
    'properties' => array(
        'name' => $row->name,
        'description' => $row->description,
        'zoom' => $row->zoom,
        'id' => $row->num,
        'storyId' => $row->storyId,
        'image' => $row->image,
        'color' => $row->color,
        'shapeColor' => $row->shapeColor,
        'shape' => $row ->shape
        )
    );
    array_push($geojson['features'], $feature);
  }

  wp_enqueue_script('pw-script1', plugin_dir_url( __FILE__ ) . 'js/pw-script.js');
	wp_localize_script('pw-script1', 'pw_script_vars1_pro', array(
			'points' => $geojson,
      'options' => $options
    )
  );
}

add_action('wp_enqueue_scripts', 'storymap_pro_load_scripts_1');


add_action('wp_footer', 'storymap_pro_footer_code');
function storymap_pro_footer_code(){

    if ( ! wp_script_is( 'jquery', 'enqueued' )) {
      // include custom jQuery
      add_action('wp_enqueue_scripts', 'load_jquery');
      function load_jquery(){
        wp_enqueue_script('jquery');
      }

    }


    $options = storymap_pro_get_options();


    wp_enqueue_editor();



    global $post;
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wp_storymap_pro') ) {

      wp_enqueue_style('storymap_pro_leafletcss', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.css');
      wp_enqueue_style('storymap_pro_generalstyle', plugin_dir_url( __FILE__ ) . 'style.css');
      wp_enqueue_script('storymap_pro_leafletjs', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.js');
      //font awesome
      wp_enqueue_style('storymap_pro_fontawesomepickerall', "https://use.fontawesome.com/releases/v5.5.0/css/all.css");
      wp_enqueue_style('storymap_pro_fontawesome', plugin_dir_url( __FILE__ ) . 'font-awesome/css/font-awesome.min.css');
      //bootstrap
      wp_enqueue_style('storymap_pro_bootstrapcss', plugin_dir_url( __FILE__ ) . 'bootstrap/bootstrap.min.css');
      wp_enqueue_script('storymap_pro_pooperjs', plugin_dir_url( __FILE__ ) . 'bootstrap/popper.min.js');
      wp_enqueue_script('storymap_pro_bootstrapjs', plugin_dir_url( __FILE__ ) . 'bootstrap/bootstrap.min.js');
      //leaflet extra markers
      wp_enqueue_script('storymap_pro_markerjsurl', plugin_dir_url( __FILE__ ) . 'leaflet-storymap-master/markers/leaflet.extra-markers.min.js');
      wp_enqueue_style('storymap_pro_markercssurl', plugin_dir_url( __FILE__ ) . 'leaflet-storymap-master/markers/leaflet.extra-markers.min.css');
      wp_enqueue_style('storymap_pro_stylemarker', plugin_dir_url( __FILE__ ) . 'leaflet-storymap-master/style.css');
      // mini maps
      wp_enqueue_style('storymap_pro_minimapscssurl', plugin_dir_url( __FILE__ ) . 'leaflet/MiniMaps/Control.MiniMap.css');
      wp_enqueue_script('storymap_pro_minimapsjsurl', plugin_dir_url( __FILE__ ) . 'leaflet/MiniMaps/Control.MiniMap.js');
      // leaflet active area
      wp_enqueue_script('storymap_pro_activearea', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.activearea.js');

      wp_enqueue_script('storymap_pro_storymapjs', plugin_dir_url( __FILE__ ) . 'wp-storymap-pro.js');
    }

}
