<?php 

$key=$_POST["My_key"];
if($key=="")
  $key="ab";//uniqid("");

include("header_new.inc");
?>

<!-- Site navigation menu -->
<ul class="navbar">
  <li><a href="bn_file_load_gom.php">Learn a network model from data</a>
  <li><a href="home_upload.php">Make predictions using a known structure</a>  
  <li><a href="help.php">Help</a>
  <li><a href="faq.php">FAQ</a>
  <li><a href="home.php">Home</a>


</ul>

<div id="outer">
<!-- Main content -->
<br>
<h1>There was an error in an input or network ID value. The network ID must be three characters and contain only upper and lowercase letters.</h1>
</div>
</body>
</html>
