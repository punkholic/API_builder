<?php
require 'flight/Flight.php';

Flight::route('/api/1', function(){
    echo 'hello world!';
});

Flight::start();
