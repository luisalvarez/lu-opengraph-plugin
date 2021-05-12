<?php
namespace OpenGraphLu\Utils;

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die();
}

if ( !class_exists( 'constants' ) ) {
    class constants {
        const PLUGIN_PREFIX = 'lu_opengraph_';
    }
}