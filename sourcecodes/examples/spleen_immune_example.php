<?php 

$key=$_POST["My_key"];
if($key=="")
  $key="ab";//uniqid("");

include("../header_new.inc");
?>
<!-- Site navigation menu -->
<ul class="navbar">
  <li><a href="../bn_file_load_gom.php">Learn a network model from data</a>
  <li><a href="../home_upload.php">Make predictions using a known structure</a>  
  <li><a href="../help.php">Help</a>
  <li><a href="../faq.php">FAQ</a>
  <li><a href="../home.php">Home</a>


</ul>

<div id="outer">
<!-- Main content -->
<br>

<h2>Genetic network of immune-related genes in the spleens of BXD mice [<a href="../example.php?My_key=example_chr2_spleen|cuL" target="_blank">View network</a>]</h2>
</br>
<p>
  This network models how a SNP on Chr 2 impacts the expression of several immune-related genes in BXD mice. To create the network, we selected expression values for genes with an immune-related GO annotation from a microarray dataset that is available in the <a href="http://genenetwork.org" target="_blank">GeneNetwork</a>. The dataset contains gene expression in the spleens of 81 BXD strains and is further described <a href="http://genenetwork.org/webqtl/main.py?FormID=sharinginfo&GN_AccessionId=283" target="_blank">here</a>. Next, we performed QTL mapping of these immune-related gene expression traits and found that many genes mapped near rs3664317, a SNP at 63.69 Mb on Chr 2.<br><br>
  To make a Bayesian network model of the interactions among the locus and the genes, we created an input file, following the BNW file format, containing the genotype at rs3664317 and the expression of the genes with QTLs at this locus. In the file, which can be downloaded <a href="../example_datasets/spleen_chr2_example_data.txt">here</a>, each row is a BXD strain and each column is a variable (a gene expression trait or the genotype). We coded the genotype to fit the BNW input file format, giving strains with a D genotype a value of 1 for the rs3664317 variable and strains with a B genotype a value of a 2.<br><br> When performing structure learning for this example, we adjusted settings and added constraints using the BNW structural constraint interface. First, we performed model averaging over the 100 highest scoring network structures and set the model averaging selection threshold to 0.9, so that only edges that were in a large majority of high scoring network structures were included. Next, we separated the network variables into 3 tiers: Tier1 contained the genotype at rs3664317, Tier2 contained cis-regulated genes, and Tier3 contained trans-regulated genes. Using the default settings for within and between tier interactions, this method of tier construction prevents trans-regulated genes from being the parents of cis-regulated genes. In this case, only one gene, Ifih1, was cis-regulated by this locus.</br>
</br>
</p>
</div>
</body>
</html>
