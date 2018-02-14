<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 13/02/2018
 * Time: 14:57
 */

class Fornitori
{
    private $conn;
    private $table_name = "fornitori";

    private $id;
    private $ditta;
    private $categoria;
    private $partita_iva;
    private $via;
    private $cap;
    private $citta;
    private $contatto;
    private $telefono;
    private $cellulare;
    private $fax;
    private $skype;
    private $email;
    private $url_sito;
    private $certificato;
    private $note;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Metodo che estrae la lista dei fornitori
     */
    public function getFornitori() {
        $query = " SELECT * FROM " . $this->table_name . " ORDER BY ditta";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}