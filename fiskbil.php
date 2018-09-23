<?php
    // fixing å, ä, ö in the result
    // header('Content-Type: application/json');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $idString = "1";
    $sort = "";

    $dbh = new PDO("mysql:host=localhost; dbname=Fiskbil_labb3_php; charset=utf8", "Anders", "abc123");

    if(isset($_GET['sort'])){
        $sort = " ORDER BY " . $_GET['sort'];
        if(isset($_GET['DESC'])){
            $sort .= " DESC ";
        }
        else {
            $sort .= " ASC ";
        }
    }

    if(isset($_GET['id'])) {
        if(is_numeric($_GET["id"])){
            $idString = "Fisk.id = " . $_GET["id"];
        }
        else {
            echo "Fel! ID måste vara ett nummer.";
            exit;
        }
    }

    $stmt = $dbh->prepare("
        SELECT *
        FROM Fisk
        WHERE " . $idString .
        $sort
    
    );

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