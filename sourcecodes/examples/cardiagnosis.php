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

<h2>Car Diagnosis [<a href="../example.php?My_key=examplecar15node|eIA">View network</a>]</h2>
</br>
<p>
Car Diagnosis is an example of Bayesian Network structure originally designed by Norsys Software Corp. The example structure shows the relationships between different parts of a car. This network includes 15 discrete variables. Label values of the variables are annotated in the following table. </br>
</br><table border="1">
<tr>
<th>Variables</th><th>Label 1</th><th>Label 2</th><th>Label 3</th>
</tr>
<tr>
<td>Voltage_at_plug</td><td>1=none</td><td>2=weak</td><td>3=strong</td>
</tr>
<tr>
<td>Car_cranks</td><td>1=False</td><td>2=True</td>
</tr>
<tr>
<td>Spark_plugs</td><td>1=fouled</td><td>2=too wide</td><td>3=okay</td>
</tr>
<tr>
<td>Spark_quality</td><td>1=very bad</td><td>2=bad</td><td>3=good</td>
</tr>
<tr>
<td>Distributer</td><td>1=faulty</td><td>2=okay</td>	
</tr>
<tr>
<td>Spark_timing</td><td>1=very bad</td><td>2=bad</td><td>3=good</td>
</tr>
<tr>
<td>Car_starts</td><td>1=False</td><td>2=True</td>
</tr>
<tr>
<td>Headlights</td><td>1=off</td><td>2=dim</td><td>3=bright</td>
</tr>
<tr>
<td>Alternator</td><td>1=faulty</td><td>2=okay</td>	
</tr>
<tr>
<td>Charging_system</td><td>1=faulty</td><td>2=okay</td>	
</tr>
<tr>
<td>Battery_voltage</td><td>1=dead</td><td>2=weak</td><td>3=strong</td>
</tr>
<tr>
<td>Battery_age</td><td>1=very old</td><td>2=old</td><td>3=new</td>
</tr>
<tr>
<td>Main_fuse</td><td>1=blown</td><td>2=okay</td>	
</tr>
<tr>
<td>Starter_system</td><td>1=faulty</td><td>2=okay</td>	
</tr>
<tr>
<td>Starter_motor</td><td>1=faulty</td><td>2=okay</td>	
</tr>
</table>
</br>
</p>

</div>
</body>
</html>
