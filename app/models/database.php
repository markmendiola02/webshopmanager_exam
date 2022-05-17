<?php

namespace App\Models;

use \App\Config\Database as dbConfig;

/**
 * The Database class
 * 
 * Contains operations for managing the database
 * 
 */
class Database {

    /**
     * The database connection
     * 
     * @var mysqli
     */
    protected $conn;

    /**
     * Prepare the database connection
     */
    function __construct() {
        try {
            $this->conn = new \mysqli( dbConfig::$host, dbConfig::$user, dbConfig::$pass, dbConfig::$name );

            if ( mysqli_connect_errno() ) {
                throw new \Exception( 'Unable to connect to the database. Please check configuration.' );
            }
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }
    
    /**
     * Create a table
     * 
     * @param string $name     The name of the table to create
     * @param array  $columns  Table columns associated with the table
     * 
     */
    public function create_table( $name, $columns ) {
        try {
            $query = 'CREATE TABLE IF NOT EXISTS ' . $name . ' (';
            $cols = array();
            $keys = array();

            if ( empty( $columns ) ) {
                throw new \Exception( 'No columns provided for table ' . $name . '.' );
                return;
            }

            foreach ( $columns as $column => $props ) {
                if ( ! isset( $props['type'] ) ) {
                    continue;
                }

                $type           = $props['type'];
                $length         = isset( $props['length'] ) ? ' (' . $props['length'] . ')' : '';
                $null           = isset( $props['null'] ) && ! $props['null'] ? ' NOT NULL' : '';
                $auto_increment = isset( $props['key'] ) && $props['key'] === 'primary' ? ' AUTO_INCREMENT' : '';

                if ( isset( $props['key'] ) ) {
                    if ( $props['key'] === 'foreign' ) {
                        $keys[$props['key']][] = array(
                            'column' => $column,
                            'reference' => $props['reference']
                        );
                    }
                    else {
                        $keys[$props['key']][] = $column;
                    }
                }

                $cols[] = $column . ' ' . $type . $length . $null . $auto_increment;
            }

            $query .= implode( ', ', $cols );

            if ( ! isset( $keys['primary'] ) || empty( $keys['primary'] ) ) {
                throw new \Exception( 'No primary key set for table ' . $name . '.' );
                return;
            }
            
            $query .= ', PRIMARY KEY (' . $keys['primary'][0] . ')';

            if ( isset( $keys['unique'] ) && ! empty( $keys['unique'] ) ) {
                foreach ( $keys['unique'] as $column ) {
                    $query .= ', UNIQUE (' . $column . ')';
                }
            }

            if ( isset( $keys['foreign'] ) && ! empty( $keys['foreign'] ) ) {
                foreach ( $keys['foreign'] as $column ) {
                    $query .= ', FOREIGN KEY (' . $column['column'] . ') REFERENCES ' . $column['reference'][0] . ' ( ' . $column['reference'][1] . ' )';
                }
            }

            $query .= ' )';

            $statement = $this->conn->prepare( $query );

            if ( ! $statement ) {
                throw new \Exception( 'A problem has occured while trying to prepare the query statement.' );
            }

            $statement->execute();
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }

    /**
     * Remove a table
     * 
     * @param string $name     The name of the table to drop
     * 
     */
    public function drop_table( $name ) {
        try {
            $query = 'DROP TABLE IF EXISTS ' . $name;
            $statement = $this->conn->prepare( $query );

            if ( ! $statement ) {
                throw new \Exception( 'A problem has occured while trying to prepare the query statement.' );
            }

            $statement->execute();
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }
    
    /**
     * Fetches data from the database
     * 
     * @param string $query   The query to execute
     * @param array  $params  Additional query parameters
     * 
     * @return array
     */
    public function select( $query = '', $params = array() ) {
        try {
            $statement = $this->conn->prepare( $query );

            if ( ! $statement ) {
                throw new \Exception( 'A problem has occured while trying to prepare the query statement.' );
            }

            if ( ! empty( $params ) ) {
                $statement->bind_param( $params[0], $params[1] );
            }

            $statement->execute();

            $result = $statement->get_result()->fetch_all( MYSQLI_ASSOC );
            $statement->close();
            
            return $result;
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    
    }
    
    /**
     * Insert data from the database
     * 
     * @param string $query   The query to execute
     * @param array  $params  Additional query parameters
     * 
     * @return array
     */
    public function insert( $table, $columns = array(), $values = array() ) {
        try {
            $query = 'INSERT INTO ' . $table;

            if ( ! empty( $columns ) ) {
                $query .= ' (' . implode( ', ', $columns ) . ') VALUES ( ' . str_replace( '? ', '?, ', trim( str_repeat( '? ', count( $columns ) ) ) ) . ' )';
            }

            $statement = $this->conn->prepare( $query );

            if ( ! $statement ) {
                throw new \Exception( 'A problem has occured while trying to prepare the query statement.' );
            }

            if ( ! empty( $values ) ) {
                $statement->bind_param( str_repeat( 's', count( $columns ) ), ...$values );
            }

            $statement->execute();

            return $statement->insert_id;
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }
    
    /**
     * Update data from the database
     * 
     * @param string $query   The query to execute
     * @param array  $params  Additional query parameters
     * 
     * @return array
     */
    public function update( $table, $set = array(), $where = array() ) {
        try {
            $query = 'UPDATE ' . $table;
            $sets = [];
            $conds = [];

            if ( ! empty( $set ) ) {
                $query .= ' SET ';

                foreach ( $set as $column => $value ) {
                    $sets[] = $column . ' = "' . $value . '"';
                }

                $query .= implode( ', ', $sets );
            }

            if ( ! empty( $where ) ) {
                $query .= ' WHERE ';

                foreach ( $where as $cond ) {
                    if ( ! isset( $cond['field'] ) || ! isset( $cond['operator'] ) ||  ! isset( $cond['value'] ) ) {
                        continue;
                    }

                    $conds[] = $cond['field'] . ' ' . $cond['operator'] . ' "' . $cond['value'] . '"';
                }

                $query .= implode( ' and ', $conds );
            }

            $statement = $this->conn->prepare( $query );

            if ( ! $statement ) {
                throw new \Exception( 'A problem has occured while trying to prepare the query statement.' );
            }

            return $statement->execute();
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }

    /**
     * Delete entries from the database
     * 
     * @param string $query   The query to execute
     * @param array  $params  Additional query parameters
     * 
     * @return array
     */
    public function delete( $table, $where ) {
        try {
            if ( empty( $where ) ) {
                return;
            }

            $query = 'DELETE FROM ' . $table . ' WHERE ';
            $conds = [];

            foreach ( $where as $cond ) {
                if ( ! isset( $cond['field'] ) || ! isset( $cond['operator'] ) ||  ! isset( $cond['value'] ) ) {
                    continue;
                }

                $conds[] = $cond['field'] . ' ' . $cond['operator'] . ' "' . $cond['value'] . '"';
            }

            $query .= implode( ' AND ', $conds );

            $statement = $this->conn->prepare( $query );

            if ( ! $statement ) {
                throw new \Exception( 'A problem has occured while trying to prepare the query statement.' );
            }

            return $statement->execute();
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage() );
        }
    }

}