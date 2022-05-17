<?php

(function() {
    require_once( ROOT . 'app/autoload.php' );
    require_once( ROOT . 'app/install.php' );

    // Autoload instance
    $autoload = new \App\Autoload();
    
    // Run the autoloader
    $autoload->run();

    // Initiate installation
    $install = new \App\Install();

    // Run the installer
    $install->run();
})();