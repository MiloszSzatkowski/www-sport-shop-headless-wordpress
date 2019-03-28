<?php

add_filter( 'user_can_richedit' , '__return_false', 50 );

add_theme_support( 'title-tag' );

add_action( 'send_headers', function() {
    if ( ! did_action('rest_api_init') && $_SERVER['REQUEST_METHOD'] == 'HEAD' ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Expose-Headers: Link' );
        header( 'Access-Control-Allow-Methods: HEAD' );
    }
} );


add_filter( 'manage_post_posts_columns', 'set_custom_edit_post_columns' );
add_filter( 'manage_edit-post_sortable_columns', 'custom_post_column_sorting' );

add_action( 'manage_post_posts_custom_column' , 'custom_post_column', 10, 2 );

function set_custom_edit_post_columns($columns) {
  unset(  $columns['date']);
  unset(  $columns['author'] );
  unset(  $columns['categories']);
  unset(  $columns['tags'] );
  unset(  $columns['comments'] );

  $columns['price'] = 'price';
  $columns['type'] = 'type';
  $columns['colors'] = 'colors';
  $columns['sizes'] = 'sizes';
  $columns['icon'] = 'icon';
  $columns['visual_1'] = 'Visual 1';
  $columns['visual_2'] = 'Visual 2';


    return $columns;
}

function custom_post_column( $column, $post_id ) {
    switch ( $column ) {

      case 'price' :
          echo get_field( "price", $post_id );
          break;

      case 'type' :
      $content = implode(', ',apply_filters( 'the_field', get_field('type') ));
      echo $content;
          break;

      case 'colors' :
      $content = implode(', ',apply_filters( 'the_field', get_field('colors') ));
      echo $content;
          break;

      case 'sizes' :
          $content = implode(', ',apply_filters( 'the_field', get_field('sizes') ));
          echo $content;
          break;

      case 'icon' :
          $content = apply_filters( 'the_field',
          get_field('icon')['sizes']['thumbnail'] );
          echo '<img src="'.$content.'"/ width="30" height="30">';
          break;

      case 'visual_1' :
          $content = apply_filters( 'the_field',
          get_field('visual_1')['sizes']['thumbnail'] );
          echo '<img src="'.$content.'"/ width="30" height="30">';
          break;

      case 'visual_2' :
          $content = apply_filters( 'the_field',
          get_field('visual_2')['sizes']['thumbnail'] );
          echo '<img src="'.$content.'"/ width="30" height="30">';
          break;

    }
}

function custom_post_column_sorting()
{
  $columns['price'] = 'price';
  $columns['type'] = 'type';

    return $columns;

}

add_action( 'pre_get_posts', 'mycpt_custom_orderby' );

function mycpt_custom_orderby( $query ) {
  if ( ! is_admin() )
    return;

  $orderby = $query->get( 'orderby');

  if ( 'price' == $orderby ) {
    $query->set( 'meta_key', 'price' );
    $query->set( 'orderby', 'meta_value_num' );
  }

  if ( 'type' == $orderby ) {
    $query->set( 'meta_key', 'type' );
    $query->set( 'orderby', 'meta_value_num' );
  }

}

// ******************** GLOBAL FIELDS
add_action('admin_menu', 'add_gcf_interface');
function add_gcf_interface() {
	add_options_page('Global Custom Fields', 'Global Custom Fields', '8', 'functions', 'editglobalcustomfields');
}
function editglobalcustomfields() {
	?>
	<div class='wrap'>
	<h2>Global Custom Fields</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>


  <div style="max-width:600px">

    <p><strong>Company name:</strong><br />
     <input type="text" name="name" size="45" value="<?php echo get_option('name'); ?>" /></p>


   <p><strong>Company number:</strong><br />
     <input type="text" name="tel" size="45" value="<?php echo get_option('tel'); ?>" /></p>

   <p><strong>Email:</strong><br />
     <input type="text" name="email" size="45" value="<?php echo get_option('email'); ?>" /></p>

   <p><strong>Company logo source:</strong><br />
     <input type="text" name="logo_src" size="45" value="<?php echo get_option('logo_src'); ?>" /></p>
     <img src="<?php echo get_option('logo_src'); ?>" alt="">

   <p><strong>Company adress:</strong><br />
     City <br />
     <input type="text" name="city" size="45" value="<?php echo get_option('city'); ?>" /></p>
     Street and building number<br />
     <input type="text" name="street" size="45" value="<?php echo get_option('street'); ?>" /></p>
     Post code <br />
     <input type="text" name="post_code" size="45" value="<?php echo get_option('post_code'); ?>" /></p>
     Region (West Midlands ...) <br />
     <input type="text" name="region" size="45" value="<?php echo get_option('region'); ?>" /></p>

   </div>

  <p><input type="submit" name="Submit" value="Update Options" /></p>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="name, tel, logo_src, city, street, post_code, region, email" />

	</form>
	</div>
  <?php
}

register_rest_route( 'global-custom-types/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'get_custom_types',
            'args' => [
            'id'
        ],
    ));

function get_custom_types (){
  $arr = [];
  $arr['types'] = get_field_object('field_5c93e4a5bebca');
  $arr['genders'] = get_field_object('field_5c8d0ca9654d6');
  $arr['ages'] = get_field_object('field_5c8d0cce338b0');
  return $arr;
}

register_rest_route( 'global-api/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'get_custom_users_data',
            'args' => [
            'id'
        ],
    ));

function get_custom_users_data (){
  $arr = [];
  $arr['settings_name'] = 'global';
  $arr['name'] = apply_filters( 'the_option', get_option('name') );
  $arr['tel'] = apply_filters( 'the_option', get_option('tel'));
  $arr['logo_src'] = apply_filters( 'the_option', get_option('logo_src'));
  $arr['city'] = apply_filters( 'the_option', get_option('city') );
  $arr['street'] = apply_filters( 'the_option', get_option('street'));
  $arr['post_code'] = apply_filters( 'the_option', get_option('post_code'));
  $arr['region'] = apply_filters( 'the_option', get_option('region'));
  $arr['email'] = apply_filters( 'the_option', get_option('email'));
  return $arr;
}

?>
