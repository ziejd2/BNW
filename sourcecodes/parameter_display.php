<head>
<script src="./scripts/d3.v4.min.js"></script>
<script src="./scripts/create_table.js"></script>
<?php 
include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);
$dir="./data/";
//$dir="/tmp/bnw/";

$param_file = $dir.$keyval."parameters.txt";
$param_ev_file = $dir.$keyval."parameters_ev.txt";

?>

</head>


<!-- Site navigation menu -->
<ul class="navbar2">
  <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
  <li><a href="about_this_page.php#parameters">About this page</a>
  <li><a href="help.php">Help</a>
  <li><a href="home.php">Home</a>
</ul>

<body>
  <div class='table_div' id='parameter_div'>
  <script type="text/javascript">
  d3.text("<?php print($param_file);?>", function(error, raw){
      var data = raw.split("\n\n");
      var dsv = d3.dsvFormat("\t");
   var i;
   for (i = 0; i < data.length; i++) {
     var data_temp = data[i].split("\n");
     var caption_text = data_temp.shift();
     var data_temp = data_temp.join("\n");
     var newDiv = document.createElement('div');
     newDiv.id = 'table_div'+i;
     newDiv.className = 'd3_table';
     var dataCurrent = dsv.parse(data_temp);
     var div_id = '#table_div'+i;
     var paramDiv = document.getElementById("parameter_div");
     paramDiv.appendChild(newDiv);
     tabulate_caption(div_id,dataCurrent,caption_text);
   }
   if (error) throw error;
   add_header();
    });
</script>
<?php
$filename="/tmp/bnw/".$keyval."parameters_ev.txt";
if(file_exists($filename)) 
{?>
  <script type="text/javascript">
  d3.text("<?php print($param_ev_file);?>", function(error, raw){
      var data = raw.split("\n\n");
      var dsv = d3.dsvFormat("\t");
   var i;
   for (i = 1000; i < data.length + 1000; i++) {
     var data_temp = data[i-1000].split("\n");
     var caption_text = data_temp.shift();
     var data_temp = data_temp.join("\n");
     var newDiv = document.createElement('div');
     newDiv.id = 'table_div'+i;
     newDiv.className = 'd3_table';
     var dataCurrent = dsv.parse(data_temp);
     var div_id = '#table_div'+i;
     var paramDiv = document.getElementById("parameter_div");
     paramDiv.appendChild(newDiv);
     tabulate_caption(div_id,dataCurrent,caption_text);
   }
   if (error) throw error;
   add_header2();
    });
</script>
<?php
}
?>
</div>
<div id="table_header">
<h3 id="header">Original network parameters</h3>
</div>
<div id="download">
&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 href="<?php print($param_file);?>">Download original parameters</a><br><br>
<?php
$filename="/tmp/bnw/".$keyval."parameters_ev.txt";
if(file_exists($filename)) 
{?>
<h3> Network parameters considering evidence/intervention</h3>
<?php
}
?>
</div>
  <script type="text/javascript">
  function add_header() {
  var element = document.getElementById("table_header");
  var parentNode = document.getElementById("parameter_div");
  console.log(parentNode);
  parentNode.insertBefore(element,parentNode.firstChild);
  var element = document.getElementById("download");
  parentNode.appendChild(element);
}
</script>
<?php
$filename="/tmp/bnw/".$keyval."parameters_ev.txt";
if(file_exists($filename)) 
{?>
<div id="download2">
&nbsp;&nbsp;&nbsp;&nbsp;<a class=button2 href="<?php print($param_ev_file);?>">Download parameters considering evidence/intervention</a><br><br>
</div>
<?php
}
?>
  <script type="text/javascript">
  function add_header2() {
  var parentNode = document.getElementById("parameter_div");
  var element = document.getElementById("download2");
  parentNode.appendChild(element);
}
</script>

</body>
