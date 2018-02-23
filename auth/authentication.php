<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 19/02/2018
 * Time: 11:04
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../config/database.php";
require_once "../utilities/user.php";

// Verifico esistenza dei parametri passati in POST
if (!isset($_POST['user']) || !isset($_POST['pass'])) {
    echo json_encode(array("ErrorMessage"=>"Parametri di autenticazione non validi."));
    exit;
}

// istanzo gli oggetti relativi ai DB
$database = Database::getInstance()->getConnection();

// Verifico utenza
if (!autenticaUtente($database, $_POST['user'], $_POST['pass'])) {
    echo json_encode(array("Status"=>"KO"));
    exit;
}

echo json_encode(array("Status"=>"OK"));

