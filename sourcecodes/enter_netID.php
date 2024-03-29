<!DOCTYPE HTML>
<html>

<?php 

include("header_new.inc");
include("input_validate.php");

$netID = "";
$netIDErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["netID"])) {
    $netIDErr = "Entering a network ID is required";
  } else {
    //    $netID = valid_keyval($_POST["netID"]);
    $netIDErr = "1";
    $netID = test_input($_POST["netID"]);
    //    $netIDErr = "1";
    // check if name only contains letters
    if (strlen($netID)!=3) {
          $netIDErr = "Network ID must be three letters"; 
    }
    if (!preg_match('/^[a-zA-Z]+$/',$netID)) {
          $netIDErr = "Only letters are allowed"; 
    }
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<div id="outer">
<!-- Main content -->
<h2>Enter network ID from previously used network</h2>
<p align="justify"> 
<br>  Networks that have previously been generated in BNW can be accessed by entering the network ID in the input box below. The network ID is currently a three character string that can be found on the left hand menu of any network page. Network files are periodically deleted from BNW so it is possible that older networks may no longer be active.
<br>
<br>
<p><span class="error"></span></p>
<FORM METHOD="post" ACTION="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Network ID:<INPUT TYPE="text" name="netID" value="<?php echo $netID;?>" style="padding: 2px 5px; border: 2px solid; border-color: black black black black; font-family: Georgia, ..., serif; font-size: 18px;
display: block; height: 30px; width: 100px;"><span class="error"> <?php echo $netIDErr;?></span><input class="button2" type="submit" name="submit" value="Submit">
</FORM>
</p>
<br>

<?php
if ($netIDErr=="1")
{
  $keyval=$netID;
?>
<script>
  window.open("layout_svg_no.php?My_key=<?php print($keyval);?>",'_self',false);  
</script>
<?php
}
?>

</div>
</body>
</html>