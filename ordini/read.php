<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 14/02/2018
 * Time: 12:05
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
include_once "../objects/ordini.php";
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

// Verifico se è presente qualche filtro
$idOrdine = isset($_GET['id']) ? $_GET['id'] : 0;

// Istanza dell'oggetto ordini
$ordini = new Ordini($database, $idOrdine);

$stmt = $ordini->read();
$numOrdini = $stmt->rowCount();

// check if more than 0 record found
if($numOrdini>0){

    // products array
    $products_arr=array();
    $products_arr["records"]=array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $product_item= [
            "id" => $id,
            "fornitore" => html_entity_decode($fornitore),
            "gestore" => html_entity_decode($gestore),
            "data_apertura" => $data_apertura,
            "msg_apertura" => $msg_apertura,
            "msg_chiusura" => $msg_chiusura,
            "msg_consegna" => $msg_consegna,
            "stato" => $stato,
            "totale" => $totale,
            "spese_acc" => $spese_acc,
            "fuori_bilancio" => $fuori_bilancio,
            "data_chiusura" => $data_chiusura,
            "data_consegna" => $data_consegna
        ];

        array_push($products_arr["records"], $product_item);
    }

    echo json_encode($products_arr);
}

else{
    echo json_encode(
        array("Message" => "Nessun ordine trovato.")
    );
}
