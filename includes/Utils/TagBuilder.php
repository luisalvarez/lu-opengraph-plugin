<?php

namespace OpenGraphLu\Utils;

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die();
}

if ( !class_exists( 'TagBuilder' ) ) {
    abstract class TagBuilder {
        /**
         * Default OpenGrpah prefix
         */
        protected $prefix = 'og';
        /**
         * Tag Fields
         */
        protected $fields = array(
            'title',
            'image',
            'description',
        );
        /**
         * Metada dictionary
         */
        protected $metadata = array();
        /**
         * Field prefix indicator
         */
        protected $fieldPrefix = '';
        /**
         * Default post type to add
         */
        protected $postTypes = array( 'page', 'post');
        /**
         * Get values
         */
        public function getDefaultValues() : void
        {
            add_filter(constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[0], array($this,'getTitle'));
            add_filter(constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[2], array($this,'getDescription'));
            add_filter(constants::PLUGIN_PREFIX . $this->fieldPrefix. $this->fields[1], array($this,'getImage'));
        }

        public function getDescription($description) : string
        {
            if (!empty($description)) {
                return $description;
            }
            $id = get_queried_object_id();
            $value = get_post_meta( $id , constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[2], true );
            return $value;
        }

        public function getTitle($title) : string
        {
            if (!empty($title)) {
                return $title;
            }
            $id = get_queried_object_id();
            $value = get_post_meta( $id , constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[0], true );
            return $value;
        }

        public function getImage($image) :string
        {
            if (!empty($image)) {
                return $image;
            }
            $id = get_queried_object_id();
            $value = get_post_meta( $id , constants::PLUGIN_PREFIX . $this->fieldPrefix . $this->fields[1], true );
            if (empty($value)) {
                $value = get_the_post_thumbnail_url( $id ,'full');
            }
            return $value;
        }
        
        /**
         * The actual factory method.
         * This lets subclasses return any concrete field without breaking the
         * superclass' contract.
         */
        abstract public function getMetadata(): array;
    }
}
