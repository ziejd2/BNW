<link rel="stylesheet" href="./scripts/font-awesome.css">
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/accordion.js"></script>
<?php
include("input_validate.php");
$key=$_POST["My_key"];
if($key=="")
  $key="abc";//uniqid("");
$key=valid_keyval("$key");

include("header_new.inc");
?>

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
  <li><a href="getting_started.php">Getting started</a>
  <li><a href="workflow.php">Tutorials and examples</a>
  <li><a href="help.php">Help</a>
  </ul>
  </div>  
</ul>
<ul class="navbar2">
  <li><a href="home.php">Home</a>
</ul>


<!-- Main content -->
<div id="outer">

<table width=100% align=center>
<tr>
<td>
<h2>Frequently Asked Questions</h2>
<ol class="help">
<li><a href="#default_setting">What are the default settings for structure learning in BNW?</a>
<li><a href="#size_limits">Are there limits on network sizes in BNW?</a>
<li><a href="#input_files">What input files do I need to use BNW?</a>
<li><a href="#tier_examples">What are some examples of using tier assignments to incorporate prior knownledge in structure learning?</a>
<li><a href="#evid_inter">What is the difference between the evidence and intervention prediction modes?</a>
<li><a href="#unconnected">Why are nodes missing from the network or why does the network consist of separate, unconnected parts?</a>
<li><a href="#old_model">Can I restore a previous session containing a model in BNW?</a>
<li><a href="#browser_use">Which browser should I use when accessing BNW?</a>

</ol>
</td>
</tr>
</table>


<br>
<table>
<tr>
<a name=default_settings><h3>1. What are the default settings for structure learning in BNW?</h3></a>
</tr>
<br>
<tr><td>
  <p align="justify">By default, structure learning is performed with the maximum number of parents for every node in the network set to 4 and <i>k</i>, the number of best scoring structures to include in model averaging, set to 1 (i.e., no model averaging is performed). 
</td></tr>
</table>

<br>
<table>
<tr>
<a name=size_limits><h3>2. Are there any limits on network sizes when using BNW?</h3></a>
</tr>
<br>
<tr><td>
  <p align="justify">The most computationally expensive step in BNW is structure learning, and BNW limits the size of datasets that can be used for structure learning. Currently, the maximum number of nodes when performing structure learning in BNW is 19, and the maximum number of samples is 10,000. We also estimate the time required for structure learning based on the input file size and the structure learning options provided by the user. Structure learning of networks on BNW should complete within approximately 10 minutes. For longer structure learning jobs, a structure learning package which is written in C is also available for <a href="../downloads/BNW_src_files.tar">download</a>. The network structure file provided as output by the package can be loaded into BNW for use with the BNW graphical prediction interface. Alternately, users can reduce the computational cost associated with structure learning by reducing the maximum number of parents for each node or the value of <i>k</i> used in model averaging.
</td></tr>
</table>


<br>
<table>
<tr>
<a name=input_files><h3>3. What input files do I need to use BNW?</h3></a>
</tr>
<br>
<tr><td>
  <p align="justify">If you want to learn the network structure from a dataset, the only input file required for BNW is a text file where each row is a sample of the dataset. If you already know the network structure, you need two input files, one containing the structure and one containing the data. The format of these files is more fully described <a href="help.php#file_format">here</a>.<br> 
</td></tr>
</table>

<br>
<table>
<tr>
<a name=tier_examples><h3>4. What are some examples of using tier assignments to incorporate prior knownledge in structure learning?</h3></a>
</tr>
<br>
<tr><td>
  <p align="justify">Examples of assigning nodes to tiers are given below. Additionally, a tutorial with examples of using the structural constraint interface is available <a href="BNW_workflow_sci.htm">here</a>.<br><br><u>Example 1: A genetic network linking genotype and phenotype</u><br>Bayesian network modeling can be used to understand the biological processes that link genotype with phenotypes. For example, consider a dataset than contains three types of data: genotypes; molecular level phenotypes, such as gene expression traits; and higher order phenotypes. This network could be organized into three tiers. Tier 1 would contain the genotypes, Tier 2 would contain the molecular level phenotypes, and Tier 3 would contain the higher order phenotypes. In many cases, biologically relevant network models start with genotypes and terminate with higher order phenotypes, and the BNW structural constraint interface can be used to include this prior knowledge when performing structure learning. By default, the structural constraint interface would allow directed edges from genotype (Tier 1) nodes to both types of phenotype nodes (Tier 2 and Tier 3) and from the molecular level phenotypes (Tier 2) to the higher order phenotypes (Tier 3). It would also allow directed edges between nodes in the same tier. Depending on the specifics of their dataset, users may want to prevent directed edges between nodes within Tier 1 (i.e., prevent innate genotype nodes from being caused by other genotype nodes) or prevent Tier 1 nodes from being the immediate parents of Tier 3 nodes, which would require that molecular level phenotypes intervene between the genotypes and higher order phenotypes.<br><br>
<u>Example 2: Time series data</u><br>
While it does not fully support dynamic Bayesian network modeling, BNW can be used for modeling of time-series datasets and time-order information can be incorporated with the structural constraint interface. Data at each time point can be grouped into a tier and these tiers can be used to provide constraints on the network structure. For example, data from the second time point can be placed in a tier that cannot be the parents of a tier containing data from the first time point but can be the parents of tiers containing data from all subsequent time points.<br>
</td></tr>
</table>


<br>
<table>
<tr>
<a name=evid_inter><h3>5. What is the difference between the evidence and intervention prediction modes?</h3></a>
</tr>
<br>
<tr><td>
  <p align="justify">To compare the evidence and intervention prediction modes, consider a genetic network model that was learned for a set of mouse strains:<br>
<u>Evidence</u>: The evidence mode can be used to predict data generated for a new set of strains which would be useful for testing the model or for estimating gene expression data that may be experimentally missing. For example, the model may predict that high expression of Gene1 results in high expression of Gene2, and this prediction can be tested by measuring the expression levels of Gene1 and Gene2 in the new set of strains. Then, the predicted expression of Gene2 from the network model given the observed expression of Gene1 for each of the new strains can be compared with the actual observed values of Gene2.<br>
<u>Intervention</u>: In contrast, the intervention mode is appropriate for cases when you are not passively observing network interactions, but instead are experimentally perturbing the network. Again, consider a case in which the model predicts that high expression of Gene1 results in high expression of Gene2. To intervene on the network, a treatment that is known to cause high expression of Gene1 could be given. This treatment will cause all of the strains to have high expression of Gene1, and it will no longer be dependent on its parents in the original model. Also, the model will predict that Gene2 should have high expression in all strains. The expression levels of Gene2 in treated strains can be measure to test this predicted effect of intervention.<br> 
</td></tr>
</table>


<br>
<table>
<tr>
<a name=unconnected><h3>6. Why are variables missing from the network or why does the network consist of separate, unconnected parts?</h3></a>
</tr>
<br>
<tr><td>
  <p align="justify">After structure learning, it is possible that all variables will not be connected within a single network. Some variables may not be found to be associated with any other variables in the input dataset and may be left out of any network model, or there may be two or more distinct networks. In these cases, the highest scoring network model did not have all variables in a single network given the data and our scoring metric.<br> 
</td></tr>
</table>

<br>
<table>
<tr>
<a name=old_model><h3>7. Can I restore a previous session containing a model in BNW?</h3></a>
</tr>
<br>
<tr><td><p align="justify"> BNW has been recently been updated to allow for returning to a previous network model. Each network is currently identified by a three letter network ID that is noted on the upper left of the network page. This network ID can be entered into a link on the left menu of the BNW homepage. Users should note that data is occasionally cleared from the BNW server, so this method will may only allow users to return to a network model for a short time. Users can follow the description below for longer term use.<br><br>
Additionally, users have the option of downloading a file that contains the structure of their model. This file can be loaded in BNW on return visits to the site, allowing them to skip the potentially time consuming structure learning step. To download the structure matrix file after structure learning of the dataset for the first time, click on "Display structure matrix" on the left side of the prediction interface, scroll to the bottom of the popup window, and click "Download". This file and the original input data file and then be loaded into BNW by clicking "Make predictions using a known structure" on the BNW home page.<br> 
</td></tr>
</table>
<br>


<table>
<tr>
<a name=browser_use><h3>8. Which browser should I use when acessing BNW?</h3></a>
</tr>
<br>
<tr><td>
<p align="justify"> We are aware that the structural constraint interface does not display or operate correctly using some older versions of Internet Explorer that did not support HTML5 drag-and-drop functions. We have not noticed any problems with this interface using recent versions of Internet Explorer, Microsoft Edge, or other web browsers. The majority of testing of BNW has been performed using recent versions of Google Chrome and Mozilla Firefox web browsers on computers with Windows operating systems. We have also done used several browers on Linux and Apple computers and have not noticed problems.<br>
</p>
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
</div>
</html>
