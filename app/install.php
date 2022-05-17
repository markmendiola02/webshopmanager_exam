<?php

namespace App;

use App\Config\App as appConfig;
use App\Config\Database as dbConfig;

/**
 * The Seeders controller class
 * 
 */
class Install {

    /**
     * Get the seeder
     */
    public static function run() {
        if ( appConfig::$create_tables ) {
            foreach ( dbConfig::tables() as $table ) {
                $namespace = '\App\Database\Migrations\\';
                
                if ( class_exists( $namespace . $table ) ) {
                    call_user_func( $namespace . $table . '::create' );
                }
            }
        }
    }
    
    
}