<?php
if ( ! defined('ABSPATH') ) die;
/**
 *
 * Helper Class Framework class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */


class Xeroft_Framework_Helper extends Xeroft_Framework_Abstract {
	
	public function __construct() {}

	// Checked Helper function
    public function checked( $checked, $current = true, $echo = true ) {
        if ( is_array($checked) && in_array( $current, $checked ) ) {
            $output = 'checked="checked"';
        } elseif( $checked == $current ) {
            $output = 'checked="checked"';
        } else {
            $output = '';
        }

        if ( $echo ) {
            echo $output;
        } else {
            return $output;
        }
    }

    // Add text Field
    public function add_text_field($field) { ?>
        <p><strong><?php echo $field['title']; ?></strong></p>
        <input id="<?php echo $field['id']; ?>_id" type="text" name="xr_options[<?php echo $field['id']; ?>]" value="<?php ( isset($this->get_option[$field['id']]) ? $this->get_option[$field['id']] : '' ); ?>"/>
    <?php
    }

    // Add Textarea Settings
    public function add_textarea_field($field) {
        echo '<p><strong>'. $field['title'] .'</strong></p>';
        echo '<textarea id="'. $field['id'] .'_id" type="text" name="xr_options['. $field['id'] .']" row="30" cols="60" >'. ( isset($this->get_option[$field['id']]) ? $this->get_option[$field['id']] : '') .'</textarea>';
    }

    // Add editor Field
    public function add_editor_field($field) {
        echo '<p><strong>'. $field['title'] .'</strong></p>';
        echo wp_editor( $this->get_option[$field['id']], $field['id'], array( 'textarea_rows' => '10', 'textarea_name' => 'xr_options['. $field['id'] .']' ) );
    }

    // Add select
    public function add_select_field($field) {
        echo '<p><strong>'. $field['title'] .'</strong></p>';
        echo '<select id="'. $field['id'] .'_id" name="xr_options['. $field['id'] .']">';
        foreach ($field['options'] as $opt_key => $opt_value) {
            echo '<option value="'.$opt_key.'" '. selected( $this->get_option[$field['id']], $opt_key ) .'>'. $opt_value .'</option>';
        }     
        echo '</select>';
    }

    // Add checkbox
    public function add_checkbox_field($field) {
        $output = '';
        ob_start();
        $output .= '<p><strong>' . $field['title'] . '</strong></p>';
        foreach ($field['options'] as $check_key => $check_value) {
            $output .= '<input type="checkbox" name="xr_options['. $field['id'] .'][]" value="'.$check_key.'" '. $this->checked( $this->get_option[ $field['id'] ], $check_key, false ) .'>'.$check_value.'</input><br/>';
        }
        ob_get_clean();
        echo $output;
    }

    // Add radio button
    public function add_radio_field($field) {
        $output = '';
        $output .= '<p><strong>' . $field['title'] . '</strong></p>';
        ob_start();
        foreach ($field['options'] as $radio_key => $radio_value) {
            $output .= '<input type="radio" name="xr_options['. $field['id'] .'][]" value="'.$radio_key.'" '. $this->checked( $this->get_option[ $field['id'] ], $radio_key, false ) .'>'.$radio_value.'</input><br/>';
        }
        ob_get_clean();
        echo $output;
    }

    // Add Image Select field
    public function add_image_select_field($field) {
        $output = '<div class="xr-image-select">';
        $output .= '<p><strong>' . $field['title'] . '</strong></p>';
        ob_start();
        foreach ($field['options'] as $radio_key => $radio_value) {
            $output .= '<span class="image-select-wrapper"><input type="radio" name="xr_options['. $field['id'] .'][]" value="'.$radio_key.'" '. $this->checked( $this->get_option[ $field['id'] ], $radio_key, false ) .'><img src="' . $radio_value . '" /></input></span>';
        }
        $output .= '</div>';
        ob_get_clean();
        echo $output;
    }

    // Add Color picker
    public function add_color_field($field) {
        echo '<p><strong>'. $field['title'] .'</strong></p>';
        echo '<input id="'. $field['id'] .'_id" type="text" name="xr_options['. $field['id'] .']" class="xr-color-field" value="'. ( isset($this->get_option[$field['id']]) ? $this->get_option[$field['id']] : '#000' ) .'"/>';
    }

    // Add Image upload field 
    public function add_upload_field($field) { 
        echo '<p><strong>'. $field['title'] .'</strong></p>';
        if ( !empty($this->get_option[ $field['id'] ]) ) {
            $image_url = $this->get_option[ $field['id'] ];
        } else {
            $image_url = '';
        } ?>
        <img class="xr-image-prev" src="<?php echo esc_url( $image_url ); ?>" height="100" width="100"/>
        <input class="xr-upload-field" type="text" name="xr_options[<?php echo $field['id']; ?>]" size="60" value="<?php echo esc_url( $image_url ); ?>">
        <a href="#" class="xr-upload-button button button-primary">Upload</a>
    <?php }

    // Add Icon picker
    public function add_icon_field($field) {
        $html = '<input class="regular-text" type="hidden" id="icon_picker_example_icon1" name="icon_picker_settings[icon1]" value=""/>';
        $html .= '<div id="preview_icon_picker_example_icon1" data-target="#icon_picker_example_icon1" class="button icon-picker"></div>';

        echo $html;
    }


    /**
    * Starts the Meta fields
    *
    */

    // Add Meta text Field
    public function add_meta_text_field($post, $field) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
            <input id="<?php echo $field['id']; ?>_id" type="text" name="xr_meta_options_<?php echo $field['id']; ?>" value="<?php echo get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true ); ?>" />
            <p class="meta-description"><?php if ( isset( $field['description'] ) ) echo $field['description']; ?></p>
        </div>
    
    <?php
    }

    // Add Meta TextArea Settings
    public function add_meta_textarea_field($post, $field) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
            <textarea id="<?php echo $field['id']; ?>-id" type="text" name="xr_meta_options_<?php echo $field['id']; ?>" row="30" cols="60" ><?php echo get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true ); ?></textarea>
            <p class="meta-description"><?php if ( isset( $field['description'] ) ) echo $field['description']; ?></p>
        </div>
    
    <?php
    }

    // Add Meta text Field
    public function add_meta_url_field($post, $field) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
            <input id="<?php echo $field['id']; ?>_id" type="text" name="xr_meta_options_<?php echo $field['id']; ?>" value="<?php echo esc_url( get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true ) ); ?>" />
            <p class="meta-description"><?php if ( isset( $field['description'] ) ) echo $field['description']; ?></p>
        </div>
    <?php 
    }


    // Add meta checkbox
    public function add_meta_checkbox_field($post, $field) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
            <?php    
            foreach ($field['options'] as $check_key => $check_value) {
                echo '<input type="checkbox" name="xr_meta_options_'. $field['id'] . '[]" value="' . $check_key . '" '. $this->checked( get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true ), $check_key, false ) .'>' . $check_value . '</input><br/>';
            }
            ?>
            <p class="meta-description"><?php if ( isset( $field['description'] ) ) echo $field['description']; ?></p>
        </div>
    <?php
    }

    // Add Meta radio button
    public function add_meta_radio_field($post, $field) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
            <input type="radio" name="xr_meta_options_<?php echo $field['id']; ?>" value="<?php echo $field['default']; ?>" checked>Default</input>
            <?php 
            foreach ($field['options'] as $radio_key => $radio_value) {
                echo '<input type="radio" name="xr_meta_options_'. $field['id'] .'" value="'. $radio_key .'" '. $this->checked( get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true ), $radio_key, false ) .'>'.$radio_value.'</input><br/>';
            } 
            ?>
            <p class="meta-description"><?php if ( isset( $field['description'] ) ) echo $field['description']; ?></p>
        </div>
    <?php    
    }


    // Add Image upload field 
    public function add_meta_upload_field($post, $field) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
                <?php 
                if ( !empty(get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true )) ) {
                    $image_url = get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true );
                } else {
                    $image_url = '';
                } ?>
            <img class="xr-image-prev" src="<?php echo esc_url( $image_url ); ?>"/>
            <input class="xr-upload-field" type="text" name="xr_meta_options_<?php echo $field['id']; ?>" size="60" value="<?php echo esc_url( $image_url ); ?>">
            <a href="#" class="xr-upload-button button button-primary">Upload</a>
        </div>
    <?php }

    // Add Meta Select field
    public function add_meta_select_field( $post, $field ) { ?>
        <div class="form-group">
            <p class="meta-title"><?php if ( isset( $field['title'] ) ) echo $field['title']; ?></p>
            <select id="<?php echo $field['id']; ?>'_id" name="xr_meta_options_<?php echo $field['id']; ?>">
                <option value="none">None</option>
                <?php
                foreach ($field['options'] as $opt_key => $opt_value) {
                    echo '<option value="'.$opt_key.'" '. selected( get_post_meta( $post->ID, '_xr_meta_key_' . $field['id'], true ), $opt_key ) .'>'. $opt_value .'</option>';
                } 
                ?>  
            </select>
            <p class="meta-description"><?php if ( isset( $field['description'] ) ) echo $field['description']; ?></p>
        </div> 
    <?php
    }

}
