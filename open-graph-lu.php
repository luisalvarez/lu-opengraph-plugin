<?php
/**
 * Plugin name: LU Open Graph
 * Plugin URI:  https://github.com/luisalvarez/open-graph-lu/
 * Description: Open Graph allows custom input open graph meta data to any content type.
 * Version:     1.0.0-beta
 * Author:      ChicoLogic
 * Author URI:  https://github.com/luisalvarez
 * License: Apache License, Version 2.0
 * License URI: http://www.apache.org/licenses/LICENSE-2.0.html
 */

namespace OpenGraphLu;	

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die();
}

if ( !class_exists( 'LU_OpenGraph' ) ) {
    class LU_OpenGraph {
        // Refers to a single instance of this class. 
        private static $instance = null;

        private $builders = array();

        /**
         * Initializes the plugin.
         */
        private function __construct()
        {  
            add_action( 'admin_enqueue_scripts', array($this,'includeScripts') );
            include_once 'includes/Utils/FaceBookTagBuilder.php';
            include_once 'includes/Utils/TwitterTagBuilder.php';
            $this->builders['facebook'] =  new Utils\FaceBookTagBuilder();
            $this->builders['twitter'] =  new Utils\TwitterTagBuilder();
            add_action( 'wp_head', array($this, 'insertMetaFields'));
        }
        /**
         * Insert metadata into head tag
         */
        public function insertMetaFields()
        {
            $metadata = array_merge($this->builders['facebook']->getMetadata(), $this->builders['twitter']->getMetadata());
            foreach ( $metadata as $key => $value ) {
                if ( empty( $key ) ) {
                    continue;
                }

                if ( stripos( $key, 'twitter:' ) === 0 ) {
                    printf(
                        '<meta name="%1$s" content="%2$s" />' . PHP_EOL,
                        esc_attr($key),
                        esc_attr($value)
                    );
                } else { // use "property" attribute for Open Graph
                    printf(
                        '<meta property="%1$s" content="%2$s" />' . PHP_EOL,
                        esc_attr($key),
                        esc_attr($value)
                    );
                }
            }
        }
        /**
         * Include scripts
         */
        public function includeScripts() {
            wp_enqueue_script( 'lu_opengraph_script', plugin_dir_url( __FILE__ ) .  '/js/upload_media.js', array('jquery'), null, false );
        }

        /**
         * Creates or returns an instance of this class.
         *
         * @return A single instance of this class.
         */
        public static function getInstance(): LU_OpenGraph 
        {
            if (!isset(self::$instance)) {
                self::$instance = new LU_OpenGraph();
            }
            return self::$instance;
        }
    }
    LU_OpenGraph::getInstance();
}

