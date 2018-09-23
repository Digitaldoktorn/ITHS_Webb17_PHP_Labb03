<?php
    // Fixing å, ä, ö in the result
    // header('Content-Type: application/json');
    // lyckas ej med htacess rewrite-kolla med Hans

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $idString = "1";
    $sort = "";
    $limit = "";

    // Connecting to database
    $dbh = new PDO("mysql:host=localhost; dbname=Fiskbil_labb3_php; charset=utf8", "Anders", "abc123");

    // Ascending and descending ordering
    if(isset($_GET['sort'])){
        $sort = " ORDER BY " . $_GET['sort'];
        if(isset($_GET['DESC'])){
            $sort .= " DESC ";
        }
        else {
            $sort .= " ASC ";
        }
    }

    // Limiting result
    if((isset($_GET['limit'])) && (is_numeric($_GET['limit']))){
        $limit = " LIMIT " . $_GET['limit'];
    }

    // Post data
    if(isset($_POST['fisksort'])) {
        $stmt = $dbh->prepare("
            INSERT INTO Fisk
            (fisksort, pris, fetthalt, zon)
            VALUES
            (:fisksort, :pris, :fetthalt, :zon)
        ");

        $stmt->bindParam(":fisksort", $_POST['fisksort']);
        $stmt->bindParam(":pris", $_POST['pris']);
        $stmt->bindParam(":fetthalt", $_POST['fetthalt']);
        $stmt->bindParam(":zon", $_POST['zon']);

        if($stmt->execute()){
            $result = $dbh->lastInsertId();
        }
        else {
            $result = $stmt->errorInfo();
        }
        $result = json_encode($result);
        echo $result;

    }
    else {
        // Checking for numeric id value
        if(is_numeric($_GET["id"])){
            $idString = "Fisk.id = " . $_GET["id"];
        }


        // Preparing SQL query
        $stmt = $dbh->prepare("
            SELECT *
            FROM Fisk
            WHERE " . $idString .
            $sort . 
            $limit
        
        );

        // Fetching the result into associative array
        if($stmt->execute()){
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } 
        else {
            $result = $stmt->errorInfo();
            var_dump($stmt);
        }

    }
    if(is_null($result)){
        $result = [
            "DEt finns ingen fisk som matchar din sökning. Försök igen." . $_GET['id'] . $_GET['title']
        ];
    }

    $result = json_encode($result);
    echo $result;
    echo "<br>";

?>