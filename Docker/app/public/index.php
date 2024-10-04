<?php 
    try {
        $dbh = new PDO('mysql:host=db;dbname=my_database', 'root', 'root');
        echo '<h4 style="color: green">Connected to database sucessfully.</h4>';
    }
    catch (PDOException $e) {
        echo '<h4 style="color: red">Fail to connect to database.</h4>';
    }
    
    phpinfo(); 
?>