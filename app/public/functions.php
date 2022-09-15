<?php
require_once('dbconnection.php');

/**
 * Klasse som repræsenterer resultatet fra vores query
 */
class Result
{
  // Properties
  private $receiver_id = "";
  private $receiver_type = "";

  private $cc_receiver_id = "";
  private $cc_receiver_type = "";

  function __construct($receiver_id, $receiver_type, $cc_receiver_id, $cc_receiver_type)
  {
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

/**
 * Slå henvisning op i DB2 databae
 * @return Result
 */
function lookup_referral($accessionnummer) : Result
{
  // Her skal der slås op imod DB2 databasen, som skal returnere RECEIVER_ID, RECEIVER_ID_TYPE, CCRECEIVER_ID, CCRECEIVER_ID_TYPE

  $accessionnummer = trim($accessionnummer);
  $accessionnummer = stripslashes($accessionnummer);
  $accessionnummer = htmlspecialchars($accessionnummer);

  // $Dbobj = new DbConnection();

  return new Result("80010911", "sygehusafdelingsnummer", "123456", "ydernummer");

}
