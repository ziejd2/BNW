<?php 

include("header_new.inc");
?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="help.php#file_format" target="_blank">Data formatting guidelines</a>
  <li><a href="help.php" target="_blank">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<div id="outer">
<!-- Main content -->
<br>
<h2>Investigate networks with a known structure</h2>
<br>
<p align="justify"> 
  BNW can be used to study and make predictions for models with a known structure. To use this feature, click the "Upload data and structure files" button below to upload the file containing the data for each of the variables in the network. Then, upload a file containing the network structure. Guidelines for formatting the data and structure data files can be found <a href="help.php#file_format">here</a>.
<br>
<br>
<FORM METHOD="LINK" ACTION="upload_structure_file.php">
<INPUT TYPE="submit" class=button2 VALUE="Upload data and structure files">
</FORM>
</p>
<br>
</div>
</body>
</html>