<?php
/**
 * @package Workevo
 * @version 0.1
 */
/*
Plugin Name: Workevo
Plugin URI: https://wordpress.org/plugins/workevo
Description: Official <a href="https://www.workevo.com">Workevo</a> plugin for WordPress.
Author: Workevo team
Version: 0.1
Author URI: https://www.workevo.com
*/

function workevo_register_admin_menu_page(){
  add_menu_page( 
      __( 'Workevo Settings', 'workevo' ),
      __( 'Workevo', 'workevo' ),
      'manage_options',
      'workevo',
      'workevo_settings',
      'dashicons-screenoptions'
  ); 
}
add_action( 'admin_menu', 'workevo_register_admin_menu_page' );

function workevo_register_settings() {
  add_option( 'workevo_workspace_id', '' );
  register_setting( 'workevo_options_group', 'workevo_workspace_id', 'workevo_callback' );
}
add_action( 'admin_init', 'workevo_register_settings' );

function workevo_settings(){ ?>
<div class="wrap">
  <h1>Workevo Settings</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'workevo_options_group' ); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="workevo_workspace_id">Workspace ID</label>
        </th>
        <td>
          <input class="regular-text" type="text" id="workevo_workspace_id" name="workevo_workspace_id" value="<?php echo get_option( 'workevo_workspace_id' ); ?>" />
          <p class="description">Enter your Workevo's workspace ID.</p>
        </td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
  </div>
<?php }

function workevo_tracking() {
if ( get_option( 'workevo_workspace_id' ) ) :
  $workspace_id = get_option( 'workevo_workspace_id' ); 
  ?>
  <script src="https://static.workevo.io/workevo.min.js"></script>
  <?php
    if ( is_user_logged_in() ) :
    $current_user = wp_get_current_user();
  ?>
  <script>
    workevo('create', '<?php echo $workspace_id; ?>', { 
      email: '<?php echo $current_user->user_email; ?>',
      first_name: '<?php echo $current_user->user_firstname; ?>',
      last_name: '<?php echo $current_user->user_lastname; ?>',
      avatar: '<?php echo get_avatar_url($current_user->id) ?>',
      wp_user_id: <?php echo $current_user->ID; ?>,
      username: '<?php echo $current_user->user_login; ?>',
      signed_up_at: <?php echo strtotime($current_user->user_registered) . '001'; ?>,
      roles: '<?php echo implode( ", ", $current_user->roles ); ?>'
    });

<?php if ( is_front_page() && get_option( 'page_on_front' ) ) : ?>
    workevo( 'send', 'Front Page Viewed', {
      'title': '<?php wp_title(); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_home() && get_option( 'page_for_posts' ) ) : ?>
    workevo( 'send', 'Blog Viewed', {
      'title': '<?php wp_title() ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_home() ) : ?>
    workevo( 'send', 'Main Page Viewed', {
      'title': '<?php wp_title(); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_category() ) : ?>
<?php
  $category = get_category( get_query_var( 'cat' ) );
  $cat_id = $category->cat_ID;
?>
    workevo( 'send', 'Category Viewed', {
      'id': '<?php esc_html_e( $cat_id ); ?>',
      'title': '<?php single_cat_title(); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_tag() ) : ?>
<?php
  $term = get_term_by( 'name', get_query_var( 'tag' ), 'post_tag' );
  $tag_id = $term->term_id;
?>
    workevo( 'send', 'Tag Viewed', {
      'id': '<?php esc_html_e( $tag_id ); ?>',
      'title': '<?php single_tag_title(); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_author() ) : ?>
    workevo( 'send', 'Author Archive Viewed', {
      'id': '<?php esc_html_e( get_the_author_meta( 'ID' )); ?>',
      'title': '<?php echo get_the_author(); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_year() ) : ?>
    workevo( 'send', 'Yearly Archive Viewed', {
      'title': '<?php echo get_the_date( 'Y' ); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_month() ) : ?>
    workevo( 'send', 'Monthly Archive Viewed', {
      'title': '<?php echo get_the_date( 'F Y' ); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_day() ) : ?>
    workevo( 'send', 'Daily Archive Viewed', {
      'title': '<?php echo get_the_date( 'F j, Y' ); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_tax( 'post_format' ) ) : ?>
    workevo( 'send', 'Post Format Archive Viewed', {
<?php if ( is_tax( 'post_format', 'post-format-aside' ) ) : ?>
      'title': 'Asides',
<?php elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) : ?>
      'title': 'Galleries',
<?php elseif ( is_tax( 'post_format', 'post-format-image' ) ) : ?>
      'title': 'Images',
<?php elseif ( is_tax( 'post_format', 'post-format-video' ) ) : ?>
      'title': 'Videos',
<?php elseif ( is_tax( 'post_format', 'post-format-quote' ) ) : ?>
      'title': 'Quotes',
<?php elseif ( is_tax( 'post_format', 'post-format-link' ) ) : ?>
      'title': 'Links',
<?php elseif ( is_tax( 'post_format', 'post-format-status' ) ) : ?>
      'title': 'Statuses',
<?php elseif ( is_tax( 'post_format', 'post-format-audio' ) ) : ?>
      'title': 'Audio',
<?php elseif ( is_tax( 'post_format', 'post-format-chat' ) ) : ?>
      'title': 'Chats',
<?php endif; ?>
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_post_type_archive() ) : ?>
    workevo( 'send', '<?php post_type_archive_title( '', true ); ?> Archive Viewed', {
      'title': '<?php post_type_archive_title( '', true ); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_tax() ) : ?>
<?php $queried_object = get_queried_object(); ?>
<?php if ( $queried_object ) : $tax = get_taxonomy( $queried_object->taxonomy ); ?>
    workevo( 'send', '<?php echo $tax->labels->singular_name; ?> Archive Viewed', {
      'title': '<?php single_term_title( '', true ); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php endif; ?>
<?php elseif ( is_404() ) : ?>
    workevo( 'send', '404 Viewed', {
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif ( is_search() ) : ?>
    workevo( 'send', 'Searched', {
      'query': '<?php esc_html_e( get_search_query() ); ?>',
      'url': '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>'
    });
<?php elseif (is_singular( 'download', 'product' )) : ?>
<?php
  $id = get_the_ID();
  $sku = get_post_meta( $id, 'edd_sku', true );
  $price = get_post_meta( $id, 'edd_price', true );
  $category = wp_get_post_terms( $id, 'download_category', array( 'fields' => 'names' ) );
?>
    workevo( 'send', 'Product Viewed', {
      product_id: '<?php esc_html_e( $id ); ?>',
<?php if ( $sku ) : ?>
      sku: '<?php esc_html_e( $sku ); ?>',
<?php endif; ?>
<?php if ( $category ) : ?>
      category: '<?php esc_html_e( $category[0] ); ?>',
<?php endif; ?>
      name: '<?php the_title_attribute( $id ) ?>',
<?php if ( $price ) : ?>
      price: <?php esc_html_e( $price ); ?>,
<?php endif; ?>
      url: '<?php esc_html_e( $_SERVER['REQUEST_URI'] ); ?>',
<?php if ( has_post_thumbnail() ) : ?>
      image_url: '<?php the_post_thumbnail_url( $id ); ?>',
<?php endif; ?>
    });
    $( ".maki-edd-add-to-cart" ).click(function() {
      workevo(
        'create', '<?php echo $workspace_id; ?>'
      );
      workevo( 'send', 'Product Added', {
        product_id: '<?php esc_html_e( $id ); ?>',
<?php if ( $sku ) : ?>
        sku: '<?php esc_html_e( $sku ); ?>',
<?php endif; ?>
<?php if ( $category ) : ?>
        category: '<?php esc_html_e( $category[0] ); ?>',
<?php endif; ?>
        name: '<?php the_title_attribute( $id ) ?>',
<?php if ( $price ) : ?>
        price: <?php esc_html_e( $price ); ?>,
<?php endif; ?>
        url: '<?php the_permalink( $id ); ?>',
<?php if ( has_post_thumbnail() ) : ?>
        image_url: '<?php the_post_thumbnail_url( $id ); ?>',
<?php endif; ?>
      });
    });
<?php elseif ( is_singular( 'post' ) ) : $id = get_the_ID(); ?>
    workevo( 'send', 'Post Viewed', {
      'id': '<?php esc_html_e( $id ); ?>',
      'title': '<?php the_title_attribute( $id ) ?>',
      'url': '<?php the_permalink( $id ); ?>',
<?php if ( has_post_thumbnail( $id ) ) : ?>
      'thumbnail_url': '<?php echo get_the_post_thumbnail_url( $id ); ?>',
<?php endif; ?>
    });
<?php elseif ( is_page() ) : $id = get_the_ID(); ?>
    workevo( 'send', 'Page Viewed', {
      'id': '<?php esc_html_e( $id ); ?>',
      'title': '<?php the_title_attribute( $id ) ?>',
      'url': '<?php the_permalink( $id ); ?>'
    });
<?php elseif ( is_singular() ) :
  $id = get_the_ID();
  $post_type_obj = get_post_type_object( get_post_type( $id ) );
  ?>
    workevo( 'send', '<?php esc_html_e( $post_type_obj->labels->singular_name ); ?> Viewed', {
      'id': '<?php esc_html_e( $id ); ?>',
      'title': '<?php the_title_attribute( $id ) ?>',
      'url': '<?php the_permalink( $id ); ?>',
<?php if ( has_post_thumbnail( $id ) ) : ?>
      'thumbnail_url': '<?php echo get_the_post_thumbnail_url( $id ); ?>',
<?php endif; ?>
    });
  <?php else : ?>
    workevo('send', 'view_page');
  <?php endif; ?>
  </script>
    
  <?php else : ?>
  <script>
    workevo(
      'create', '<?php echo $workspace_id; ?>'
    );
    workevo('send', 'view_page');
  </script>
  <?php endif;
endif;
}
add_action( 'wp_footer', 'workevo_tracking' );