<?php $keyval=$_GET["My_key"]; ?>

<h3> <a href=<?php $d="./data/".$keyval."input_desc.txt"; print($d);?>>View variable descriptions</a></h3>
<br>

<br>

<?php
$filename="./data/".$keyval."continuous_input_orig.txt";
if(file_exists($filename)) 
{?>
<h3> <a href=<?php $d="./data/".$keyval."continuous_input_orig.txt"; print($d);?>>View uploaded data file</a></h3>
<br>
<?php
}
?>
