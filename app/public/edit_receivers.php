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

$receiver_type_array = array('0' => 'sygehusafdelingsnummer', '1' => 'ydernummer', '2' => 'lokationsnummer', '3' => 'sorkode');
$ccreceiver_type_array = array('0' => '', '1' => 'sygehusafdelingsnummer', '2' => 'ydernummer', '3' => 'lokationsnummer', '4' => 'sorkode');
?>

<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

    <fieldset>
      <legend>Fremsøg henvisningsoplysninger</legend>

      <label for="t1">Accessionnummer:</label>

      <input type="text" name="accessionnummer" id="t1" value="<?php echo (empty($_POST["accessionnummer"]) || isset($_POST["clear"])) ? '' : $_POST["accessionnummer"] . '" readonly=true'; ?>" />
      <input type="submit" name="search" value="Søg" class="button" />
      <input type="submit" name="clear" value="Ryd" class="button" />

      <?php

      if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
        if (empty($_POST["accessionnummer"])) {
          echo '<span class="error">* Der er ikke indtastet et accessionnummer</span>';
        } else {
          $connection = new DbConnection();
          $resultat = $connection->lookup_referral($_POST["accessionnummer"]);

          if ($resultat === null) {
            echo '<span class="error">* Fandt ingen henvisning med det indtastede accessionnummer: ' . $_POST["accessionnummer"] . ' </span>';
          } else {
            $receiver = $resultat->get_sender_id();
            $receiver_type = $resultat->get_sender_type();
            $cc_receiver = $resultat->get_cc_receiver_id();
            $cc_receiver_type = $resultat->get_cc_receiver_type();

            // Herunder udfyldes formularen med de hentede data
            print <<<END
              <fieldset>
                <legend>Rediger henvisende instans/kopimodtager:</legend>

                <label for="t3">Henvisende instans:</label>
                <input type="text" name="henvisende_instans" id="t3" value="$receiver" />
                <label for="t2">Type:</label>
                <select name="henvisende_instans_type" id="t2">
            END;

            if (isset($receiver_type)) {
              foreach ($receiver_type_array as $id => $value) { ?>
                <option value="<?php echo $id; ?>" <?php echo ($value == $receiver_type) ? ' selected="selected"' : ''; ?>><?php echo $value; ?></option>
              <?php }
            }

            print <<<END
              </select>
              <br />
              <br />
              <label for="t5">Kopimodtager:</label>
              <input type="text" name="kopimodtager" id="t5" value="$cc_receiver"/>
              <label for="t4">Type:</label>
              <select name="kopimodtager_type" id="t4">
            END;

            if (isset($cc_receiver_type)) {
              foreach ($ccreceiver_type_array as $id => $value) { ?>
                <option value="<?php echo $id; ?>" <?php echo ($value == $cc_receiver_type) ? ' selected="selected"' : ''; ?>><?php echo $value; ?></option>
      <?php }
            }

            print <<<END
              </select>
              <br />
              <input type="submit" name="opdater_henvisning" value="Opdater henvisning" class="button" />
              <input type="submit" value="Annuller" class="button" />
            </fieldset>
            END;
          }
        }
      } elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["opdater_henvisning"])) {
        $accessionnummer =  $_POST["accessionnummer"];
        $henvisende_instans = $_POST["henvisende_instans"];
        $henvisende_instans_type = $receiver_type_array[$_POST["henvisende_instans_type"]];
        $kopi_modtager = $_POST["kopimodtager"];
        $kopi_modtager_type = $ccreceiver_type_array[$_POST["kopimodtager_type"]];

        $error = FALSE;
        // Print fejl hvis Henvisende instant er tom
        if(empty($henvisende_instans)) {
          echo '<span class="error">* Henvisende instans må ikke være tom"</span>';
          $error = TRUE;
        }

        // Print fejl hvis kopimodtager er udfyldt, men kopimodtagertype er tom
        if(!empty($kopi_modtager) && $kopi_modtager_type == '') {
          echo '<span class="error">* Kopimodtagertype skal være udfyldt</span>';
          $error = TRUE;
        }
        
        // Hvis kopimodtager er tom, men kopimodtagertype er udfyldt
        if(!empty($kopi_modtager_type) && empty($kopi_modtager)) {
          echo '<span class="error">* Kopimodtager skal være udfyldt</span>';
          $error = TRUE;
        }

        if(!$error) {
          $connection = new DbConnection();
          // Hvis selve opdatering af henvisning fejler, så dumpes fejlen og der printes at ændringen ikke er gemt
          $resultat = $connection->save_referral($accessionnummer, $henvisende_instans, $henvisende_instans_type, $kopi_modtager, $kopi_modtager_type);
          if($resultat) {
            echo '<span class="result">Ændring gemt</span>';
          } else {
            echo '<span class="error">* Ændring blev ikke gemt</span>';
          }
          unset($connection);
        }
      }

      ?>

    </fieldset>
  </form>
</body>
</html>