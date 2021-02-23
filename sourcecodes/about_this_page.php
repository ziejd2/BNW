<link rel="stylesheet" href="./scripts/font-awesome.css">
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/accordion.js"></script>
<?php

$key=$_POST["My_key"];
if($key=="")
  $key="ab";//uniqid("");

include("header_new.inc");
?>

<!-- Site navigation menu -->
<ul class="navbar2">
  <li><a href="help.php">Main help page</a>
  <li><a href="home.php">Home</a>
</ul>




<!-- Main content -->
<div id="outer">

<table width=100% align=center>
<tr>
<td>
<h2>Table of Contents</h2>
<ol class="help">
<li><a href="#file_upload">File upload</a>
<li><a href="#network_structure">Network structure</a>
<li><a href="#network_predictions">Network predictions</a>
<li><a href="#input_check">Input file processing</a>
<li><a href="#matrix">Structure matrix</a>
<li><a href="#parameters">Network parameters</a>

</ol>
</td>
</tr>
</table>


<br>
<table>
<tr>
<a name=file_upload><h2>1. File upload page</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">This is the main page for uploading a data file for use with BNW. This input file should be a tab-delimited text file. Formatting guidelines are more fully described <a href="help.php#file_format">here</a>. Several example data sets can be loaded by clicking the appropriate button.<br><br>
  After loading the data file, Bayesian network modeling can performed using default settings or using the BNW <a href="help.php#constraint_interface">structural constraint interface</a>. The "Remove variables from data set" button can be used to edit input files. This option may be useful for examining the impact of specific variables on network structures.<br><br>
  Finally, a table describing the file uploaded to BNW is provided that can be inspected to ensure that the input file was properly interpreted. This table is described in more detail <a href=#input_check>here<a>.<br><br> 
</td></tr>
</table>


<br>
<table>
<tr>
<a name=network_structure><h2>2. Network structure page</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">This page displays the network structure learned from the loaded data file. If model averaging was used to determine the network structure, the weights of edges after model averaging can be viewed by clicking "Network image options" and "Show network with edge weights". Users should also note the three letter network ID code in the upper left of the page. This code can be used to return to the network in the future. However, files stored on BNW are cleared periodically, and users should download the Structure Matrix, as described <a href="#matrix">here</a>, to ensure that they can return to a specific network structure in the future.<br><br>
  After viewing the network structure, users have four main options. First, clicking "Use network to make predictions" goes to a more detailed view of the network that can be used to quickly investigate how network variables respond to interventions.<br><br>
  Second, the "Network image options" menu allows users to save the network image as a PNG or SVG file. If model averaging was used during structure learning, the weights of network edges can be added to the network image<br><br>
  Third, users can learn more about the network structure and parameters, or check to make sure the input file was properly processed by BNW.<br><br>
  Fourth, the network structure can be modified by either directly adding or removing specific edges, changing the settings used during structure learning, or removing variables from the data set.<br><br>
</td></tr>
</table>

<br>
<table>
<tr>
<a name=network_predictions><h2>3. Network predictions page</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">This page is the main way in which users can use the network to make predictions about how observed evidence or interventions impact the other variables in the network. Users can switch between "Evidence" and "Intervention" modes by clicking the appropriate button. These modes are described more fully <a href=help.php#param_learn>here</a>. Entered evidence or interventions can be removed by clicking "Clear evidence".<br><br>
  Leave-one-out cross-validation, k-fold cross validation, and predictions of an uploaded test set can be performed using the network after clicking on "Cross validations and predictions".<br><br>
  Users can also access network parameters and violin plots of the distributions of the network under the "More about network" menu. If evidence/intervention has been entered in the network, parameters and violin plots that consider this information are also provided, providing additional quantification and visualization of the impact of the added evidence/intervention.<br><br>
</td></tr>
</table>



<br>
<table>
<tr>
<a name=input_check><h2>4. Input processing page</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">The main goal of this page is to allow users to ensure that input files have been uploaded and processed correctly.  The title of the table indicates if any variables or cases (rows) were automatically removed by BNW. Variables would be removed by BNW if all cases contained the same value. Cases would be removed if they contained missing data (i.e., at least one of the variables in the case had a value of 'NA').<br><br>
  The table has 4 columns:<br>
- The name of each variable in the input file.<br>
- The type of the variable as determined by BNW.<br>
- The number of states for discrete variables or the mean and standard deviation for continuous variables.<br>
- A brief explanation for why the variable was classified as discrete or continuous.<br><br>
</td></tr>
</table>



<br>
<table>
<tr>
<a name=matrix><h2>5. Structure matrix</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">This page provides the structure matrix, a table containing the network structure learned from the data. A '1' in the table indicates that a directed edge going from the row variable to the column variable is present in the network. In other words, the row variable is the parent, while the column variable is the child.<br><br>
The structure matrix can be downloaded by clicking the link under the structure matrix. Users can upload the structure matrix file to BNW in the future to return to the network and make additional predictions.<br><br>
  If model averaging was performed when learning the structure, a second table on the page provides the model averaging scores of the directed edges in the network. Model averaging is further described <a href=help.php#learn_details>here</a>.<br><br>
</td></tr>
</table>


<br>
<table>
<tr>
<a name=parameters><h2>6. Network parameters</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">This page provides a table with the network parameters for each variable in the network. For discrete variables, the probability of each possible state is shown. For continuous variables, means and standard deviations of Gaussian distributions are provided. If the continuous variable has one or more discrete parents, it is described by a mixture of Gaussian distributions, and the corresponding table contains the weights of these Gaussian distributions.<br><br>
  If evidence or intervention has been used to make predictions in the network, there is a second set of parameter tables. These tables provide the parameters considering entered evidence or intervention values.<br><br>
</td></tr>
</table>


</table>


<br>
<br>

<br>

<table align="center" width="100%">
<tr>
<a name="contact"><h2>Contact us</h2></a>
</tr>
<tr>
<td>
Please send questions and comments to <a href=mailto:ycui2@uthsc.edu>Dr. Yan Cui</a> at University of Tennesee Health 
Science Center.
</td>
</tr>
</table>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
</html>
