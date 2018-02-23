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
    private $user;
    private $quantita;
    private $note;
    private $idProdotto;

    private $conn;
    private $prefixTabella = "prodotti_ordine_";
    private $nomeTabellaOrdini;

    public function __construct($db, $id, $user = null, $quantita = 0, $note = null, $idProdotto = 0) {
        $this->conn = $db;
        $this->id = $id;
        $this->user = $user;
        $this->quantita = $quantita;
        $this->note = $note;
        $this->idProdotto = $idProdotto;
        $this->nomeTabellaOrdini = $this->prefixTabella . $id;
    }

    /** Metodo che estrae tutti gli ordini che non sono stati ancora evasi
     * @return mixed
     */
    public function read() {
        $query =    "SELECT " .
                    " o.id, o.fornitore, o.stato, f.ditta, " .
                    " u.cognome as cognomeGestore, u.nome as nomeGestore, u.email as emailGestore, " .
                    " date(o.data_apertura) as dataApertura, o.msg_apertura, o.msg_chiusura, o.msg_consegna, " .
                    " date(o.data_chiusura) as dataChiusura, date(o.data_consegna) as dataConsegna, o.totale, o.spese_acc, o.fuori_bilancio " .
                    " FROM ordini_gas as o " .
                    " join fornitori as f on o.fornitore = f.id " .
                    " join utenti as u on o.gestore = u.username " .
                    " WHERE o.stato <> 'evaso' ";

        // Verifico la presenza di filtri sull'id ordine
        if ($this->id > 0) {
            $query .= " AND o.id = '" . $this->id . "'";
        }

        $query .= " ORDER BY o.stato, o.data_chiusura ";

        // Eseguo query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /** Metodo che estrae i prodotti associati all'ordine di un determinato utente
     * @return mixed
     */
    public function getProdottiPerOdine() {

        $query = "SELECT * FROM " . $this->nomeTabellaOrdini. " as uo " .
                 "JOIN prodotti as p on uo.prodotto = p.id " .
                 "WHERE uo.utente = '" . $this->user . "' AND disponibile = 'si' ORDER BY p.descrizione";

        // Eseguo query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /** Aggiorna quantitativo ordinato per l'utente
     * @return bool
     */
    public function updateOrdineUtente(){
        $query = "UPDATE " . $this->nomeTabellaOrdini . " SET " .
                 "quantita = " . $this->quantita . ", " .
                 "note = '" . $this->note . "' " .
                 "WHERE utente = '" . $this->user . "' AND prodotto = " . $this->idProdotto;
        $stmt = $this->conn->prepare($query);

        if($stmt->execute()) {
            return true;
        }

        return $stmt->errorInfo();
    }

    public function getContoOrdine($idOrdine) {
        $tab = $this->prefixTabella . $idOrdine;

        $query = "SELECT uo.quantita, p.prezzo FROM " . $tab. " as uo " .
            "JOIN prodotti as p on uo.prodotto = p.id " .
            "WHERE uo.utente = '" . $this->user . "' AND disponibile = 'si' ORDER BY p.descrizione";

        // Eseguo query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll();

        $saldo = 0;
        foreach($data as $row) {
            if ($row['quantita'] > 0) {
                $prezzoProd = $row['quantita'] * $row['prezzo'];
                $saldo = $saldo + $prezzoProd;
            }
        }

        return $saldo;
    }

}