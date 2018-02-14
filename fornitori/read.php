<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 13/02/2018
 * Time: 15:45
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(array("ErrorMessage"=>"Utente non autorizzato"));
    exit;
}

//$_SERVER['PHP_AUTH_PW']
//$_SERVER['PHP_AUTH_USER']

include_once "../config/database.php";
include_once "../objects/fornitori.php";
require_once "../utilities/user.php";

// istanzo gli oggetti relativi ai DB
$database = Database::getInstance()->getConnection();

// Verifico utenza
if (!autenticaUtente($database, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(array("Message"=>"Utente non autorizzato o cessato."));
    exit;
}

// Istanzio l'oggetto relativo ai fornitori
$fornitori = new Fornitori($database);

$stmt = $fornitori->getFornitori();
$numFornitori = $stmt->rowCount();

// check if more than 0 record found
if($numFornitori>0){

    // products array
    $products_arr=array();
    $products_arr["records"]=array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $product_item= [
            "id" => $id,
            "ditta" => html_entity_decode($ditta),
            "categoria" => html_entity_decode($categoria)
        ];

        array_push($products_arr["records"], $product_item);
    }

    echo json_encode($products_arr);
}

else{
    echo json_encode(
        array("Message" => "Nessun produttore trovato.")
    );
}