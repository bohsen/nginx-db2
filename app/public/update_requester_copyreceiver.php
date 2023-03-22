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
</body>
</html>