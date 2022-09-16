<?php
include 'edit_receivers.php';

# echo "<pre>";
# print_r(get_loaded_extensions());
# echo "<pre/>";

$database = 'testdb';
$user = 'db2inst1';
$password = 'ChangeMe!';
$hostname = 'db2.rn.dk';
$port = 50000;

$conn_string =
    "DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;" .
    "HOSTNAME=$hostname;PORT=$port;PROTOCOL=TCPIP;UID=$user;PWD=$password;";
$conn = db2_connect($conn_string, '', '');
if ($conn) {
    echo "Connection succeeded.\n";
    db2_close($conn);
} else {
    echo "<p><b>Using ibm_db2 extension:</b></p>";
    echo "<p><u>Connection failed:</u></p>";
    echo "<p>Error: ".db2_conn_error()."<br />";
    echo db2_conn_errormsg()."</p>";
}


try {
    $connection = new PDO('ibm:db2.rn.dk', 'db2inst1', 'ChangeMe!', [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    echo "<p><b>Using pdo_db2 extension:</b></p>";
    echo "<p><u>Connection failed:</u></p>";
    echo "<p>Code: ".$e->getCode()." Error: ".$e->getMessage()."<br />";
    echo "<p>Error: ".$e->getTraceAsString()."</p>";
}

phpinfo();
?>
