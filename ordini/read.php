<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 14/02/2018
 * Time: 12:05
 * read() -> Metodo che estrae la lista degli ordini non 'evasi', con la possibilità di filtrare per
 * singolo ordine
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic');
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(array("ErrorMessage"=>"Utente non autorizzato"));
    exit;
}

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
$ordini = new Ordini($database, $idOrdine, $_SERVER['PHP_AUTH_USER']);

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
            "stato" => $stato,
            "ditta" => $ditta,
            "cognomeGestore" => $cognomeGestore,
            "nomeGestore" => $nomeGestore,
            "emailGestore" => $emailGestore,
            "dataApertura" => $dataApertura,
            "msg_apertura" => $msg_apertura,
            "msg_chiusura" => $msg_chiusura,
            "msg_consegna" => $msg_consegna,
            "dataChiusura" => $dataChiusura,
            "dataConsegna" => $dataConsegna,
            "totale" => $totale,
            "spese_acc" => $spese_acc,
            "fuori_bilancio" => $fuori_bilancio,
            "saldoUtenteOrdine" => $ordini->getContoOrdine($id)
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
