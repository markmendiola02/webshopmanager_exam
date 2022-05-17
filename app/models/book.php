<?php

namespace App\Models;

use \App\Models\Database;

/**
 * The Book class
 * 
 */
class Book {

    /**
     * The database
     * 
     * @var Database
     */
    private $db;

    /**
     * The database table for Book
     * 
     * @var string
     */
    private $table = 'books';

    /**
     * The database table for Book metadata
     * 
     * @var string
     */
    private $meta_table = 'book_meta';

    /**
     * Instantiate the database
     */
    function __construct() {
        $this->db = new Database;
    }

    /**
     * Get the list of books
     * 
     * @return array
     */
    public function get_books( $where = array() ) {
        $statement = 'SELECT * FROM ' . $this->table;

        if ( ! empty( $where ) ) {
            $statement .= ' WHERE';
            foreach ( $where as $cond ) {
                if ( ! isset( $cond['field'] ) || ! isset( $cond['operator'] ) ||  ! isset( $cond['value'] ) ) {
                    continue;
                }

                $statement .= ' ' . $cond['field'] . ' ' . $cond['operator'] . ' ' . $cond['value'];
            }
        }

        return $this->db->select( $statement );
    }

    /**
     * Get a book by specific field
     * 
     * @return array
     */
    public function get_book_by( $field, $value ) {
        return $this->get_books( array(
            array(
                'field' => $field,
                'operator' => '=',
                'value' => $value,
            )
        ) );
    }

    /**
     * Get a book by ID
     * 
     * @return array
     */
    public function get_book( $id ) {
        return $this->get_book_by( 'book_id', $id );
    }

    /**
     * Add a book
     * 
     * @param array $args  The list of values
     */
    public function add_book( $args ) {
        $this->db->insert( $this->table, array_keys( $args ), array_values( $args ) );
    }

    /**
     * Update books
     * 
     * @param array $source  The file where data should be read
     */
    public function update_books( $source ) {
        $books = array();

        if ( ( $handle = fopen( $source, 'r' ) ) !== false ) {
            while ( ( $data = fgetcsv( $handle, 1000,  ',' ) ) !== false ) {
                $books[] = $data;
            }

            fclose( $handle );
        }

        if ( ! empty( $books ) ) {
            $columns = $books[0];
            unset( $books[0] );
            
            foreach ( $books as $index => $book ) {
                if ( ! $this->get_book_by( 'isbn13', $book[array_search( 'isbn13', $columns )] ) ) {
                    $this->add_book( $this->map_book_values( $columns, $book ) );
                }
                else {
                    $this->update_book( $this->map_book_values( $columns, $book ), array(
                        array(
                            'field' => 'isbn13',
                            'operator' => '=',
                            'value' => $book[array_search( 'isbn13', $columns )]
                        )
                    ) );
                }
            }
        }

        return $books;
    }

    public function map_book_values( $columns, $values ) {
        $output = array();

        foreach ( $values as $key => $value ) {
            $output[$columns[$key]] = $value;
        }

        return $output;
    }

    /**
     * Update a book
     * 
     * @param array $sets   The list of value updates
     * @param array $conds  Conditions to be met
     */
    public function update_book( $sets, $conds ) {
        $this->db->update( $this->table, $sets, $conds );
    }

    /**
     * Delete a book
     * 
     * @param array $sets   The list of value updates
     * @param array $conds  Conditions to be met
     */
    public function delete_book( $id ) {
        $this->db->delete( $this->table, array(
            array(
                'field' => 'book_id',
                'operator' => '=',
                'value' => $id
            )
        ) );
    }

}