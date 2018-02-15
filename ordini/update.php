<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 15/02/2018
 * Time: 11:12
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


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

// Verifico esistenza dei parametri passati in POST
if (!isset($_POST['idProdotto']) || !isset($_POST['idOrdine']) || !isset($_POST['quantita'])) {
    echo json_encode(array("ErrorMessage"=>"Parametri errati."));
    exit;
}

// Verifico la presenza dei dati obbligatori al fine di completare l'operazioni richiesta
if ($_POST['idProdotto'] == 0) {
    echo json_encode(array("ErrorMessage"=>"Identificativo del prodotto obbligatorio."));
    exit;
}

if ($_POST['idOrdine'] == 0) {
    echo json_encode(array("ErrorMessage"=>"Identificativo dell'ordine obbligatorio."));
    exit;
}

if ($_POST['quantita'] == 0) {
    echo json_encode(array("ErrorMessage"=>"Parametro quantità da ordinare."));
    exit;
}
$note = "";
if (isset($_POST['note'])) {
    $note = $_POST['note'];
}

// Istanzio oggetto
$ordine = new Ordini($database, $_POST['idOrdine'], $_SERVER['PHP_AUTH_USER'], $_POST['quantita'], $note, $_POST['idProdotto']);

// Eseguo update
if ($ordine->updateOrdineUtente()) {
    echo json_encode(array("Result" => "OK"));
} else {
    echo json_encode(array("Result" => "KO"));
}
