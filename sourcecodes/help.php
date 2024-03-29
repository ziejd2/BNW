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
  <li><a href="faq.php">FAQ</a>
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
<h2>BNW Help: Table of Contents</h2>
<ol class="help">
<li><a href="#introduction">Introduction</a>
<li><a href="#learn_methods">Structure learning overview</a>
<li><a href="#constraint_interface">Structure learning settings and the structural constraint interface</a>
<li><a href="#param_learn">Parameter learning and using models to make predictions</a>
<li><a href="#learn_details">Details of structure learning methods</a>
<li><a href="#file_format">Formatting files for BNW</a>
<li><a href="#download_sl">Downloadable structure learning package</a>
<li><a href="#updates">Recent updates to BNW</a>
<!-- <li><a href="#updates">Recent updates and upcoming BNW features</a> -->

</ol>
</td>
</tr>
</table>


<br>
<table>
<tr>
<a name=introduction><h2>1. Introduction to BNW</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify">Use of the Bayesian network webserver (BNW) can be broken up into two main parts: learning the structure of a network model and using the model to make predictions about the interactions between the variables in the model. Both of these steps are more fully described later in this help file. However, users may also want to start using BNW by following <a href="BNW_workflow_net1.htm">this tutorial</a> for network modeling of a dataset containing 8 variables which is available <a href="example_datasets/example_data_8nodes.txt">here</a>. 
</td></tr>
</table>



<br>
<table>
<tr>
<a name=learn_methods><h2>2. Overview of structure learning in BNW</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify"> The first step in Bayesian network modeling of a dataset is identifying the network structure. In BNW, users can either upload a known network structure or learn the network structure that best explains the data. Structure learning from a dataset identifies which directed edges between network variables (nodes) should be included in the network to represent the conditional dependencies observed in the data. The structure learning method implemented in BNW can be used to learn the network structures of discrete, continuous, and hybrid (i.e., datasets containing both hybrid and continuous variables) datasets. After <a href="#file_format">uploading a text file</a> containing a dataset, users can either immediately perform structure learning using default settings or <a href="#constraint_interface">add or modify structural constraints</a> that can improve the performance of structure learning. By default, BNW limits the maximum number of parents for each node in the network to 4 and presents only the highest scoring network structure (i.e., no model averaging is performed). The structure learning method is more fully described <a href="#learn_details">later in this help file</a>. 
</td></tr>
</table>


<br>
<table>
<tr>
<a name=constraint_interface><h2>3. Structure learning settings and the structural constraint interface</h2></a>
</tr>
<br>
<tr><td>
<p align="justify">
       BNW includes a structural constraint interface that provides users with options that can increase the speed of structure learning, aid in identifying robust network structures, and limit structure searches to biologically or physically meaningful networks by incorporating prior knowledge. This procedure has two main steps. First, a set of global structure learning settings are defined. Second, network variables are assigned to tiers and the types of interactions that are allowed within and between tiers are set. An example of using the structural constraint interface is available <a href="BNW_workflow_sci.htm">here</a>.<br><br><b>Global structure learning settings:</b><br><br>
<u>Maximum number of parents</u>: This option sets a limit on the number of immediate parents for every node in the network and can impact structure learning in two main ways. First, limiting the maximum number of parents can dramatically reduce the time required for  structure learning of larger networks. Second, this limit may also help in avoiding over-fitting a network model, as it prevents a variable from being directly influenced by a large number of the other variables in the network. By default, the maximum number of parents of a node in BNW is 4.<br><br>
<u>Number of networks to include in model averaging</u>: This option specifies <i>k</i>, the number of the high scoring networks that will be included in <a href="#learn_details">model averaging</a>. For <i>k</i>=1, only the highest scoring network is considered and no model averaging is performed. For other values of <i>k</i>, model averaging is performed over the <i>k</i>-best networks. Increasing the value of <i>k</i> will increase the time required to perform the structure learning search but may improve the performance of model averaging.<br><br>
<u>Model averaging selection threshold</u>: This option specifies the threshold that should be used to select directed edges to be included in the network given their posterior probabilities after model averaging. All directed edges with posterior probabilities greater than the threshold will be included in the network. It is ignored if <i>k</i>=1. By default, the threshold is set to 0.5.<br><br>
<b> Structural constaint interface:</b><br><br>
<u>Number of tiers</u>: This option specifies the number of different tiers to which variables can be assigned.<br><br>
<u>Tier Assignment</u>: Network variables can be placed in desired tiers by clicking and dragging the variable into the appropriate box. If variables are not placed in a tier, they are included in structure learning without any restrictions.<br><br>
<u>Tier Interactions</u>: This section allows for the description of the interactions that are allowed within and between tiers. By default, edges are allowed within tiers for all tiers, and nodes within a tier are only allowed to be parents of nodes within lower ranking tiers. For example, nodes in Tier2 of a network with 4 tiers could be the parents of nodes in Tiers 3 and 4, but could not be the parents of nodes in Tier1. Users can modify the allowed interactions to fit the details of their networks. For example, they may want to prohibit interactions within a tier containing variables that do not causally depend on each other.<br><br>
<u>Specific Banned and Required Edges</u>: Finally, users can enter lists of banned and required edges to identify specific interactions that should or should not be included in the network. Banned edges can be used if experimental testing has shown that a particular regulatory relationship does not exist, while required edges can specify known regulatory relationships.
</p>
<br>
</td></tr>
</table>


<br>
<table>
<tr>
<a name=param_learn><h2>4. Parameter learning and using models to make predictions</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify"> After structure learning is completed, BNW automatically performs parameter learning of the network model using the Kevin Murphy's <a href=http://citeseerx.ist.psu.edu/viewdoc/summary?doi=10.1.1.25.1216>Bayes Net Toolbox</a> (<a href=https://github.com/bayesnet/bnt>BNT</a>) and displays the network model. BNW has been recently been updated and Dirichlet prior distributions are now used during parameter learning.<br><br>Discrete variables in the network are displayed as bar charts and continuous variables are displayed as Gaussian distributions. The networks can be used to make predictions after clicking on a node and entering a value for that variable. Specifically, click on either the blue bar for a discrete node or the blue line for a continuous node to bring up a pop-up box that can be used to enter a value for the node. After submitting a value for the variable, the distributions of the other nodes in the network will change, allowing for visualization of the impact of setting the variable to the given value. The distributions after the entered value is considered are shown in red, while the original distributions are shown in blue. The node for which data was entered is outlined in red.<br><br>
Two prediction modes are available in BNW: evidence and intervention. In the evidence mode, entered values will alter the distributions of the other variables in the network, but will not alter the network structure. In intervention mode, the intervention alters both the distributions of the network variables and the network structure. Specifically, the intervened variable becomes independent of its parents. Evidence mode is appropriate when making predictions of other network variables after the value of one variable in the network is observed, while intervention mode is appropriate for predictions after experimental interventions that alter the values of some variables in the network. Further discussion of the difference between evidence and intervention prediction modes is given on the BNW <a href="faq.php">FAQ</a> page.       
</td></tr>
</table>


<br>
<table>
<tr>
<a name=learn_details><h2>5. Details of structure learning methods</h2></a>
</tr>
<br>
<tr><td>
  <p align="justify"> The structure learning method used in BNW can be broken down into three main steps: calculating local network scores, determing global structures that optimize network scores, and, if indicated by user settings, performing model averaging over high scoring structures.<br><br>
<u>Local score calculation</u>: The score of a local structure of a Bayesian network considers how well a node is explained by the nodes that are its immediate parents. To begin structure learning in BNW, we calculate all possible local scores by perform an exhaustive search of local structures given the structural constraints specified by the user. In order to allow structure learning of hybrid datasets, BNW calculates local scores using the scoring metric proposed by <a href=http://www.jstatsoft.org/v08/i20/paper>Bottcher and Dethlefsen</a> that they have previously incorporated in the R package <a href=http://cran.r-project.org/web/packages/deal/index.html><i>deal</i></a>. Briefly, to allow for the use hybrid datasets containing both discrete and continuous variables, local structures are scored based on conditional probability tables for entirely discrete local strucutes, Gaussian distributions for entirely continuous local structures, and conditional Gaussian distributions for hybrid local structures. One property of this scoring metric is that it does not allow discrete nodes to be the children of continuous nodes. To improve the speed of structure learning, we do not use <i>deal</i>, but, instead, calculate local scores using code that we have written in the C programming language.<br><br>
<u>Search for the <i>k</i>-best global optimal structures</u>: After calculating local scores, BNW performs a search for the <i>k</i>-best global optimal structures, using a user-specified value of <i>k</i>. The <i>k</i>-best structure search method used in BNW was developed by <a href=http://arxiv.org/abs/1203.3520>Tian and Re</a>. It is an adaptation of an algorithm for identifying the optimal network structure developed by <a href=http://arxiv.org/ftp/arxiv/papers/1206/1206.6875.pdf>Silander and Myllymaki</a> that is available <a href=http://b-course.cs.helsinki.fi/obc/bene/>here</a>.<br><br>
<u>Model averaging</u>: Model averaging can be used to reduce the risk of over-fitting data to a single model. In BNW, model averaging is automatically performed over the <i>k</i>-best scoring structures, when users select values of <i>k</i> > 1. To select features (i.e., directed edges between nodes), the posterior probability of each feature is calculated by a weighted average over the <i>k</i>-best networks where the weight is given by the score of the global network structure. This posterior probability, which ranges for 1 for features included in all high scoring networks to 0 for features in no high scoring networks, reflects confidence in the feature.<br><br>Model averaging may be particularly advantageous when learning network models using small datasets. As the number of samples in a dataset increases, the differences between the scores of the highest scoring networks often also increase. Therefore, instead of a single network structure having the best score, structure learning of small datasets may identify a group of structures with similar scores. Model averaging can be used to select features that are common to many of these high scoring networks.
</td></tr>
</table>




<br>
<table>
<tr>
<a name=file_format><h2>6. Data formatting guidelines</h2></a>
</tr>
<br>
<tr>
<h3>Data file format</h3>
</tr>
<tr><td>
  <p align="justify"> Data files uploaded to the Bayesian Network Webserver should be tab-delimited text files with the names of the variables in the first row of the file and the values of the variables for each sample or individual in the remaining rows. A sample input file is provided <a href=sample_input.php>here</a>.<br><br>Variable names should not contain any whitespace characters.<br><br>BNW automatically determines whether each variable contains continuous or discrete data by applying the following rules, in order:<br><br>
 <b> 1.</b> If a variable contains 3 or fewer different values, the variable is considered to be discrete.<br><br>
 <b> 2.</b> If a variable contains more than 20 different values, the variable is considered to be continuous.<br><br>
 <b> 3.</b> If the ratio of the number of different values for a variable compared to the number of cases in the data set is large, the variable is considered to be continuous. Specifically, if this ratio is 1/3 or larger, the variable is considered to be continuous.<br><br>
 <b> 4.</b> If none of the first three rules apply, the data set is inspected to determine if any of the values for the variable contain a period (.). If at least one value contains a period, the variable is considered to be continuous; otherwise, the variable is considered to be discrete.<br><br>
  Users can examine whether or not BNW has correctly loaded input data files and classified variables by clicking on "View uploaded variables and data" after uploading a dataset. We believe that BNW should correctly classify variables in most cases, but users may occasionally need to add or remove a period to the data of some variables. <br><br>
BNW allows input data files to contain cases that have missing data. A value of "NA" should be entered if the value of a variable is missing for the case. Cases with missing variables are ignored by BNW and are removed from the data set before structure and parameter learning.<br><br>
    An example input data file for a file with 5 variables is given below. The network contains 2 discrete (Disc1 and Disc2) variables, which are given in the first two columns of the file, and 3 continuous variables (Cont1, Cont2, and Cont3). Disc1 is a discrete variable with two states (1 and 2), while Disc2 has two states (A and B). Although Cont2 has integral values, the value of Cont2 for the first sample is given as '3.0' instead of '3'. The addition of the '.' is not necessary, but ensures that Cont2 is interpreted as a continuous variable.
</p>
<br>
<div class="d3_table">
<table>
<tr><th>Disc1</th><th>Disc2</th><th>Cont1</th><th>Cont2</th><th>Cont3</td></tr>
<tr>
<td>2</td><td>A</td><td>3.25</td><td>3.0</td><td>0.97</td>
</tr>
<tr>
<td>2</td><td>B</td><td>2.46</td><td>2</td><td>0.93</td>
</tr>
<tr>
<td>1</td><td>A</td><td>4.21</td><td>33</td><td>0.43</td>
</tr>
<tr>
<td>2</td><td>B</td><td>3.76</td><td>8</td><td>0.88</td>
</tr>
<tr>
<td>2</td><td>A</td><td>3.69</td><td>4</td><td>0.91</td>
</tr>
<tr>
<td>1</td><td>B</td><td>4.27</td><td>13</td><td>0.38</td>
</tr>
<tr>
<td>1</td><td>A</td><td>4.12</td><td>9</td><td>0.45</td>
</tr>
</table>
</div>
<br>
<br>
<h3>Structure file format</h3>
<p align="justify">
  If the structure of the network model for a dataset is already known, users can upload this structure by selecting "Upload structure" on the BNW home page. The structure file should be tab-delimited, with the variable names on the first row. The remainder of the file should be an n x n matrix of 0's and 1's, where n is the number of variables in the network. A '1' in row i and column j in this matrix indicates that there is a directed edge connecting variables i and j, (i.e., there is a edge from i to j in the network). '0' indicates that there is not a directed edge from variable i to variable j.
<br><br> An example of a structure data file is shown below. The following edges would be included in the network:
<br>1. Disc1 -> Cont1
<br>2. Dics2 -> Cont2
<br>3. Cont1 -> Cont3
<br>4. Cont2 -> Cont3 
</p>
<br>
<div class="d3_table">
<table>
<tr><th>Disc1</th><th>Disc2</th><th>Cont1</th><th>Cont2</th><th>Cont3</td></tr>
<tr><td>0</td><td>0</td><td>1</td><td>0</td><td>0</td></tr>
<tr><td>0</td><td>0</td><td>0</td><td>1</td><td>0</td></tr>
<tr><td>0</td><td>0</td><td>0</td><td>0</td><td>1</td></tr>
<tr><td>0</td><td>0</td><td>0</td><td>0</td><td>1</td></tr>
<tr><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
</table>
<br>
</table>




<br>
<table>
<tr>
<a name=download_sl><h2>7. Downloadable structure learning package</h2></a>
</tr>
<br>
<tr><td>
<p align="justify"> To learn the structure of large networks or for large values of <i>k</i> when identifying the <i>k</i>-highest scoring networks for model averaging, users may want to download a package containing the BNW structure learning method, which is available <a href="../downloads/BNW_src_files.tar">here</a>. The model_averaging.txt output file provided by the package can be loaded into BNW by selecting "Make predictions using a known structure" on the left menu on the BNW homepage or <a href="home_upload.php">here</a> and used to make predictions. The input of the data file required by the downloadable package has one change from the BNW input file format; namely, the downloadable package requires that user specify the variable type on the second line of input file. Enter the number of unique states on this line for discrete variables and enter '1' on this line to indicate a continuous variable. The downloadable package is written in C and is intended for use on computers with a Linux operating system and the gcc compiler.<br>
</table>


<br>
<br>
<table>
<tr>
<a name=updates><h2>8. Recent updates to BNW</h2></a>
</tr>
<br>
<tr><td>
<p align="justify"> BNW has recently been updated to add features and improve the user experience. Four major recent changes have been:<br><br>
<b>The addition of a new visualization of the network structure:</b> This network structure is presented to users immediately after structure learning to allow for a simpler introduction to the relationships within the network model. This new network image can be shown with or without edge weights that were determined after model averaging and can also be exported as a PNG or SVG file. The visualization of the network that has previously been used in BNW and is appropriate for using the network to make predictions can be accssed by clicking on the <u>Use network to make predictions</u> button on the left side of the page.<br>
<br><b>The addition of a several options for modifying input files and network structures:</b> Users of BNW now have the option of changing structure learning settings, making specific changes to network structures, and removing variables from an uploaded data set without having to re-upload input data files.<br>
<br><b>The addition of several options for learning more about the network parameters and distributions:</b> First, a <u>View parameters</u> button has been added that allows users to quantify network parameters. The parameters of the network model considering the original data set that was uploaded by the user or the predicted parameters considering the evidence or intervention that has been entered by the user. The fraction of cases for each possible state is provided for discrete nodes, while continuous nodes are described by the means and standard deviations of Gaussian distributions.<br><br>If users have made predictions using either the evidence or intervention modes, a link to a file containing the parameters of the network considering the entered evidence or intervention is also provided. The file lists the predicted fraction of states for discrete nodes and predicted mean and standard deviation of the Gaussian distribution for continuous nodes. Nodes for which evidence or intervention has been entered are also noted in the file.<br>
<br>Second, clicking <u>View violin plots of distributions</u> shows a figure created using <a href="plotly.com" target="_blank">plotly</a> that compares fits to uploaded data and the distributions from the network parameters. If evidence or interventions have been added to the network, an additional plot compares the fits to the data, as originally uploaded, with the distributions in the network model considering the evidence or intervention.<br>
<br><a name=crossvalid></a>
<b> BNW is now able to perform cross-validation and make predictions on a test data set:</b> Clicking the <u>Cross validation and predictions</u> button on the network page presents three options for testing the predictions of the network: leave-one-out cross validation, k-fold cross validation, and uploading a test data set to make predictions using the network model.<br><br> 
<b>a) Leave-one-out cross validation: </b>Users should enter the name of a network variable that they want to investigate. These calculations can take up to 3 minutes for large data sets.
After the calculation is complete, the cross-validation output contains the following information:<br><br>
For discrete variables, the predicted likelihood of each state for the left-out case, given the values of its parent variables in the network, is provided.<br><br>
For continuous variables, the predicted mean and standard deviation of the left-out variable, given the values of its parent variables in the network, is provided.<br><br>
<b>b) k-fold cross validation: </b>Users should enter the name of the variable that should be predicted as well as the number of folds into which the data set should be split.
The output file contains similar information as what is produced using leave-one-out cross-validation.<br><br>
<b>c)Predictions on a test data set: </b>Users can then upload a file containing a test data set. The format of the file should follow the format of the input data file with one important exception: The first line of the file should containing the name of the variable that should be predicted.<br><br>
The test data file can contain missing data. If data for a variable of a given sample or case is not known, an "NA" can be entered in the input file. Data can be missing for the variable that is to be predicted or for the variables that are to be used as predictors.<br>
<br><b>Other changes and new features</b> that have been added to BNW since its introduction in <a href="https://academic.oup.com/bioinformatics/article/29/21/2801/195868" target="_blank">Bioinformatics</a> include:<br>
<br><b>1)</b> Parameter learning settings have been modified. Specifically, network parameters (e.g., the distributions of states for discrete variables and means and standard deviations of Gaussian distributions) are now learned using Dirichlet prior distributions. In our testing, this has had a minimal impact on the parameters of most networks, but has helped reduce the impact of cases with rare combinations of states on the predicted distributions of some networks. In the original version of BNW, no priors were used.<br>
<br><b>2)</b> Increased flexibility in formatting of uploaded data files. One major change is that BNW now allows for users to upload data files in which discrete variables have alphabetic values. For example, a file containing a node for the genotype of BXD mice can now have values of B and D; the values do not have to be recoded as integers. Input data files can also contain missing data by entering "NA" for missing values. Cases with missing data are ignored during structure and parameter learning of the network. A full description of the proper format for input files in BNW considering these changes can be found <a href="help.php#file_format">here</a>.<br>
<br><b>3)</b> Added "View uploaded variables and data" button to allow users to ensure that uploaded data sets are loaded and parsed correctly. After users upload a data set, clicking this button provides the ability to view either the uploaded data file directly or view a variable description file that shows how BNW has parsed the data. The variable description file lists the number of variables and cases (e.g., individuals or samples) in the data file. It also indicates whether each variable is discrete or continuous and the criteria that was used to make this determination. For discrete variables, the possible states of the variable are provided. For continuous variables, the mean and standard deviation of the variable is provided.<br>
<br><b>4)</b> Added a "Use a network ID to return to a network" on the BNW home page. This button allows users to enter a network ID to return to a previously generated network model. The network ID can also be used to share the network model with collaborators. The network ID is a three character string that is listed in left menu of a BNW network page.
<br>
<br><b>5)</b> In a change that is invisible to users, Octave is used instead of Matlab for parameter learning using the BayesNet Toolbox.<br> 
<!--
-->
<br>
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
