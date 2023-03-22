<?php
class DbConnection
{
    private const database = 'testdb';
    private const user = 'db2inst1';
    private const hostname = 'db2.rn.dk';
    private const password = 'ChangeMe1';
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
            echo '<p>Forbundet til DB2 database.</p>';

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
                ' where ACCESSION_NUMBER = ?'
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
        $stmt = db2_prepare($connection, 'update ' . DbConnection::table . '
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
     * Tæl antal henvisninger med bestemt afsender/kopimodtager
     * @param string $search_id Ydernummer eller anden type kode på rekvirent/kopimodtager
     * @return CountResult
     */
    function count(string $search_id): ?CountResult {
        // Fjern tegn der kan skyldes forsøg på sql injection
        $searchid = trim($search_id);
        $searchid = stripslashes($search_id);
        $searchid = htmlspecialchars($search_id);

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
            "SELECT * FROM (VALUES ('$searchid',(SELECT count(ACCESSION_NUMBER) FROM ev.referral 
            WHERE SENDER_ID = '$searchid' AND STATE_DEFINITION_ID NOT IN ('Completed','Deleted')),
            (SELECT count(ACCESSION_NUMBER) FROM ev.referral WHERE CCRECEIVER_ID = '$searchid' AND 
            STATE_DEFINITION_ID NOT IN ('Completed','Deleted')))) AS s(SEARCH_PARAMETER, 
            COUNT_SENDER_ID, COUNT_CCRECECEIVER_ID)"
        );
        // Håndter SQL fejl
        if (!$stmt) {
            $this->state = db2_stmt_error();
            var_dump($this->state);
        }

        $success = db2_execute($stmt);
        if (!$success) {
            $this->state = db2_stmt_errormsg($stmt);
            var_dump($this->state);
        }

        $result = null;
        if ($row = db2_fetch_object($stmt)) {
            $result = new CountResult(
                $row->SEARCH_PARAMETER,
                $row->COUNT_SENDER_ID,
                $row->COUNT_CCRECECEIVER_ID
            );
        }

        db2_close($connection);

        return $result;
    }

    function replace_code(string $old_code, string $new_code): ?string {
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
            "MERGE INTO ev.referral AS A
            USING
              ( SELECT ACCESSION_NUMBER
                FROM ev.referral
                WHERE (SENDER_ID = '$old_code' OR CCRECEIVER_ID ='$old_code') AND STATE_DEFINITION_ID NOT IN ('Completed','Deleted')
              ) AS B
              ON A.ACCESSION_NUMBER = B.ACCESSION_NUMBER
            WHEN MATCHED AND A.SENDER_ID = '$old_code'
            THEN
              UPDATE SET SENDER_ID = '$new_code'
            WHEN MATCHED AND A.CCRECEIVER_ID = '$old_code'
            THEN
              UPDATE SET CCRECEIVER_ID = '$new_code'"
        );
        // Håndter SQL fejl
        if (!$stmt) {
            $this->state = db2_stmt_error();
            var_dump($this->state);
        }

        $success = db2_execute($stmt);
        if (!$success) {
            $this->state = db2_stmt_errormsg($stmt);
            var_dump($this->state);
        }

        $result = null;
        if ($row = db2_fetch_object($stmt)) {
            $result = $row;
        }

        db2_close($connection);

        return $result;
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

/**
 * Klasse som repræsenterer resultatet fra vores query
 */
class CountResult
{
    // Properties
    private $search_id = '';
    private $sender_id_count = 0;
    private $ccreceiver_id_count = 0;

    function __construct(
        $search_id,
        $sender_id_count,
        $ccreceiver_id_count
    ) {
        $this->search_id = $search_id;
        $this->sender_id_count = $sender_id_count;
        $this->ccreceiver_id_count = $ccreceiver_id_count;
    }

    function get_search_id()
    {
        return $this->search_id;
    }
    function get_sender_id_count()
    {
        return $this->sender_id_count;
    }
    function get_ccreceiver_id_count()
    {
        return $this->ccreceiver_id_count;
    }
}