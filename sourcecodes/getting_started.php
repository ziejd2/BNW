<link rel="stylesheet" href="./scripts/font-awesome.css">
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/accordion.js"></script>
<?php 

include("header_new.inc");
?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="bn_file_load_gom.php">Load data and begin modeling</a>
</ul>
   <button class="accordion">Return to a network&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
  <div class="panel">
  <ul class="navbar_ac">
  <li><a href="enter_netID.php">Use a network ID to return to a network</a>
  <li><a href="home_upload.php">Load data for a known structure</a>  
  </ul>
  </div>  
  <button class="accordion">Other help resources&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
  <div class="panel">
  <ul class="navbar_ac">
  <li><a href="workflow.php">Tutorials and examples</a>
  <li><a href="help.php">Help</a>
  <li><a href="faq.php">FAQ</a>
  </ul>
  </div>  
</ul>
<ul class="navbar2">
  <li><a href="home.php">Home</a>
</ul>


<!-- Main content -->
<div id="outer">
<h2>Getting Started with BNW</h2>
<br>
    <p align="justify">The Bayesian Network Webserver (BNW) is designed to be a comprehensive tool for Bayesian network modeling of biological datasets. It should allow users to, within seconds or minutes, go from having a simply formatted input file containing a dataset to using a network model to make predictions about the interactions between variables and the potential effects of experimental interventions. New users of BNW may want to start by following <a href="BNW_workflow_net1.htm" target="_blank">this tutorial</a> which uses an example dataset that is used to create a genetic network. An additional <a href="BNW_workflow_sci.htm" target="_blank">tutorial</a> provides an introduction to using the BNW structural constraint interface. Several example networks using real and synthetic data are available <a href="workflow.php">here</a>.<br>
<br>
BNW also provides a <a href="help.php">help</a> page that provides an overview of the use of BNW as well as a <a href="faq.php">FAQ</a> page that addresses specific questions.
<br>
<br>
    To begin modeling your own data, prepare an input file following these <a href="help.php#file_format">file formatting guidelines</a>, upload the file <a href="bn_file_load_gom.php">here</a> to learn the network structure that best explains the data, and begin using it to make predictions. If you already know the structure of your network, you can upload files containing your data and the network structure <a href="home_upload.php">here</a>.
</p>
<br>
</div>
</body>
</html>
