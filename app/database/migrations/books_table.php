<?php

namespace App\Database\Migrations;

use \App\Database\Schema;

class Book_Meta_Table {

    public static $table = 'book_meta';

    public static function create() {
        Schema::create( self::$table, array(
            'book_meta_id' => array(
                'type'   => 'int',
                'length' => 10,
                'key'    => 'primary',
            ),
            'meta_key' => array(
                'type'   => 'varchar',
                'length' => 255,
                'null'    => true,
            ),
            'meta_value' => array(
                'type'   => 'longtext',
                'null'    => true,
            ),
            'book_id' => array(
                'type'   => 'int',
                'length' => 10,
                'key'    => 'foreign',
                'reference' => array( 'books', 'book_id' )
            ),
        ) );
    }

    public static function drop() {
        Schema::drop( self::$table );
    }

}