<!DOCTYPE HTML>
<html>

<head>
    <style>
        .error {
            color: #FF0000;
        }
    </style>
</head>
<?php
include 'functions.php';
?>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <fieldset>
            <legend>Fremsøg henvisningsoplysninger</legend>

            <label for="t1">Accessionnummer:</label>

            <input type="text" name="accessionnummer" id="t1" value="<?php echo (empty($_POST["accessionnummer"]) || isset($_POST["clear"])) ? '' : $_POST["accessionnummer"]; ?>" />
            <input type="submit" name="search" value="Søg" class="button" />
            <input type="submit" name="clear" value="Ryd" class="button" />

            <?php

            # echo (empty($_POST["accessionnummer"])) ? '<span class="error">* Der er ikke indtastet et accessionnummer</span>' : $_POST["accessionnummer"];
            
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
                if (empty($_POST["accessionnummer"])) {
                    echo '<span class="error">* Der er ikke indtastet et accessionnummer</span>';
                } else {
                    $resultat = lookup_referral($_POST["accessionnummer"]);

                    

                    $receiver = $resultat->get_receiver_id();
                    $receiver_type = $resultat->get_receiver_type();
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
                        $type = array('0' => '','1' => 'sygehusafdelingsnummer','2'=>'ydernummer','3'=>'lokationsnummer','4'=>'sorkode' );
                        foreach ($type as $id => $value) { ?>
                            <option value="<?php echo $id;?>" <?php echo ($value == $receiver_type) ? ' selected="selected"' : '';?>><?php echo $value;?></option>
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
                            $type = array('0' => '','1' => 'sygehusafdelingsnummer','2'=>'ydernummer','3'=>'lokationsnummer','4'=>'sorkode' );
                            foreach ($type as $id => $value) { ?>
                                <option value="<?php echo $id;?>" <?php echo ($value == $cc_receiver_type) ? ' selected="selected"' : '';?>><?php echo $value;?></option>
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

            ?>

        </fieldset>
    </form>


</body>

</html>