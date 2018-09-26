<?php
    // Fixing å, ä, ö in the result
    // header('Content-Type: application/json');

    
    // G-krav:
    // kolla sista if is null, funkar den?
    // Lista alla produkter - JA
    // Lista enskilda produkter - JA t ex id=1
    // Lista vissa produkter - JA med limit, t ex limit=3
    // Lista bara kategorier - NEJ
    // Sortera - JA t ex ?sort=pris&DESC
    // htaccess - JA t ex http://localhost:8888/_PHP/_Laborationer/Labb03/fiskbil/1
    //                    http://localhost:8888/_PHP/_Laborationer/Labb03/fiskbil/
    //                    http://localhost:8888/_PHP/_Laborationer/Labb03/fiskbil
    //                    http://localhost:8888/_PHP/_Laborationer/Labb03/fiskbil.php?fisksort=torsk

    // VG-krav:
    // POST data - JA
    // PUT data - NEJ
    // DELETE data - JA
    // API-nyckel i URI - NEJ

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

    // Delete data
    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $stmt = $dbh->prepare("
        DELETE FROM Fisk WHERE id =" . $_GET['id']);
        
        if ($stmt->execute()){
            echo "Post raderad";
        }
    }
    // Update data
    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        // $stmt = $dbh->prepare("
        // UPDATE Fisk SET fisksort=:fisksort, pris=:pris, fetthalt=:fetthalt, zon=:zon WHERE id =" . $_GET['id']);
        $stmt = $dbh->prepare("
        UPDATE Fisk SET :fisksort WHERE id =" . $_GET['id']);

        $stmt->bindParam(":fisksort", $_POST['fisksort']);
        // $stmt->bindParam(":pris", $_POST['pris']);
        // $stmt->bindParam(":fetthalt", $_POST['fetthalt']);
        // $stmt->bindParam(":zon", $_POST['zon']);
        
        if ($stmt->execute()){
            echo "Post uppdaterad";
        }
    }

    else {
        // Checking for numeric id value
        if(is_numeric($_GET["id"])){
            $idString = "Fisk.id = " . $_GET["id"];
        }
        
        // rewrite functionality, URI example: ?fisksort=torsk
        if (isset($_GET['fisksort'])){
            $idString = "fisksort LIKE '" . $_GET['fisksort'] . "%'";
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
            "Det finns ingen fisk som matchar din sökning. Försök igen." . $_GET['id'] . $_GET['title']
        ];
    }

    $result = json_encode($result);
    echo $result;
    echo "<br>";

?>