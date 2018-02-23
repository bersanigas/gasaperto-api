<?php
/**
 * Created by PhpStorm.
 * User: BIASTE
 * Date: 14/02/2018
 * Time: 09:19
 */


function autenticaUtente($db, $user, $pass){
    $query = " SELECT * FROM utenti WHERE username = '" . $user . "' AND password = '" . $pass .
        "' AND (data_cessazione = '0000-00-00 00:00:00' or data_cessazione is null)";
    $stmt = $db->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() ==  0) {
        return 0;
    }

    $resultSet = $stmt->fetch();
    return $resultSet['privilegi'];
}