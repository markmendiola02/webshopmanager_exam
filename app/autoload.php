<?php

namespace App;

class Autoload {

    function __construct() {

    }

    private function files() {
        return [
            "config" => [
                "config-database",
                "config-app",
            ],
            "controllers" => [
                "SeedersController",
                "RouteController",
            ],
            "database" => [
                "schema",
                "migrations/books_table",
                "migrations/book_meta_table",
            ],
            "models" => [
                "database",
                "book",
            ],
            "routes" => [
                "api"
            ],
        ];
    }

    public function run( $files = array(), $group = '' ) {
        if ( is_array( $files ) && empty( $files ) ) {
            $files = $this->files();
        }

        foreach ( $files as $current_group => $file ) {
            if ( is_array( $file ) ) {
                $this->run( $file, $current_group );
            }
            else {
                if ( $group ) {
                    include_file( $file, $group );
                }
                else {
                    include_file( $file, $current_group );
                }
            }
        }
    }

}

function include_file( $file, $current_group = '' ) {
    $file_path = ROOT . 'app/' . $current_group . '/' . $file . '.php';

    if ( file_exists( $file_path ) ) {
        include $file_path;
    }
}