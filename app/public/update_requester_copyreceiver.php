<!DOCTYPE HTML>
<html>

<head>
  <style>
    .error {
      color: #FF0000;
    }
    .result {
      color: #38761D;
    }
  </style>
</head>
<?php
include 'dbconnection.php';
?>

<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

    <fieldset>
      <legend>Udskift en udgået rekvirent/kopimodtager</legend>

      <label for="t1">Udgået kode:</label>

      <input type="text" name="inactive_code" id="t1" value="<?php echo (empty($_POST["inactive_code"]) || isset($_POST["clear"])) ? '' : $_POST["inactive_code"] . '" readonly=true'; ?>" />
      <input type="submit" name="search" value="Søg" class="button" />
      <input type="submit" name="clear" value="Ryd" class="button" />

    </fieldset>
  </form>

  <?php

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    if (empty($_POST["inactive_code"])) {
      echo '<span class="error">* Der er ikke indtastet en kode</span>';
    } else {
      $connection = new DbConnection();
      $resultat = $connection->count($_POST["inactive_code"]);

      $search_id = $resultat->get_search_id();
      $sender_id_count = $resultat->get_sender_id_count();
      $ccreceiver_count = $resultat->get_ccreceiver_id_count();
      if(isset($resultat)) {
        print <<<END
        <fieldset>
          <legend>Søgningen fandt</legend>
          $sender_id_count aktive henvisninger med $search_id som afsender
          <br />
          $ccreceiver_count aktive henvisninger med $search_id som kopimodtager
        </fieldset>
        END;
      }
      if($sender_id_count > 0 && $ccreceiver_count > 0) {
        ?>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="old_id" value="<?php echo $search_id ?>">
        <br />
        <label for="t5">Ny værdi:</label>
        <input type="text" name="new_id" id="t5" />
        <input type="submit" name="update_id" value="Udskift" class="button" />
        <input type="submit" name="clear" value="Annuller" class="button" />
      </form>
        <?php
      }

      unset($connection);
    }
  } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_id"])) {
    $old_id =  $_POST["old_id"];
    $new_id = $_POST["new_id"];

    $error = FALSE;
    // Print fejl hvis udgået eller ny kode ikke er udfyldt
    if(empty($old_id) || empty($new_id)) {
      echo '<span class="error">* Ét af felterne mangler at blive udfyldt</span>';
      $error = TRUE;
    }
    
    if(!$error) {
      echo 'Saving changes: ' . $old_id .' to ' . $new_id;
    }
  }
  ?>
</body>
</html>