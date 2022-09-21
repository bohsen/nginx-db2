<?php
class DbConnection
{
    private const database = 'testdb';
    private const user = 'db2inst1';
    private const hostname = 'db2.rn.dk';
    private const password = 'ChangeMe!';
    private const port = 50000;
    private const conn_string_ibm_db2 =
        'DRIVER={IBM DB2 ODBC DRIVER};DATABASE=' .
        DbConnection::database .
        ';' .
        'HOSTNAME=' .
        DbConnection::hostname .
        ';PORT=' .
        DbConnection::port .
        ';PROTOCOL=TCPIP;UID=' .
        DbConnection::user .
        ';PWD=' .
        DbConnection::password .
        ';';

    function connect()
    {
        $conn = db2_connect($this::conn_string_ibm_db2, '', '');

        if ($conn) {
            echo 'Connection succeeded.';
        } else {
            echo '<p><u>Connection failed:</u></p>';
            echo '<p>Error: ' . db2_conn_error($this->$conn) . '<br />';
            echo db2_conn_errormsg($this->$conn) . '</p>';
        }
    }

    /**
     * Slå henvisning op i DB2 databae
     * @return Result
     */
    function lookup_referral($accessionnummer): Result
    {
        // Her skal der slås op imod DB2 databasen, som skal returnere RECEIVER_ID, RECEIVER_ID_TYPE, CCRECEIVER_ID, CCRECEIVER_ID_TYPE

        $accessionnummer = trim($accessionnummer);
        $accessionnummer = stripslashes($accessionnummer);
        $accessionnummer = htmlspecialchars($accessionnummer);

        // $Dbobj = new DbConnection();

        return new Result(
            '80010911',
            'sygehusafdelingsnummer',
            '123456',
            'ydernummer'
        );
    }

    function query($sql)
    {
        // TODO: Forespørg db2 database
        $stmt = db2_prepare(
            $connection,
            'select RECEIVER_ID,RECEIVER_ID_TYPE,CCRECEIVER_ID,CCRECEIVER_ID_TYPE from Referral where ACCESSION_NUMBER = ?'
        );
    }

    function get_receiver_id()
    {
        return $this->receiver_id;
    }
    function get_receiver_type()
    {
        return $this->receiver_type;
    }
    function get_cc_receiver_id()
    {
        return $this->cc_receiver_id;
    }
    function get_cc_receiver_type()
    {
        return $this->cc_receiver_type;
    }
}
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

/**
 * Klasse som repræsenterer resultatet fra vores query
 */
class Result
{
    // Properties
    private $receiver_id = '';
    private $receiver_type = '';

    private $cc_receiver_id = '';
    private $cc_receiver_type = '';

    function __construct(
        $receiver_id,
        $receiver_type,
        $cc_receiver_id,
        $cc_receiver_type
    ) {
        $this->receiver_id = $receiver_id;
        $this->receiver_type = $receiver_type;
        $this->cc_receiver_id = $cc_receiver_id;
        $this->cc_receiver_type = $cc_receiver_type;
    }

    function get_receiver_id()
    {
        return $this->receiver_id;
    }
    function get_receiver_type()
    {
        return $this->receiver_type;
    }
    function get_cc_receiver_id()
    {
        return $this->cc_receiver_id;
    }
    function get_cc_receiver_type()
    {
        return $this->cc_receiver_type;
    }
}
