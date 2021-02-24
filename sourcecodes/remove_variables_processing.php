<?php
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

function get_tier($keyval)
{

$tier=trim($_GET['tier']);
//$dir="./data/";
//$dir="/tmp/bnw/";
$dir="/var/lib/genenet/bnw/";
$tf=$dir.$keyval."del_var.txt";
$fpvar = fopen("$tf","w");

fwrite($fpvar,"$tier");
fclose($fpvar);
}


function enter_ban_list($t1,$s1,$t2,$s2,$tier_d,$fpvar)
{
  
   for ($i=0;$i<$s1;$i++)
   {
        $data_val_1=$tier_d[$t1][$i];
           
        for ($j=0;$j<$s2;$j++)
        {
            $data_val_2=$tier_d[$t2][$j];
            if($data_val_1!=$data_val_2)
            {
               
               fprintf($fpvar,"%s\t%s\n",$data_val_1,$data_val_2); 
              

            }
   
        }
         
   }
  

}



function describe_tier($fpvar,$keyval)
{


$tierdesc=trim($_GET['tierdesc']);
//$dir="./data/";
//$dir="/tmp/bnw/";
$dir="/var/lib/genenet/bnw/";
$tf=$dir.$keyval."tier.txt";
$tier=file_get_contents("$tf");

$tier_data=array();
$tier_size=array();
$tier_d=array();
$tier_data=explode(",",$tier);

$tn=$tier_data[0];
$count=0;

for ($i=0;$i<$tn;$i++)
{
    $count+=2;
    $tier_size[$i]=$tier_data[$count];
         
    $rs=$tier_size[$i];
    for ($j=0;$j<$rs;$j++)
    {
     $count++;  
     $tier_d[$i][$j]=$tier_data[$count];
     
    
    }
   
}



$data=array();
$data=explode(",",$tierdesc);
$n=count($data);
$nn=0;
$di=0;
for ($i=0;$i<$tn;$i++)
{

$d_y=trim($data[$di]);
$di++;
$d_n=trim($data[$di]);

if($d_n=="true") //put data in banlist
{
   $s1=$tier_size[$i];
   enter_ban_list($i,$s1,$i,$s1,$tier_d,$fpvar);

}
$di++;


   for ($j=0;$j<$tn;$j++)
   {
      if($j!=$i)
      {
        $p_d=trim($data[$di]);
        $di++;
    
        if($p_d=="false") //put data in banlist
        {
             $s1=$tier_size[$j];
             $s2=$tier_size[$i];
             //echo $tier_size[$j];
            enter_ban_list($j,$s1,$i,$s2,$tier_d,$fpvar);
        }
    


        $c_d=trim($data[$di]);
        $di++;
        if($c_d=="false") //put data in banlist
        {
             $s1=$tier_size[$i];
             $s2=$tier_size[$j];
                  // echo $tier_size[$j];
            enter_ban_list($i,$s1,$j,$s2,$tier_d,$fpvar);
        }

 
      }
   }

}


}


function banlist($fpvar)
{
$ban=trim($_GET['ban']);
$data=array();
$data=explode(",",$ban);
$n=count($data);
$nn=0;
for ($i=0;$i<$n;$i+=2)
{
	$ii=$i+1;
	$d1=trim($data[$i]);
	$d2=trim($data[$ii]);

	if($d1!="" && $d2!="")
	{
  		fprintf($fpvar,"%s\t%s\n",$d1,$d2);
  		$nn++;
	}

}

}

function whitelist($fpvar)
{

$white=trim($_GET['white']);



$data=array();
$data=explode(",",$white);
$n=count($data);
$nn=0;
for ($i=0;$i<$n;$i+=2){
$ii=$i+1;
$d1=trim($data[$i]);
$d2=trim($data[$ii]);

if($d1!="" && $d2!="")
{
  fprintf($fpvar,"%s\t%s\n",$d1,$d2);
  $nn++;
}

}


}


/////////////////////Call functions//////////////////////



//$dir="./data/";
//$dir="/tmp/bnw/";
$dir="/var/lib/genenet/bnw/";

//$sid1=$dir.$keyval."ban";
//$sid2=$dir.$keyval."white";

//$Textban=$sid1.".txt";
//$Textwhite=$sid2.".txt";
//$fpb = fopen($Textban,"w");
//$fpw = fopen($Textwhite,"w");
//$datpost="From\tTo\n";
//fwrite($fpb,"$datpost");
//fwrite($fpw,"$datpost");

get_tier($keyval);

$oldkey=$keyval;

///Generate new random key
$alphas=array();
$alphas = array_merge(range('A', 'Z'), range('a', 'z'));

$al1=rand(0,51);
$al2=rand(0,51);
$al3=rand(0,51);

$alpha="$alphas[$al1]"."$alphas[$al2]"."$alphas[$al3]";
$keyval=$alpha;

shell_exec('./run_scripts/run_del_var '.$oldkey.' '.$keyval);

//describe_tier($fpb,$keyval);
//banlist($fpb);
//whitelist($fpw);


////////////////////////////////////execute structurelearning/////////////////////////////////////////////////////////////////////////////////////////////
?>
<script>
window.open("create_tiers_gom_part1.php?My_key=<?php print($keyval);?>",'_self',false);
</script>
