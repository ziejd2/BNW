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

<h2>Synthetic genetic network with time-series data [<a href="../example.php?My_key=example_time_series|TEb" target="_blank">View network</a>]</h2>
</br>
<p>
This network was created using a simulated time-series genetic dataset that is available <a href="../example_datasets/time_series_example.txt">here</a>. The dataset contains a genotype and the three quantitative traits (TraitA, TraitB, and TraitC) that were each measured at 3 time points (T1, T2, and T3). In this example, T1 might represent the baseline measurement the traits at the time of or shortly before an experimental intervention, while T2 and T3 could represent measurements of the traits in response to the experiment. <br><br>
When performing structure learning of this dataset, we used the BNW structural constraint interface to provide constraints on the structure to incorporate the prior knowledge provided by the time the data was collected. Specifically, we separated the data into 4 tiers: Tier 1 contained the genotype, Tier2 containted the three traits at time T1, Tier3 containted the traits at time T2, and Tier4 contained the traits at time T3. We maintined the default options for allowing interactions within or between tiers, preventing any traits from being the parent of the genotype and any trait at a later time point from being the parent of an earlier time point. The network shown was learned using a model average of the 100 highest scoring structures.<br><br>
The network model shows that the genotype consistently influenced TraitA at each of the time points, but that other interactions in the network were dependent on time. The variation in TraitB caused by the genotype could only be observed at time T2, after the experimental intervention, and this influence on TraitB decreased by time T3. There were also time-dependent interactions between the quantitative traits, as TraitB at T2 could be predicted by TraitA at T1 and TraitC at T3 could be predicted by TraitA at T1 and TraitB at T2.</br>
</br>
</p>
</div>
</body>
</html>
