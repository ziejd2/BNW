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
<h1>Tutorials and example networks</h1>
<br>
<br>
<h3>Tutorials<br></h3>
<p align="justify"> 
<ul class="listbar">
  <li>Tutorial 1: Using BNW to learn a network structure and make predictions with the model. [<a href="BNW_workflow_net1.htm">View tutorial</a>] [<a href="example.php?My_key=example2|hQG" target="_blank">View network</a>]</li>
  <li>Tutorial 2:</a> A more in-depth look at the BNW structural constraint interface. [<a href="BNW_workflow_sci.htm">View tutorial</a>] [<a href="example.php?My_key=example_sci|Llu" target="_blank">View network</a>]</li>
<!--  <li><a href="structure_learning2.htm">Work flow diagram 2:</a> Global optimal search based structure learning.
  <li><a href="Prediction.htm">Work flow diagram 3:</a> Entering evidence data and predictions.
--> 
</ul>
</p>
<br>
<br>
<h3>Examples<br></h3>
<p align="justify"> 
<ul class="listbar">
  <li>Example 1: Genetic network of immune-related gene in the spleens of BXD mice. [<a href="examples/spleen_immune_example.php">Description</a>]  [<a href="example.php?My_key=example_chr2_spleen|cuL" target="_blank">View network</a>]</li>
  <li>Example 2: Genetic network of immune response to infection with Chlamydia psittaci. [<a href="examples/chl_example.php">Description</a>]  [<a href="example.php?My_key=example_chl|bWR" target="_blank">View network</a>]</li>
  <li>Example 3: Synthetic genetic network with 2 genotypes and 6 traits. [<a href="examples/synthetic_example2.php">Description</a>]  [<a href="example.php?My_key=example2|hQG" target="_blank">View network</a>]</li>
  <li>Example 4: Synthetic time-series genetic network with a genotype and 3 traits measured at 3 different time points. [<a href="examples/synthetic_example3.php">Description</a>]  [<a href="example.php?My_key=example_time_series|TEb" target="_blank">View network</a>]</li>
  <li>Example 5: Network for investigating electrical problems with a car. [<a href="examples/cardiagnosis.php">Description</a>]  [<a href="example.php?My_key=examplecar15node|eIA"  target="_blank">View network</a>]</li>
</ul>
</p>
</div>
</body>
</html>
