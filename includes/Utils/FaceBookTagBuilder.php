<?php

namespace OpenGraphLu\Utils;

include_once 'constant.php';
include_once 'TagBuilder.php';

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die();
}

if ( !class_exists( 'FaceBookTagBuilder' ) ) {
    class FaceBookTagBuilder extends TagBuilder {
        public function __construct($prefix = 'og', $fieldPrefix = 'fb_') 
        {
            $this->prefix = $prefix;
            $this->fieldPrefix = $fieldPrefix;
            add_action( 'wp', array($this, 'getDefaultValues') );
            add_action( 'admin_menu', array( $this, 'createCustomFields' ) );
            add_action( 'save_post', array( $this, 'saveCustomFields' ), 1, 2 );
        }
        /**
         * Configure new custom fields
         */
        public function createCustomFields(): void 
        {
            $args = array(
                'public'   => true,
                '_builtin' => false
            );
            $output = 'names'; // 'names' or 'objects' (default: 'names')
            $operator = 'and'; // 'and' or 'or' (default: 'and')
            $postTypes = get_post_types( $args, $output, $operator );
            $this->postTypes = array_merge($this->postTypes,$postTypes);
            if ( function_exists( 'add_meta_box' ) ) {
                foreach ( $this->postTypes as $postType ) {
                    add_meta_box( constants::PLUGIN_PREFIX . $this->fieldPrefix .'fields', 'Facebook Tags', array( $this, 'displayFields' ), $postType, 'normal', 'high' );
                }
            }
        }

        /**
         * Display new custom fields
         */
        public function displayFields(): void
        {
            global $post;
            ?>
            <div class="form-wrap">
            <?php
                wp_nonce_field( constants::PLUGIN_PREFIX . $this->fieldPrefix . 'fields', constants::PLUGIN_PREFIX . $this->fieldPrefix .'fields_wpnonce', false, true );
            ?>
                <div class="form-field form-required">
                    <label for="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[0]; ?>"><?php _e('Title')?></label>
                    <input id="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix. $this->fields[0]; ?>" type="text" name="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[0]; ?>"  value="<?php echo htmlspecialchars( get_post_meta( $post->ID, constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[0], true ) ); ?>"/>
                </div>
                <div class="form-field form-required">
                    <label for="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[2]; ?>"><?php _e('Description')?></label>
                    <textarea name="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix. $this->fields[2]; ?>" id="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix. $this->fields[2]; ?>" columns="30" rows="3"><?php echo htmlspecialchars( get_post_meta( $post->ID, constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[2], true ) ); ?></textarea>
                </div>
                <div class="form-field">
                    <a href="#" class="lu_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></td>
                    <input type="text" name="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[1]; ?>" id="<?php echo constants::PLUGIN_PREFIX . $this->fieldPrefix. $this->fields[1]; ?>" value="<?php echo get_post_meta($post->ID, constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[1], true); ?>" style="width:100%;" />
                </div>
            </div>
            <?php
        }

        /**
         * Save custom fields data
         */
        public function saveCustomFields($post_id, $post): void 
        {
            if ( !isset( $_POST[ constants::PLUGIN_PREFIX . $this->fieldPrefix . 'fields_wpnonce'] ) || !wp_verify_nonce( $_POST[ constants::PLUGIN_PREFIX . $this->fieldPrefix . 'fields_wpnonce' ], constants::PLUGIN_PREFIX . $this->fieldPrefix . 'fields' ) )
                return;
            if ( !current_user_can( 'edit_post', $post_id ) )
                return;
            
            foreach ( $this->fields as $field) {
                if ( isset( $_POST[ constants::PLUGIN_PREFIX . $this->fieldPrefix . $field] ) && trim( $_POST[ constants::PLUGIN_PREFIX . $this->fieldPrefix .  $field ] ) ) {
                    $value = $_POST[ constants::PLUGIN_PREFIX . $this->fieldPrefix . $field];
                    update_post_meta( $post_id, constants::PLUGIN_PREFIX . $this->fieldPrefix . $field, $value );
                } else {
                    delete_post_meta( $post_id, constants::PLUGIN_PREFIX . $this->fieldPrefix . $field);
                }
            }
        }
        
        /**
         * Get fb metadata
         */
        public function getMetadata(): array 
        {
            foreach ( $this->fields as $field) {
                $filter = constants::PLUGIN_PREFIX . $this->fieldPrefix . $field;
                $this->metadata[ "$this->prefix:$field" ] = apply_filters( $filter, '' );
            }
            return $this->metadata;
        }
    }

}