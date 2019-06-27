<?php 
//$cmd="/usr/bin/dot -Tpng -o $figfile $grfilenametemp";
//system($cmd);
$cmd="top -b -n 2 -u apache|grep ^Cpu|cut -d',' -f1|cut -d':' -f2|cut -d'%' -f1>datafile";
system($cmd);

$cpu=file_get_contents("datafile");           
$str_arrmat=array();
$str_arrmat=explode("\n",$cpu);

if($str_arrmat[1]>70.0)
{

?>
<script>
window.open("http://bnw.genenetwork.org/BNW_1.22/sourcecodes/home.php",'_self',false);
</script>
<?php
}
else
{
?>    
<script>
window.open("http://compbio.uthsc.edu/BNW_1.22/sourcecodes/home.php",'_self',false);
</script>

<?php
}
?>
