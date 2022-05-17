<?php

namespace App\Config;

use App\Config\App as appConfig;

class Database {

    /**
     * Database host name
     * 
     * @var string
     */
    public static $host = 'localhost';

    /**
     * Database username
     * 
     * @var string
     */
    public static $user = 'root';

    /**
     * Database password
     * 
     * @var string
     */
    public static $pass = '';

    /**
     * Database name
     * 
     * @var string
     */
    public static $name = 'wsm_exam';

    /**
     * Return the list of database tables
     * 
     * @return array
     */
    function tables() {
        return array(
            'Books_Table',
            'Book_Meta_Table',
        );
    }

}