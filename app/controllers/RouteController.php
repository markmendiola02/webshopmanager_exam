<?php

namespace App\Controllers;

/**
 * The Route controller class
 * 
 */
class Route {
    
    public static function current_route() {
        if ( ! isset( $_GET['p'] ) ) {
            return;
        }

        return parse_url( $_GET['p'], PHP_URL_PATH );
    }

    /**
     * Get the route
     */
    public static function get( $path, $callback ) {
        $route = self::current_route();

        if ( $route === $path ) {
            $callback();
        }
        else {
            if ( strpos( $path, '{' ) !== false && strpos( $path, '}' ) !== false ) {
                $vars = self::map_vars( $path, $route );

                if ( ! empty( $vars ) ) {
                    $callback( ...$vars );
                }
            }
        }
    }

    public static function map_vars( $path, $route ) {
        $path_fragments  = explode( '/', $path );
        $route_fragments = explode( '/', $route );
        $output = array();

        if ( ! empty( $path_fragments ) && ! empty( $route_fragments ) && count( $path_fragments ) === count( $route_fragments ) ) {
            foreach ( $path_fragments as $index => $fragment ) {
                $start = strpos( $fragment, '{' );
                $end   = strpos( $fragment, '}' );

                if ( $start !== false && $end !== false ) {
                    $output[] = $route_fragments[$index];
                }
            }
        }

        return $output;
    }
    
    
}