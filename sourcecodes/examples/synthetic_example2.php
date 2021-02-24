<?php 

$key=$_POST["My_key"];
if($key=="")
  $key="ab";//uniqid("");

include("../header_new.inc");
?>
<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="../bn_file_load_gom.php">Load data and begin modeling</a>
  <li><a href="../home_upload.php">Make predictions using a known structure</a>  
  <li><a href="../help.php">Help</a>
  <li><a href="../faq.php">FAQ</a>
  <li><a href="../home.php">Home</a>


</ul>

<div id="outer">
<!-- Main content -->
<br>

<h2>Synthetic genetic network [<a href="../example.php?My_key=example2|hQG" target="_blank">View network</a>]</h2>
</br>
<p>
  This network was created using simulated genetic data. The input data file, which is available <a href="../example_datasets/example_data_8nodes.txt">here</a>, contains 2 genotypes and 6 traits. However, one of the traits (Trait5), is not connected in the network, indicating that it is not influenced by other variables in the dataset and that it does not influence other variables in the network. A complete workflow describing the use of BNW to learn the network structure and make predictions of the resulting model with this dataset is provided in <a href="../BNW_workflow_net1.htm" target="_blank">this tutorial</a>.</br>
</br>
</p>
</div>
</body>
</html>
