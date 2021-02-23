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

<h2>Genetic network of immure response to infection with Chlamydia psittaci [<a href="../example.php?My_key=example_chl|bWR" target="_blank">View network</a>]</h2>
</br>
<p>
  This network models the differential susceptibility to infection with <i>Chlamydia psittaci</i> observed in B6 and D2 mice, as well as in the BXD recombinant inbred strains that have been developed from these parental strains. The model predicts that disease status, as quantified by the weight loss after infection (Weight), is directly influenced by the level of neutrophils and the genotype at the Ctrq3 locus on Chr 11. Macrophage activation status (MAS) is also predicted to influence the level of neutrophils and the pathogen load. This network has been fully described in a previous paper [<a href="http:\\www.ncbi.nlm.nih.gov/pubmed/22438999" target="_blank">PubMed link</a>], in which several predictions made by the model were experimentally validated.</br>
</br>
</p>
</div>
</body>
</html>
