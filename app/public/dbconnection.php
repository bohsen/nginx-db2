<?php
class DbConnection
{

    /*
  // Håndter forbindelsesfejl
  $connection = db2_connect($database, $user, $password);
  if (!$connection) {
    $this->state = db2_conn_error();
    return false;
  }
  
  // Håndter SQL fejl
  $stmt = db2_prepare($connection, "select RECEIVER_ID,RECEIVER_ID_TYPE,CCRECEIVER_ID,CCRECEIVER_ID_TYPE from ev.referral where ACCESSION_NUMBER = ?");
  if (!$stmt) {
    $this->state = db2_stmt_error();
    return false;
  }
  
  // Håndter query fejl
  $success = db2_execute($stmt, $accessionnummer);
  if (!$success) {
    $this->state = db2_stmt_error($stmt);
    return $false;
  }
  */
    function connect()
    {
        // TODO: Forbind til db2 database
    }

    function query($sql) {
        // TODO: Forespørg db2 database
    }

    function close() {
        // TODO: Luk forbindelse til db2 database
    }
}
