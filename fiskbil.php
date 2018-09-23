<?php
    // Fixing å, ä, ö in the result
    // header('Content-Type: application/json');

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


    // Checking if id is set and is not NULL
    if(isset($_GET['id'])) {
        // Checking for numeric id value
        if(is_numeric($_GET["id"])){
            $idString = "Fisk.id = " . $_GET["id"];
        }
        else {
            echo "Fel! ID måste vara ett nummer.";
            exit;
        }
    }

    // Preparing SQL query
    $stmt = $dbh->prepare("
        SELECT *
        FROM Fisk, Färdigmat
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

    if($result == NULL){
        echo "Fel! Värde saknas.";
    }

    $result = json_encode($result);
    echo $result;
    echo "<br>";
?>