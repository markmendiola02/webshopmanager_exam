<?php

namespace App\Database\Migrations;

use \App\Database\Schema;

class Books_Table {

    public static $table = 'books';

    public static function create() {
        Schema::create( self::$table, array(
            'book_id' => array(
                'type'   => 'int',
                'length' => 10,
                'key'    => 'primary',
            ),
            'author_first_name' => array(
                'type'   => 'varchar',
                'length' => 255,
                'null'    => true,
            ),
            'author_last_name' => array(
                'type'   => 'varchar',
                'length' => 255,
                'null'    => true,
            ),
            'nationality' => array(
                'type'   => 'varchar',
                'length' => 10,
                'null'    => true,
            ),
            'wikipedia' => array(
                'type'   => 'text',
                'null'    => true,
            ),
            'book' => array(
                'type'   => 'text',
                'null'    => true,
            ),
            'published_year' => array(
                'type'   => 'int',
                'length' => 4,
                'null'    => true,
            ),
            'isbn13' => array(
                'type'   => 'varchar',
                'length' => 13,
                'null'    => true,
                'key'    => 'unique',
            ),
        ) );
    }

    public static function drop() {
        Schema::drop( self::$table );
    }

}