<?php

namespace App\Database;

use \App\Models\Database as dbModel;

class Schema {

    public static function create( $name, $blueprint ) {
        $db = new dbModel;
        
        if ( empty( $blueprint ) ) {
            return;
        }

        if ( ! $primary = array_filter( $blueprint, array( __CLASS__, 'get_primary' ) ) ) {
            return;
        }

        $db->create_table( $name, $blueprint );
    }

    public static function get_key( $item, $key ) {
        if ( isset( $item['key'] ) && $item['key'] === $key ) {
            return $item;
        }
    }

    public static function get_primary( $item ) {
        return self::get_key( $item, 'primary' );
    }

    public static function get_unique( $item ) {
        return self::get_key( $item, 'unique' );
    }

    public static function drop( $name ) {
        $db = new dbModel;

        $db->drop_table( $name );
    }

}