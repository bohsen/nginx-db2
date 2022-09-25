<?php
class DbConnection
{
  private const database = 'testdb';
  private const user = 'db2inst1';
  private const hostname = 'db2.rn.dk';
  private const password = 'ChangeMe!';
  private const port = 50000;
  private const table = 'ev.referral';
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
    db2_close($conn);
  }

  /**
   * Slå henvisning op i DB2 databae
   * @return Result or null if no results where found
   */
  function lookup_referral(string $accessionnummer): ?Result
  {
    // Her skal der slås op imod DB2 databasen, som skal returnere RECEIVER_ID, RECEIVER_ID_TYPE, CCRECEIVER_ID, CCRECEIVER_ID_TYPE

    $accessionnummer = trim($accessionnummer);
    $accessionnummer = stripslashes($accessionnummer);
    $accessionnummer = htmlspecialchars($accessionnummer);

    // Forbind til db2 database
    $connection = db2_connect($this::conn_string_ibm_db2, '', '');

    // Håndter forbindelsesfejl
    if (!$connection) {
      $this->state = db2_conn_error();
      var_dump($this->state);
    }
    // TODO: Forespørg db2 database
    $stmt = db2_prepare(
      $connection,
      "select RECEIVER_ID,RECEIVER_ID_TYPE,CCRECEIVER_ID,CCRECEIVER_ID_TYPE from ".DbConnection::table." table where ACCESSION_NUMBER = ?"
    );
    // Håndter SQL fejl
    if (!$stmt) {
      $this->state = db2_stmt_error();
      var_dump($this->state);
    }

    $success = db2_execute($stmt, array($accessionnummer));
    if (!$success) {
      $this->state = db2_stmt_errormsg($stmt);
      var_dump($this->state);
    }

    $result = null;
    if($row = db2_fetch_object($stmt)) {
      $result =  new Result(
        $row->RECEIVER_ID,
        $row->RECEIVER_ID_TYPE,
        $row->CCRECEIVER_ID,
        $row->CCRECEIVER_ID_TYPE
      );
    }

    db2_close($connection);
    
    return $result;
  }
}

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
