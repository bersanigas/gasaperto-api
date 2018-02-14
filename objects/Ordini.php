<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 14/02/2018
 * Time: 11:21
 */

class Ordini
{
    private $id;
    private $fornitore;
    private $gestore;
    private $data_apertura;
    private $msg_apertura;
    private $msg_chiusura;
    private $msg_consegna;
    private $stato;
    private $totale;
    private $spese_acc;
    private $nome_report;
    private $fuori_bilancio;
    private $data_chiusura;
    private $data_consegna;
    private $statoOrdine;

    private $conn;

    public function __construct($db, $id) {
        $this->conn = $db;
        $this->id = $id;
    }

    public function read() {
        $query = "SELECT * FROM ordini_gas WHERE stato <> 'evaso' ";

        // Verifico la presenza di filtri sull'id ordine
        if ($this->id > 0) {
            $query .= " AND id = '" . $this->id . "'";
        }

        // Eseguo query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

}