<?php

/**
 * PART 1. Defining Custom Database Table
 * ============================================================================
 *
 * In this part you are going to define custom database table
 */

global $storymap_pro_db_version;
$storymap_pro_db_version = '1.1'; // version changed from 1.0 to 1.1

/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */


function storymap_pro_table_points()
{
    global $wpdb;
    global $storymap_pro_example_db_version;

    $table_name = $wpdb->prefix . 'storymap_pro_points'; // do not forget about tables prefix
    $related_table_name = $wpdb->prefix . 'storymap_pro_my_stories';

    // sql to create your table
    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      name tinytext NOT NULL,
      num int(10) NOT NULL,
      description VARCHAR(10000) NOT NULL,
      lat double NOT NULL,
      lon double NOT NULL,
      zoom int(2),
      image VARCHAR(500),
      storyId int(10) NOT NULL,
      color VARCHAR(1000) NOT NULL,
      shapeColor VARCHAR (1000) NOT NULL,
      shape VARCHAR(1000) NOT NULL,
      PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('storymap_pro_example_db_version', $storymap_pro_example_db_version);

    /**
     * [OPTIONAL] Example of updating to 1.1 version
     *
     */
    $installed_ver = get_option('storymap_pro_example_db_version');
    if ($installed_ver != $storymap_pro_example_db_version) {
        $sql = "CREATE TABLE " . $table_name . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          name tinytext NOT NULL,
          num int(10) NOT NULL,
          description VARCHAR(10000) NOT NULL,
          lat double NOT NULL,
          lon double NOT NULL,
          zoom int(2),
          image VARCHAR(500),
          storyId int(10) NOT NULL,
          color VARCHAR(1000) NOT NULL,
          shapeColor VARCHAR (1000) NOT NULL,
          shape VARCHAR(1000) NOT NULL,
          PRIMARY KEY  (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('storymap_pro_example_db_version', $storymap_pro_example_db_version);
    }
}

register_activation_hook(__FILE__, 'storymap_pro_install_');


/**
 * register_activation_hook implementation
 *
 * [OPTIONAL]
 * additional implementation of register_activation_hook
 * to insert some dummy data
 */
function storymap_pro_table_points_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'storymap_pro_points'; // do not forget about tables prefix

    $wpdb->insert($table_name, array(
        'name' => 'Alex',
        'num' =>1,
        'description' => 'Your Point Description',
        'lon' => -0.5,
        'lat' => 51.5,
        'zoom' => 10,
        'storyId' => 0,
        'image' => null,
        'color' => '#ffffff',
        'shapeColor' => '#000000',
        'shape' => 'fas fa-camera'
    ));
    $wpdb->insert($table_name, array(
        'name' => 'Maria',
        'num' =>1,
        'description' => 'Your Point Description',
        'lon' => -0.8,
        'lat' => 45.5,
        'zoom' => 10,
        'storyId' => 1,
        'image' => null,
        'color' => '#ffffff',
        'shapeColor' => '#000000',
        'shape' => 'fas fa-camera'
    ));
}

/**
 * Trick to update plugin database, see docs
 */
function storymap_pro_update_db_check()
{
    global $storymap_pro_example_db_version;
    if (get_site_option('storymap_pro_example_db_version') != $storymap_pro_example_db_version) {
        storymap_pro_install();
    }
}

add_action('plugins_loaded', 'storymap_pro_update_db_check');


/**
 * PART 2. Defining Custom Table List
 * ============================================================================
 *
 * In this part you are going to define custom table list class,
 * that will display your database records in nice looking table
 *
 */

 if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
* Custom_Table_Example_List_Table class that will display our custom table
* records in nice table
*/
class Custom_WP_StoryMap_Pro_List_Table extends WP_List_Table
{
   /**
    * [REQUIRED] You must declare constructor and give some basic params
    */
   function __construct()
   {
       global $status, $page;

       parent::__construct(array(
           'singular' => 'point',
           'plural' => 'points',
       ));
   }

   /**
    * [REQUIRED] this is a default column renderer
    *
    * @param $item - row (key, value array)
    * @param $column_name - string (key)
    * @return HTML
    */
   function column_default($item, $column_name)
   {
       return $item[$column_name];
   }

   /**
    * [OPTIONAL] this is example, how to render specific column
    *
    * method name must be like this: "column_[column_name]"
    *
    * @param $item - row (key, value array)
    * @return HTML
    */
   /*function column_age($item)
   {
       return '<em>' . $item['age'] . '</em>';
   }*/

   //image is represented
   function column_image($item){
     //return '<b>' . $item['image'] . '</b>';
     return '<img src="' . $item['image'] . '" width="50" height="50">';
   }

   //color is represented
   function column_color($item){
     //return '<b>' . $item['image'] . '</b>';
     $color = explode("|",$item['color'])[0];
     //return '<div style="height:10px; width:30px; background:'. $color .'"></div>';
     return '<i class="fas fa-map-marker fa-2x" style="color:' . $item['color'] . '"></i>';
   }

   // description is represented
   function column_description($item){
     return '<div style="height:130px;overflow:auto;">' . $item['description'] . '</div>';
   }

   //SHAPE color is represented
   function column_shapeColor($item){
     //$color_shape = $item['shapeColor']
     return '<div style="height:10px; width:30px; background-color:' . $item['shapeColor'] . '"></div>';
   }

   //shape is represented
   function column_shape($item){
     return '<i class="' . $item['shape'] . ' fa-2x" style="color:' . $item['shapeColor'] . '"></i>';
   }


   /**
    * [OPTIONAL] this is example, how to render column with actions,
    * when you hover row "Edit | Delete" links showed
    *
    * @param $item - row (key, value array)
    * @return HTML
    */
   function column_name($item)
   {
       // links going to /admin.php?page=[your_plugin_page][&other_params]
       // notice how we used $_REQUEST['page'], so action will be done on curren page
       // also notice how we use $this->_args['singular'] so in this example it will
       // be something like &person=2
       $actions = array(
           'edit' => sprintf('<a href="?page=storymap-pro-edit-points&id=%s">%s</a>', $item['id'], __('Edit', 'stroymap_example')),
           'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'stroymap_pro_example')),
       );

       return sprintf('%s %s',
           $item['name'],
           $this->row_actions($actions)
       );
   }

   /**
    * [REQUIRED] this is how checkbox column renders
    *
    * @param $item - row (key, value array)
    * @return HTML
    */
   function column_cb($item)
   {
       return sprintf(
           '<input type="checkbox" name="id[]" value="%s" />',
           $item['id']
       );
   }

   /**
    * [REQUIRED] This method return columns to display in table
    * you can skip columns that you do not want to show
    * like content, or description
    *
    * @return array
    */
   function get_columns()
   {
       $columns = array(
           'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
           'name' => __('Name', 'storymap_pro_example'),
           'num' => __('Point Number', 'storymap_pro_example'),
           'description' => __('Description', 'storymap_pro_example'),
           'lat' => __('Latitude', 'storymap_pro_example'),
           'lon' => __('Longitude', 'storymap_pro_example'),
           'zoom' => __('Zoom', 'storymap_pro_example'),
           'storyId' => __('Story Number', 'storymap_pro_example'),
           'image' => __('Image', 'storymap_pro_example'),
           'color' => __('Color', 'storymap_pro_example'),
           'shapeColor' => __('Shape Color', 'storymap_pro_example'),
           'shape' => __('Shape', 'storymap_pro_example'),
       );
       return $columns;
   }

   /**
    * [OPTIONAL] This method return columns that may be used to sort table
    * all strings in array - is column names
    * notice that true on name column means that its default sort
    *
    * @return array
    */
   function get_sortable_columns()
   {
       $sortable_columns = array(
           'name' => array('name', true),
           'num' => array('num', true),
       );
       return $sortable_columns;
   }

   /**
    * [OPTIONAL] Return array of bult actions if has any
    *
    * @return array
    */
   function get_bulk_actions()
   {
       $actions = array(
           'delete' => 'Delete'
       );
       return $actions;
   }

   /**
    * [OPTIONAL] This method processes bulk actions
    * it can be outside of class
    * it can not use wp_redirect coz there is output already
    * in this example we are processing delete action
    * message about successful deletion will be shown on page in next part
    */
   function process_bulk_action()
   {
       global $wpdb;
       $table_name = $wpdb->prefix . 'storymap_pro_points'; // do not forget about tables prefix

       if ('delete' === $this->current_action()) {
           $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
           if (is_array($ids)) $ids = implode(',', $ids);

           if (!empty($ids)) {
               $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
           }
       }
   }

   /**
    * [REQUIRED] This is the most important method
    *
    * It will get rows from database and prepare them to be showed in table
    */
   function prepare_items()
   {
       global $wpdb;
       $table_name = $wpdb->prefix . 'storymap_pro_points'; // do not forget about tables prefix

       $per_page = 15; // constant, how much records will be shown per page

       $columns = $this->get_columns();
       $hidden = array();
       $sortable = $this->get_sortable_columns();

       // here we configure table headers, defined in our methods
       $this->_column_headers = array($columns, $hidden, $sortable);

       // [OPTIONAL] process bulk action if any
       $this->process_bulk_action();

       // will be used in pagination settings
       $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

       // prepare query params, as usual current page, order by and order direction
       $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
       $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'name';
       $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

       // [REQUIRED] define $items array
       // notice that last argument is ARRAY_A, so we will retrieve array
       $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

       // [REQUIRED] configure pagination
       $this->set_pagination_args(array(
           'total_items' => $total_items, // total items defined above
           'per_page' => $per_page, // per page constant defined at top of method
           'total_pages' => ceil($total_items / $per_page) // calculate pages count
       ));
   }
}


/**
 * PART 3. Admin page
 * ============================================================================
 *
 * In this part you are going to add admin page for custom table
 */

 function storymap_pro_points_page_handler()
 {
     global $wpdb;

     $table = new Custom_WP_StoryMap_Pro_List_Table();
     $table->prepare_items();

     $message = '';
     if ('delete' === $table->current_action()) {
         $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'storymap_pro_example'), count([$_REQUEST['id']])) . '</p></div>';
     }
     ?>
       <div class="wrap">

           <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
           <h2><?php _e('Points', 'storymap_pro_example')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=storymap-pro-edit-points');?>"><?php _e('Add new point', 'storymap_pro_example')?></a>
           </h2>
           <?php echo $message; ?>

           <form id="points-table" method="GET">
               <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
               <?php $table->display() ?>
           </form>

       </div>
     <?php
 }

 /**
  * PART 4. Form for adding andor editing row
  * ============================================================================
  *
  * In this part you are going to add admin page for adding andor editing items
  */

  /**
   * Form page handler checks is there some data posted and tries to save it
   * Also it renders basic wrapper in which we are callin meta box render
   */
function storymap_pro_points_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'storymap_pro_points'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'name' => '',
        'num' => 1,
        'description' => '',
        'lon' => null,
        'lat' => null,
        'zoom' => null,
        'storyId' => 0,
        'image' =>null,
        'color' => '#FFFFFF',
        'shapeColor' => '#000000',
        'shape' => 'fas fa-camera',
    );

    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = storymap_pro_validate_person($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'storymap_pro_example');
                } else {
                    $notice = __('There was an error while saving item', 'storymap_pro_example');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'storymap_pro_example');
                } else {
                    $notice = __('There was an error while updating item', 'storymap_pro_example');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'storymap_pro_example');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('points_form_meta_box', 'Point data', 'storymap_pro_example_point_form_meta_box_handler', 'point', 'normal', 'default');

    ?>
    <div class="wrap">
      <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
      <h2><?php _e('Points', 'storymap_pro_example')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=storymap-pro-points-list');?>"><?php _e('back to list', 'storymap_pro_example')?></a>
      </h2>

      <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
      <?php endif;?>
      <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
      <?php endif;?>

      <form id="form" method="POST">
          <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
          <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
          <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

          <div class="metabox-holder" id="poststuff">
              <div id="post-body">
                  <div id="post-body-content">
                      <?php /* And here we call our custom meta box */ ?>
                      <?php do_meta_boxes('point', 'normal', $item); ?>
                      <input type="submit" value="<?php _e('Save', 'storymap_pro_example')?>" id="submit" class="button-primary" name="submit">
                  </div>
              </div>
          </div>
      </form>
  </div>
<?php
}


/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function storymap_pro_example_point_form_meta_box_handler($item)
{
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'storymap_pro_example')?></label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                   size="50" class="code" placeholder="<?php _e('Your name', 'storymap_pro_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="num"><?php _e('Point Number', 'storymap_pro_example')?></label>
        </th>
        <td>
            <input id="num" name="num" type="number" style="width: 95%" value="<?php echo esc_attr($item['num'])?>"
                   size="50" class="code" placeholder="<?php _e('Point Number', 'storymap_pro_example')?>" required>
        </td>
    </tr>

    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="description"><?php _e('Description', 'storymap_pro_example')?></label>
        </th>
        <td>
            <?php $args = array(
                'editor_height' => 200,
                'media_buttons' => false,
                'teeny' => true,
                'dfw' => false,
                'tinymce' => true,
                'wpautop' => false,
                'quicktags' => true
            );

            $str = stripslashes_deep($item['description']);
            wp_editor($str, 'description', $args );

            //$table_update = $wpdb->prefix . 'storymap_pro_points';
            //$wpdb->update( $table_update, $data, $where );
            //UPDATE wp_storymap_pro_points SET description = replace(description, '\\', '')
            ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="lat"><?php _e('Latitude', 'storymap_pro_example')?></label>
        </th>
        <td>
            <input id="lat" name="lat" oninput="changeLatLongZoom()" type="number" step="any" style="width: 95%" value="<?php echo esc_attr($item['lat'])?>"
                   size="50" class="code" placeholder="<?php _e('Latitude', 'storymap_pro_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="lon"><?php _e('Longitude', 'storymap_pro_example')?></label>
        </th>
        <td>
            <input id="lon" name="lon" oninput="changeLatLongZoom()" type="number" step="any" style="width: 95%" value="<?php echo esc_attr($item['lon'])?>"
                   size="50" class="code" placeholder="<?php _e('Longitude', 'storymap_pro_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
      <th valign="top" scope="row">
      </th>
      <td>
        <div id="wp-storymap-pro-picker" style="width: 95%; height: 400px;"></div>
      </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="zoom"><?php _e('Zoom', 'storymap_pro_example')?></label>
        </th>
        <td>
            <input id="zoom" oninput="changeLatLongZoom()" name="zoom" type="number" style="width: 95%" value="<?php echo esc_attr($item['zoom'])?>"
                   size="50" class="code" placeholder="<?php _e('Zoom', 'storymap_pro_example')?>" required>
        </td>
    </tr>

    <tr class="form-field">
        <?php
        global $wpdb;

        $table_name = $wpdb->prefix . 'storymap_pro_my_stories';
        $myrows = $wpdb->get_results("SELECT storyNumber FROM $table_name");
        ?>
        <th valign="top" scope="row">
            <label for="story"><?php _e('Story Number', 'storymap_pro_example')?></label>
        </th>
        <td>
            <select id="storyId" name="storyId" style="width:95%">
                         <?php
                         $three = "three three";
                    
                         foreach($myrows as $row){
                           //https://stackoverflow.com/questions/8193093/extracting-the-html-form-option-value-which-has-spaces-in-bitween
                          /*?><option value=<?php echo esc_textarea($row->name) ?>><?php echo esc_attr($row->name)?></option><?php*/
                          if ($row->storyNumber == $item['storyId']) {
                            ?><option value=<?php echo esc_attr($row->storyNumber) ?> selected = <?php echo esc_attr($row->storyNumber) ?>><?php echo esc_attr($row->storyNumber)?></option><?php
                          }else{
                            ?><option value=<?php echo esc_attr($row->storyNumber) ?>><?php echo esc_attr($row->storyNumber)?></option><?php
                          }

                          //echo '<option value="'.$row ->name.'">'.$row->name.'</option>';
                         }
                         ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="image"><?php _e('Image', 'storymap_pro_example')?></label>
        </th>
        <td>
            <input id="image-url" type="text" name="image" value="<?php echo esc_attr($item['image'])?>"/>
            <input id="upload-button" type="button" class="button" value="Upload Image"
                   size="500" class="code" placeholder="<?php _e('Image', 'storymap_pro_example')?>" required>
        </td>
    </tr>

    <tr class="form-field">
      <th valign="top" scope="row">
          <label for="shape"><?php _e('Marker Shape', 'storymap_pro_example')?></label>
      </th>

      <td>

        <div class="input-group iconpicker-container">
          <span style="padding-left:5px; cursor:pointer" class="input-group-addon fa-2x" ><i id="classicon" class="icona fas fa-map-marker-alt" style="color:<?php echo esc_attr($item['shapeColor'])?>"></i></span>
          <input type="hidden" id="shape" name="shape" class="form-control icp demo iconpicker-element iconpicker-input" data-placement="bottomLeft" value="<?php echo esc_attr($item['shape'])?>"/>
        </div>

      </td>
      <script>
        jQuery('.demo').iconpicker({
          placement:'bottomLeft',
          hideOnSelect: true
        });
      </script>

    </tr>

    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="shapeColor"><?php _e('Shape Color', 'storymap_pro_example')?></label>
        </th>

        <td>
          <input onchange="canviColor()" style="cursor:pointer" type="color" id="shapeColor" name="shapeColor" value="<?php echo esc_attr($item['shapeColor'])?>">

        </td>

        <script>

        function canviColor() {
          var color = document.getElementById("shapeColor").value;
          document.getElementById('classicon').style.color = ""+color+"";
          $('.icon-marker').css('color', color);

        }
        </script>

    </tr>

    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="color"><?php _e('Marker Color', 'storymap_pro_example')?></label>
        </th>

        <td>
          <input onchange="canviColorMarker()" style="cursor:pointer" type="color" id="color" name="color" value="<?php echo esc_attr($item['color'])?>">

        </td>

        <script>
        function canviColorMarker(){
          var color = document.getElementById("color").value;
          $('.fa-stack-2x').css('color', color);
        }
        </script>

    </tr>


    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function storymap_pro_validate_person($item)
{
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'cltd_example');
    if (empty($item['image'])) $messages[] = __('Image is required', 'cltd_example');
    if (empty($item['zoom'])) $messages[] = __('Zoom value is required', 'cltd_example');
    if (empty($item['lat'])) $messages[] = __('Latitude value is required', 'cltd_example');
    if (empty($item['lon'])) $messages[] = __('Longitude value is required', 'cltd_example');
    if (empty($item['description'])) $messages[] = __('Description is required', 'cltd_example');
    if (empty($item['num'])) $messages[] = __('Number of point is required', 'cltd_example');
    if($item['zoom'] < 0 || $item['zoom'] > 20)  $messages[] = __('Zoom must be between 0 and 20');
    //if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'cltd_example');
    //if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'cltd_example');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}
