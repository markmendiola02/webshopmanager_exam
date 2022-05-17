<?php

namespace App\Routes;

use \App\Config\App as appConfig;
use \App\Controllers\Route;
use \App\Controllers\Seeders;
use \App\Models\Book;

Route::get( 'api/books/list', function() {
    $book = new Book();

    header( 'Content-Type: application/json', 'HTTP/1.1 200 OK' );

    echo json_encode( $book->get_books() );
    exit();
} );

Route::get( 'api/books/get/{book_id}', function( $id ) {
    $book = new Book();

    header( 'Content-Type: application/json', 'HTTP/1.1 200 OK' );

    echo json_encode( $book->get_book( $id ) );
    exit();
} );

Route::get( 'api/books/update', function() {
    if ( appConfig::$run_feeds ) {
        $book = new Book();
        $book->update_books( Seeders::get( 'books%20-%20Sheet1.csv' ) );
    }

    exit();
} );