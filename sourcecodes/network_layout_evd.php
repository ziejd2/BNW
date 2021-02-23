<?php 
include("structuremap.php");
include("header_batchsearch.inc");
include("input_validate.php");

//This function is used to transfer between the integral
//  levels for discrete nodes that required by BNT and
//  the actual states of the levels that are in the input file.
function levelmap($inx,$name,$mapdata)
{

$leve_l=array();
$leve_l=explode("\n",$mapdata);
$leve_d=array();

foreach($leve_l as $l)
{ 
  $l=trim($l);
  $leve_d=explode("\t",$l);
  $inxname=trim($leve_d[0]);
  $name=trim($name);
  if($inxname==$name)
    return $leve_d[$inx];
}
           
}

/////////////Read data from net_figure file/////////////

//$dir="./data/";
$dir="/tmp/bnw/";

$keyval=valid_keyval($_GET["My_key"]);

$matfile=$dir."$keyval"."net_figure.txt";

$data_val="";

$matrix=file("$matfile");
$matrix1=file_get_contents("$matfile");   

$lfile=$dir.$keyval."nlevels.txt";
$levelmap=file_get_contents($lfile);   


$str_arrmat=array();
$str_arrmat=explode("\n",$matrix1);

if($str_arrmat[2]=="" || $matrix1=="")
{
?>
  <h1><br><br><br><font color=blue>Error: Unable to display your network structure.</font></h1>
<?php
exit;
}

$datamat=array();
$data_read=array();
 
$node=trim($str_arrmat[0]);

$width=150;
$height=150;
$font=12;

$r_index=$node+2;

for($i=0;$i<$node;$i++)
{
$datamat=explode("\t",$str_arrmat[$r_index]);
$data_read[$i][0]=trim($datamat[0]);  //name
$data_read[$i][1]=trim($datamat[1]);  //datatype
$col_in=2;
$r_index+=4;
	if($datamat[1]==1)  //type=continuous
	{
         for($j=1;$j<=101;$j++)
         {
           $datamat=explode("\t",$str_arrmat[$r_index]);
           $data_read[$i][$col_in]=trim($datamat[0]);  //x data

           $col_in++;
           $data_read[$i][$col_in]=trim($datamat[1]);  //y data

           $col_in++;
           $r_index++; // increment to point next data
         } 
	}
	if($datamat[1]>1)  //type=discrete
	{
         $iter=$datamat[1];
         for($j=1;$j<=$iter;$j++)
         {
           $datamat=explode("\t",$str_arrmat[$r_index]);
           $data_read[$i][$col_in]=trim($datamat[0]);  //x data
           //use level mapping
	   $data_read[$i][$col_in]=levelmap($data_read[$i][$col_in],$data_read[$i][0],$levelmap);
 
           $col_in++;
           $data_read[$i][$col_in]=trim($datamat[1]);  //y data
         
           $col_in++;
           $r_index++; // increment to point next data
         } 
	}

}


/////////Graphviz data read////////////////

$grv=$dir.$keyval."graphviz.txt";
$line=shell_exec('/usr/bin/dot -Tplain -y '.$grv);
$grviz_name_list=array();
//$g_file_name="./data/".$keyval."grviz_name_file.txt";
$g_file_name=$dir.$keyval."grviz_name_file.txt";
$grviz_name=file_get_contents("$g_file_name");
$grviz_name_list=explode("\n",$grviz_name);

$str_arrname=array();
$str_arrname=explode("\n",$line);
$data=array();
$ID_data=array();
$i=0;
$ii=0;
foreach($str_arrname as $row)
{
  $i++;
  if($i>1)
  {
       $data=explode(" ",$row);
       $j=0;
       $k=0;
       $flag=0;
  	foreach($data as $cell)
	{
             $j++;
             $cell=trim($cell);
             if($j==1 && $cell=="node")
             {
                $flag=1;
             }  
             if($j>1 && $j<5 && $flag==1)
             {
                if($j==2)
                {
                   $ID_data[$ii][$k]=$grviz_name_list[$cell];
                } 
                else
                   $ID_data[$ii][$k]=round($cell/10*900);
                $k++; 
             }
       } 
       if($flag==1)
       {
          $ID_data[$ii][3]=$ID_data[$ii][1]+100;
          $ID_data[$ii][4]=$ID_data[$ii][2]+150; 
          $ID_data[$ii][5]=$ID_data[$ii][1]+100;
          $ID_data[$ii][6]=$ID_data[$ii][2]; 
          $ii++;
       } 
  }
}

$nnode=$ii;
$edge_data=array();

$i=0;
$ii=0;

foreach($str_arrname as $row)
{
  $i++;
  if($i>1)
  {
       $data=explode(" ",$row);
       $j=0;
       $flag=0;
       $number_of_point=0;
       $index=0; 
  	foreach($data as $cell)
	{
             $j++;
             $cell=trim($cell);
             if($j==1 && $cell=="edge")
             {
                $flag=1;
             }  
             if($j==2 && $flag==1)
             {
                for($k=0;$k<$nnode;$k++)
                {     
                      $cell=$grviz_name_list[$cell];
                      if($cell==$ID_data[$k][0])
                      {
                          $edge_data[$ii][0]=$ID_data[$k][3];
                          $edge_data[$ii][1]=$ID_data[$k][4];
                      }                     
                }
             }
             else if($j==3 && $flag==1)
             {
               for($k=0;$k<$nnode;$k++)
                {
                      if($cell==$ID_data[$k][0])
                      {
                          $edge_data[$ii][2]=$ID_data[$k][5];
                          $edge_data[$ii][3]=$ID_data[$k][6];
                      }                     
                }
             }
             else if($j==4 && $flag==1)
             {
                  $edge_data[$ii][4]=$cell;
                  $number_of_point=$cell*2;
                  $index=5;
             }
             else if($j>4 && $number_of_point>0 && $flag==1)
             {
                if(($j%2)!=0)
                  $edge_data[$ii][$index]=round($cell/10*900)+60;
                else
                  $edge_data[$ii][$index]=round($cell/10*900)+48;
                $number_of_point--;
                $index++;
             }
       } 
       if($flag==1)
       {
          $ii++;
       } 
  }
}
$nedges=$ii;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1', {packages: ['corechart']});
</script>
 

<?php 
$data_val="";
$x_max = $ID_data[0][1];
$y_max = $ID_data[0][2];

for($i=0;$i<$nnode;$i++)
 {
////////////////////////////select node //////////////////////////////////////
   if($ID_data[$i][1] > $x_max) $x_max = $ID_data[$i][1];
   if($ID_data[$i][2] > $y_max) $y_max = $ID_data[$i][2];
			   
 $name=$ID_data[$i][0];
 $replaced_char = array("-","/",",",".","!","?");
 $name_replaced = str_replace($replaced_char,"_",$name);
 $fname="draw_".$name_replaced."()";
 $data_val.=$name;
 for($j=0;$j<$node;$j++)
 {
    if($data_read[$j][0]==$name)
    {
       $s_i=$j;
       break;
    } 
 }
 
/////////////////////////////////Display nodes//////////////////////////////////////////////////////////
?>    
 <script type="text/javascript">


<?php
  $node_type=$data_read[$s_i][1];
  if($node_type>1) //discrete node
  { 
?>
  
 
  function <?php print($fname);?> {
  // Create and populate the data table.
 
  var data = google.visualization.arrayToDataTable([
   ['<?php print($name);?>', ''],
  <?php
  $c_i=2; 

  for($j=0;$j<$node_type-1;$j++) 
  {     
       $val1=trim($data_read[$s_i][$c_i]);


       $c_i++;
       $val2=trim($data_read[$s_i][$c_i]);
       $c_i++;
       $data_val.="\t".$val2;
        
  ?>
   ['<?php print($val1);?>', <?php print($val2);?>],

  <?php
  }
  if($j==$node_type-1)
  {
        $val1=trim($data_read[$s_i][$c_i]);


       $c_i++;
       $val2=trim($data_read[$s_i][$c_i]);
       $c_i++;
       $data_val.="\t".$val2."\n";
  ?> 
    ['<?php print($val1);?>', <?php print($val2);?>]
<?php
  }
?>
  ]);
      
        // Create and draw the visualization.
      
      var cnode="<?php print($name); ?>";
      var keyv="<?php print($keyval);?>";  

        var chart = new google.visualization.ColumnChart(document.getElementById('<?php print($name);?>'));
        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            
            var s = window.prompt('Selected evidence ' + topping + ' for ' + cnode + '. Enter new evidence ', topping );	    
	    if (input_check(s)) 
	      {
		window.location.href = "add_evd.php?name=" + cnode + "&evidence=" + s + "&My_key=" + keyv;  
	      }
	    else
	      {
		window.location.href = "input_error_no_menu.php";
	      }
          }
        } 
         chart.draw(data,
                 {title:"<?php print($name);?>", titleTextStyle: {fontSize: <?php print($font);?>},
                  width:<?php print($width);?>, height:<?php print($height);?>,
		  vAxis: {textStyle: {fontSize:9},minValue: 0, maxValue: 1},
		  hAxis: {textStyle: {fontSize:11}}, legend: {position: 'none'},
                  backgroundColor: {stroke: 'black', strokeWidth: 5}}
            );
        google.visualization.events.addListener(chart, 'select', selectHandler);  

  }
<?php
 }
 else //continuous node
 {
?>
  function <?php print($fname);?> {
  // Create and populate the data table.
  var data = google.visualization.arrayToDataTable([
						    [{label:"<?php print($name);?>",type:'number'}, {label:''}],
  <?php
  $c_i=2;
  for($j=0;$j<100;$j++) 
  {
       $val1=trim($data_read[$s_i][$c_i]);

       $c_i++;
       $val2=trim($data_read[$s_i][$c_i]);
       $c_i++;         
       $data_val.="\t".$val2;
  ?>
   ['<?php print($val1);?>', <?php print($val2);?>],

  <?php
  }
  if($j==100)
  {
       $val1=trim($data_read[$s_i][$c_i]);

       $c_i++;
       $val2=trim($data_read[$s_i][$c_i]);
       $c_i++;
       $data_val.="\t".$val2."\n";
  ?> 
    ['<?php print($val1);?>', <?php print($val2);?>]
<?php
  }
?>
  ]);
      
        // Create and draw the visualization.
      var cnode="<?php print($name); ?>";
      var keyv="<?php print($keyval);?>";  
        var chart = new google.visualization.LineChart(document.getElementById('<?php print($name);?>'));

        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
           
            var topping = data.getValue(selectedItem.row, 0);
            var s = window.prompt('Selected evidence ' + topping + ' for ' + cnode + '. Enter new evidence ', topping );
	    if (input_check(s)) 
	      {
		window.location.href = "add_evd.php?name=" + cnode + "&evidence=" + s + "&My_key=" + keyv;  
	      }
	    else
	      {
		window.location.href = "input_error_no_menu.php";
	      }
	  }
       }
            chart.draw(data, {curveType: "function",
                  title:"<?php print($name);?>", titleTextStyle: {fontSize: <?php print($font);?>},
			legend: {position: 'none'},
                  width:<?php print($width);?>, height:<?php print($height);?>,
                  vAxis: {minValue: 0, maxValue: 0.5, viewWindow: {min:0}, gridlines: {count: 5}, textStyle: {fontSize: 9}},
		    hAxis: {gridlines: {count: 4}, textStyle: {fontSize: 9},viewWindowMode: 'maximized'},
		    chartArea:{left:30,top:25,right:8,bottom:25},
                    backgroundColor: {stroke: 'black', strokeWidth: 5}}
            );
           google.visualization.events.addListener(chart, 'select', selectHandler); 
       }

<?php
 }
?>
</script>
<?php
///////////////////////////end of display nodes////////////////////////////////////////////////
}

///////////////////////////////////write data to file///////////////////////////////////////////////////////////
//$fl=$dir.$keyval."structure_old.txt";
//$fp = fopen("$fl","w");
//fwrite($fp,"$data_val");
?>



<script type="text/javascript">
function canvas_arrow(context, fromx, fromy, tox, toy){
    var headlen = 15;
    var dx = tox-fromx;
    var dy = toy-fromy;
    var angle = Math.atan2(dy,dx);
    context.moveTo(fromx,fromy);
    context.lineTo(tox,toy);
    context.lineTo(tox-headlen*Math.cos(angle-Math.PI/6),toy-headlen*Math.sin(angle-Math.PI/6));
    context.moveTo(tox,toy);
    context.lineTo(tox-headlen*Math.cos(angle+Math.PI/6),toy-headlen*Math.sin(angle+Math.PI/6));
}
</script>

<script type="text/javascript">
function canvas_arrow_head(context, fromx, fromy, tox, toy){
    var headlen = 15;
    var dx = tox-fromx;
    var dy = toy-fromy;
    var angle = Math.atan2(dy,dx);
    context.moveTo(tox,toy);
    context.lineTo(tox-headlen*Math.cos(angle-Math.PI/6),toy-headlen*Math.sin(angle-Math.PI/6));
    context.moveTo(tox,toy);
    context.lineTo(tox-headlen*Math.cos(angle+Math.PI/6),toy-headlen*Math.sin(angle+Math.PI/6));
}
</script>


</head>

<body>


<canvas id="test" width="3000" height="3000" style="position:absolute">
<script>
function canvas_arrow_draw(n, polypts,context){

//Create jsGraphics object
var headlen = 10;
hexno="black";
var polypoints = new Array();
var n2=n*2;

var sx=polypts[0];
var sy=polypts[1];
 
var sx1=polypts[n2-4];
var sy1=polypts[n2-3];
var ex1=polypts[n2-2];
var ey1=polypts[n2-1];

context.beginPath();
canvas_arrow_head(context,sx1,sy1,ex1,ey1);

context.moveTo(polypts[0],polypts[1]);
nm=n%3;
ni=(n-nm)/3;

for(i=0;i<ni;i++)
{
  i11=i*6+2;
  i12=i*6+3;
  i21=i*6+4;
  i22=i*6+5;
  i31=i*6+6;
  i32=i*6+7;

  context.bezierCurveTo(polypts[i11],polypts[i12],polypts[i21],polypts[i22],polypts[i31],polypts[i32]);
  context.moveTo(polypts[i31],polypts[i32]);

 // polypoints[i] = new jxPoint(polypts[i1], polypts[i2]);
}


if(nm==2)
{
  context.moveTo(polypts[i21],polypts[i22]);
  context.bezierCurveTo(polypts[i31],polypts[i32],sx1,sy1,ex1,ey1);
  context.moveTo(ex1,ey1);

}

 context.strokeStyle = hexno;
 context.stroke();

}


var canvas=document.getElementById("test");
var context = canvas.getContext("2d");
context.lineWidth = 3;

<?php
for($i=0;$i<$nedges;$i++)
{

   $n=$edge_data[$i][4]; 
   $n2=$n*2;
   
?>
var polypoints = new Array();
var j=0;
var n="<?php print($n);?>";
<?php
for($j=0;$j<$n2;$j++)
{
$inx=$j+5;
$data=$edge_data[$i][$inx];

?>
	polypoints[j] = "<?php print($data);?>";
        j++;
<?php
}
?>
canvas_arrow_draw(n,polypoints,context);
<?php
}
?>



</script>
</canvas>


<?php 
for($i=0;$i<$nnode;$i++)
{
  $x=$ID_data[$i][1]-8; $y=$ID_data[$i][2]-20; 

  $name=$ID_data[$i][0];
  $replaced_char = array("-","/",",",".","?","!");
  $name_replaced = str_replace($replaced_char,"_",$name);
?>

<script type="text/javascript">
   google.setOnLoadCallback(draw_<?php print($name_replaced)?>);
</script>
<div id="<?php print($name)?>" style="left: <?php print($x)?>px; top: <?php print($y)?>px; width:<?php print($width);?>; height:<?php print($height);?>; position: absolute"></div>

<?php 
}

?>


</body>



</html>


