<?php
include 'edit_receivers.php';

echo "<pre>";
print_r(get_loaded_extensions());
echo "<pre/>";

$database = 'testdb';
$user = 'db2inst1';
$password = 'ChangeMe!';
$hostname = 'host.docker.internal';
$port = 50000;

# $conn_string = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;" "HOSTNAME=$hostname;PORT=$port;PROTOCOL=TCPIP;UID=$user;PWD=$password;";
#$conn = db2_connect($database, $user, $password);
$conn_string = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;" .
  "HOSTNAME=$hostname;PORT=$port;PROTOCOL=TCPIP;UID=$user;PWD=$password;";
$conn = db2_connect($conn_string, '', '');

if ($conn) {
    echo "Connection succeeded.\n";
    db2_close($conn);
}
else {
    echo "Connection failed.\n";
}

phpinfo();
?>