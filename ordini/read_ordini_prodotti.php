<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 14/02/2018
 * Time: 16:36
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

// Verifica parametri obbligatori
if (!isset($_GET['idOrdine']) || $_GET['idOrdine'] == 0) {
    echo json_encode(array("ErrorMessage"=>"Identificativo dell'ordine obbligatorio."));
    exit;
}

// Istanzo oggetto ordini
$ordine = new Ordini($database, $_GET['idOrdine'], $_SERVER['PHP_AUTH_USER']);

// Ottengo la lista dei prodotti collegati all'ordine per utente
$stmt = $ordine->getProdottiPerOdine();
$numProdottiOrdine = $stmt->rowCount();

if ($numProdottiOrdine > 0) {
    $arrayProdotti = array();
    $arrayProdotti["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $product_item= [
            "prodotto" => $prodotto,
            "utente" => html_entity_decode($utente),
            "quantita" => $quantita,
            "note" => $note,
            "id" => $fornitore,
            "articolo" => html_entity_decode($articolo),
            "descrizione" => html_entity_decode($descrizione),
            "confezione" => html_entity_decode($confezione),
            "pezzi_cartone" => $pezzi_cartone,
            "prezzo" => $prezzo,
            "disponibile" => $disponibile

        ];

        array_push($arrayProdotti["records"], $product_item);
    }

    echo json_encode($arrayProdotti);
} else {
    echo json_encode(
        array("Message" => "Nessun prodotto collegato all'ordine " . $_GET['idOrdine'])
    );
}
