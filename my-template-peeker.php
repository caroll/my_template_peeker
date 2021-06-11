<?php
/*
Plugin Name: Template Peeker
Version: 1.0
Description: Shows information of the template each page uses
Author: Caroll caroll@carollices.me
Author URI: https://carollices.me
License: GPLv3
*/

/* Start Adding Functions Below this Line */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( is_admin() ) {
  /*
   * adds page-layout to the columns's list
   */
  add_filter( 'manage_pages_columns', 'peeker_custom_pages_columns' );
  function peeker_custom_pages_columns( $columns ) {
    $custom_column = array(
      'page-layout' => __( 'Template', 'textdomain' )
    );
    $columns = array_merge( $columns, $custom_column );
    return $columns;
  }
  /*
   * adds content to the page-layout column
   */
  add_action( 'manage_pages_custom_column', 'peeker_page_custom_column_views', 5, 2 );
  function peeker_page_custom_column_views( $column_name, $id ) {
    if ( $column_name === 'page-layout' ) {
      $set_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
      if ( $set_template == 'default' || $set_template == null ) {
        esc_html_e( 'Default Template', 'text_domain' );
      }
      $templates = get_page_templates();
      ksort( $templates );
      foreach ( array_keys( $templates ) as $template ) {
        if ( $set_template == $templates[$template] ) {
          echo $template.' ('.$set_template.') ';
        }
      }
    }
  }
  /*
   * creates filter
   */
  add_action( 'restrict_manage_posts', function(){
    global $wpdb, $table_prefix;
    $post_type = (isset($_GET['post_type']));
    //only add filter to post type you want: pages
    if ( $post_type == 'page' ){
      $templates = get_page_templates();
      ksort( $templates );
      $current = isset($_GET['admin_filter_page_layout'])? $_GET['admin_filter_page_layout'] : '';
      ?>
      <select name="admin_filter_page_layout" >
        <option value="">All templates</option>
        <?php
        foreach ( $templates as $key => $value ) {
          printf( '<option value="%s" %s >%s</option>',
            $value,
            $value == $current? ' selected="selected"':'',
            $key );
        }
        ?>
      </select>
      <?php
    }
  });
  /*
   * changes main query to add template filtering
   */
  add_filter( 'parse_query', function($query){
    global $pagenow;
    $post_type = (isset($_GET['post_type']));
    if ($post_type == 'page' && $pagenow=='edit.php' && isset($_GET['admin_filter_page_layout']) && !empty($_GET['admin_filter_page_layout'])) {
      $query->query_vars['meta_key'] = '_wp_page_template';
      $query->query_vars['meta_value'] = $_GET['admin_filter_page_layout'];
    }
  });
}





/* Stop Adding Functions Below this Line */
?>
