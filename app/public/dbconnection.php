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
            echo 'Forbundet til DB2 database.';

        } else {
            echo '<p><u>Forbindelse fejlet:</u></p>';
            echo '<p>Fejl: ' . db2_conn_error($this->$conn) . '<br />';
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
        // Fjern tegn der kan skyldes forsøg på sql injection
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
        // Forespørg db2 database
        $stmt = db2_prepare(
            $connection,
            'select SENDER_ID,SENDER_ID_TYPE,CCRECEIVER_ID,CCRECEIVER_ID_TYPE from ' .
                DbConnection::table .
                ' table where ACCESSION_NUMBER = ?'
        );
        // Håndter SQL fejl
        if (!$stmt) {
            $this->state = db2_stmt_error();
            var_dump($this->state);
        }

        $success = db2_execute($stmt, [$accessionnummer]);
        if (!$success) {
            $this->state = db2_stmt_errormsg($stmt);
            var_dump($this->state);
        }

        $result = null;
        if ($row = db2_fetch_object($stmt)) {
            $result = new Result(
                $row->SENDER_ID,
                $row->SENDER_ID_TYPE,
                $row->CCRECEIVER_ID,
                $row->CCRECEIVER_ID_TYPE
            );
        }

        db2_close($connection);

        return $result;
    }

    function save_referral(
        string $accessionnummer,
        string $receiver,
        string $receiver_type,
        ?string $cc_receiver,
        ?string $cc_receiver_type
    ) {
        // Forbind til db2 database
        $connection = db2_connect($this::conn_string_ibm_db2, '', '');

     // Håndter forbindelsesfejl - vi dumper blot fejlen ud i brugerfladen
     if (!$connection) {
         $this->state = db2_conn_error();
         var_dump($this->state);
     }
     // Forbered sql-forespørgsel
     $stmt = db2_prepare(
         $connection,
         'update ' . DbConnection::table . '
         set SENDER_ID = ?, SENDER_ID_TYPE = ?, CCRECEIVER_ID = ?, CCRECEIVER_ID_TYPE = ? where ACCESSION_NUMBER = ?'
     );
     // Håndter statement fejl - vi dumper blot fejlen ud i brugerfladen
     if (!$stmt) {
         $this->state = db2_stmt_error();
         var_dump($this->state);
     }

        $clean_input = $this->evaluate_input([
            $receiver,
            $receiver_type,
            $cc_receiver,
            $cc_receiver_type,
            $accessionnummer,
        ]);

        $success = db2_execute($stmt, $clean_input);
        // Håndter SQL fejl - vi dumper blot fejlen ud i brugerfladen
        if (!$success) {
            $this->state = db2_stmt_errormsg($stmt);
            var_dump($this->state);
        }

        db2_close($connection);
        return $success;
    }

    /**
     * Trims, strips slashes and removes html-specialchars
     * @return String array with modified content
     */
    private function evaluate_input(array $input): array
    {
        $callback = function ($input) {
            $element = trim($input);
            $element = stripslashes($input);
            $element = htmlspecialchars($input);
            return $element;
        };
        return array_map($callback, $input);
    }
}

/**
 * Klasse som repræsenterer resultatet fra vores query
 */
class Result
{
    // Properties
    private $sender_id = '';
    private $sender_type = '';

    private $cc_receiver_id = '';
    private $cc_receiver_type = '';

    function __construct(
        $sender_id,
        $sender_type,
        $cc_receiver_id,
        $cc_receiver_type
    ) {
        $this->sender_id = $sender_id;
        $this->sender_type = $sender_type;
        $this->cc_receiver_id = $cc_receiver_id;
        $this->cc_receiver_type = $cc_receiver_type;
    }

    function get_sender_id()
    {
        return $this->sender_id;
    }
    function get_sender_type()
    {
        return $this->sender_type;
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
