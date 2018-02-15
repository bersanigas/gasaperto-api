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
    private $user;

    private $conn;

    public function __construct($db, $id, $user) {
        $this->conn = $db;
        $this->id = $id;
        $this->user = $user;
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

    public function getProdottiPerOdine() {
        $nomeTabella = "prodotti_ordine_" . $this->id;
        $query = "SELECT * FROM " . $nomeTabella . " as uo " .
                 "JOIN prodotti as p on uo.prodotto = p.id " .
                 "WHERE uo.utente = '" . $this->user . "' AND disponibile = 'si' ORDER BY p.descrizione";

        // Eseguo query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

}