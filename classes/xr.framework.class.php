<?php
if ( ! defined('ABSPATH') ) die;
/**
 *
 * Options Framework class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */

class Xeroft_Framework extends Xeroft_Framework_Helper {

    /**
    *
    * @access public 
    * @var array 
    *
    */
    public $settings =  array();

    /**
    *
    * @access public 
    * @var array 
    *
    */
    public $sections  =  array();

    /**
    *
    * @access public 
    * @var array 
    *
    */
    public $options =  array();

    /**
    *
    * @access public 
    * @var array 
    *
    */
    public $get_option =  array();

    /**
    *
    * @access private 
    * @var class 
    *
    */
    private static $instance = null;

    /**
    *
    * @access public
    *
    * Constructor Method of Class Xeroft_Framework
    */
    public function __construct( $settings, $options ) {
        $this->settings = apply_filters( 'xeroft_framework_settings', $settings);
        $this->options = apply_filters( 'xeroft_framework_options', $options);

        if ( ! empty($this->options) ) {
            $this->sections   = $this->get_section();
            $this->get_option = get_option( 'xr_options' );
            add_action( 'admin_init', array($this, 'settings_api') );
            add_action( 'admin_menu', array($this, 'admin_menu') );
            foreach ( $this->options as $option) {
                if ( isset( $option['is_meta'] ) && $option['is_meta'] == true ) {
                    add_action( 'add_meta_boxes', array( $this, 'register_meta_options' ) );
                    add_action( 'save_post', array( $this, 'save_metabox' ) );
                }
            }        
        }
    }


    public static function instance( $settings = array(), $options = array() ) {
        if ( is_null(self::$instance) ) {
            self::$instance = new self( $settings, $options );
        }
        return self::$instance;
    }

    public function options_validate($input) {
        foreach( $this->sections as $section ) {
            foreach ($section['fields'] as $field) {
                $newinput[$field['id']] = isset( $input[$field['id']] ) ? $input[$field['id']] : '';   
            }
        }
        return $newinput; 
    }

    public function get_section() {
        foreach( $this->options as $key => $value ) {
            if ( isset( $value['fields'] ) ) {
                $sections[] = $value;
            }
        }

        return $sections;
    }


    /**
    *
    * Field Type
    * 
    */
    public function field_callback( $field ) {
        call_user_func( array(&$this, 'add_' . $field['type'] . '_field'), $field );
    }


    public function field_meta_callback( $post, $field ) {
        wp_nonce_field( 'xr_nonce_action', 'xr_nonce' );
        foreach( $field['args']['fields'] as $field_value ) {
            call_user_func( array(&$this, 'add_meta_' . $field_value['type'] . '_field'), $post, $field_value );
        }
    }

    /**
    * Register Post meta fields
    *
    */
    public function register_meta_options() {
        foreach ($this->options as $field_key => $field ) {
            if ( isset( $field['is_meta'] ) && $field['is_meta'] == true ) {
                add_meta_box( $field['name'] . '_meta', $field['title'], array(&$this, 'field_meta_callback' ), $field['post_type'], $field['context'], 'default', $field );
            }
        }
    }


    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id ) {
        
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['xr_nonce'] ) ? $_POST['xr_nonce'] : '';
        $nonce_action = 'xr_nonce_action';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        
        $post_meta_array = array();
        foreach ($this->options as $field) {
            if ( isset( $field['is_meta'] ) && $field['is_meta'] == true ) {
                foreach ( $field['fields'] as $value) { 
                    $post_value = 'xr_meta_options_' . $value['id'];
                    $post_meta_array['_xr_meta_key_' . $value['id']] = isset( $_POST[$post_value] ) ? $_POST[$post_value] : '';
                    foreach ($post_meta_array as $meta_key => $meta_value) {
                        /*$old_meta = get_post_meta( $post_id, $meta_key, false); */
                        if ( empty( $meta_value ) || ! $meta_value ) {
                            delete_post_meta( $post_id, $meta_key, $meta_value );
                        } else {    
                            update_post_meta( $post_id, $meta_key, $meta_value );
                        }
                    }   
                }
            }
        }
        
    }


    public function settings_api() {
        foreach ($this->sections as $section) {
            if ( isset( $section['is_meta'] ) && $section['is_meta'] == false ) {
                register_setting( 'xr_options_group', 'xr_options', array(&$this, 'options_validate') );
            
                add_settings_section( $section['name'] . '_section', $section['title'], '', $section['name'] . '_section_group' );
            
                foreach ( $section['fields'] as $field_key => $field ) {
                    add_settings_field( $field_key . '_field', '', array(&$this, 'field_callback'), $section['name'] . '_section_group', $section['name'] . '_section', $field );
                }  
            }
        }  
    }

    
    /**
    *
    * Adding Menu Item
    *
    */
    public function admin_menu() {
        $defaults = array(
            'parent_slug' => '',
            'page_title' => '',
            'menu_title' => '',
            'menu_type' => '',
            'menu_slug' => '',
            'capability' => 'manage_options',
            'icon_url'   => '',
            'position'   => null, 
            );

        $args = wp_parse_args( $this->settings, $defaults );
        
        if( $args['menu_type'] == 'submenu' ) {
            call_user_func( 'add_' . $args['menu_type'] . '_page' , $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], array( &$this, 'admin_page' ) );
        } else {
            call_user_func( 'add_' . $args['menu_type'] . '_page', $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], array( &$this, 'admin_page' ) , $args['icon_url'], $args['position'] );
        }
        
    }

    public function admin_page() { ?>
        <div class="wrap">
            <form method="post" action="options.php" enctype="mulipart/form-data">
                <?php 
                settings_fields('xr_options_group');
                foreach ($this->sections as $section) {
                    do_settings_sections( $section['name'] . '_section_group' );
                }
                submit_button( 'Save Changes', 'primary', 'submit' );
                ?>
            </form>
        </div>
    <?php
    }

}


