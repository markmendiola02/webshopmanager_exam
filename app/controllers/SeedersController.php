<?php

namespace App\Controllers;

use App\Config\App as appConfig;

/**
 * The Seeders controller class
 * 
 */
class Seeders {

    /**
     * Get the seeder
     */
    public static function get( $filename ) {
        return appConfig::$base_url . '/app/database/seeders/' . $filename;
    }
    
    
}