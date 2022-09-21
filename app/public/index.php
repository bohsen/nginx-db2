<?php
include 'edit_receivers.php';
require_once 'dbconnection.php';

echo "<p><b>Using ibm_db2 extension:</b></p>";

$Dbobj = new DbConnection();
$Dbobj->connect();

?>