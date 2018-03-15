<?php $keyval=$_GET["My_key"]; ?>

<h3> <a href=<?php $d="./data/".$keyval."parameters.txt"; print($d);?>>View original parameters</a></h3>
<br>

<br>

<?php
$filename="./data/".$keyval."parameters_ev.txt";
if(file_exists($filename)) 
{?>
<h3> <a href=<?php $d="./data/".$keyval."parameters_ev.txt"; print($d);?>>View parameters after added evidence or intervention</a></h3>
<br>
<?php
}
?>
