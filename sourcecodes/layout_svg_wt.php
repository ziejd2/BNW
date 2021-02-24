<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="./scripts/font-awesome.css">
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/accordion.js"></script>

</head>

<?php

include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

$dir="./data/";

$svg_file=$dir.$keyval."network.svg";
$png_file=$keyval."network.png";


?>
<script language="JavaScript">
<!--
function calcHeight()
{
  //find the height of the internal page
  var the_height=
    document.getElementById('the_iframe').contentWindow.
      document.body.scrollHeight;

  //change the height of the iframe
  document.getElementById('the_iframe').height=
      the_height;
}
//-->
</script>

<!--
<script src="http://d3js.org/d3.v4.min.js" charset="utf-8"></script>
<script src="http://d3js.org/d3-selection-multi.v1.js"></script>
<script src="https://cdn.rawgit.com/eligrey/canvas-toBlob.js/f1a01896135ab378aa5c0118eadd81da55e698d8/canvas-toBlob.js"></script>
<script src="https://cdn.rawgit.com/eligrey/FileSaver.js/e9d941381475b5df8b7d7691013401e171014e89/FileSaver.min.js"></script>
-->
<script src="./scripts/d3.v4.min.js" charset="utf-8"></script>
<script src="./scripts/d3-selection-multi.v1.js"></script>
<script src="./scripts/canvas-toBlob.js"></script>
<script src="./scripts/FileSaver.min.js"></script>


<!-- Site navigation menu -->
<ul class="navbar2">
<li class="noHover">Network ID:<br><?php print($keyval);?></li>
<li><a href="layout.php?My_key=<?php print($keyval);?>">Use network to make predictions</a></li>
</ul>
  <button class="accordion">Network image options&nbsp;&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
<div class="panel">
<ul class="navbar_ac">
<li><a href="layout_svg_no.php?My_key=<?php print($keyval);?>">Hide edge weights</a></li>
</li>
<button id='saveButton' class="button1">Save network image as PNG</button>
<a href="<?php print($svg_file);?>" download><button type="submit" class="button1">Save network image as SVG</button></a>
</ul>
</div>
   <button class="accordion">More about network&nbsp;&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
<div class="panel">
<ul class="navbar_ac">
  <li><a href="matrix.php?My_key=<?php print($keyval);?>" target="_blank">View structure matrix</a>  
  <li><a href="parameter_display.php?My_key=<?php print($keyval);?>" target="_blank">View network parameters</a>  
  <li><a href="review_settings.php?My_key=<?php print($keyval);?>" target="_blank">View structure learning settings</a>  
  <li><a href="input_check.php?My_key=<?php print($keyval);?>" target="_blank">View input data and descriptions</a>  
</ul>
</div>
 <button class="accordion">Modify network structure&nbsp;&nbsp;<i class="fa fa-angle-down" style="font-size: 18px;"></i></button>
<div class="panel">
<ul class="navbar_ac">
<li><a href="layout_cyto.php?My_key=<?php print($keyval);?>" target='_blank'>Add or remove edges from network</a>
<li><a href="modify_structure_learning.php?My_key=<?php print($keyval);?>" target='_blank';>Modify structure learning settings</a>
<li><a href="remove_variables.php?My_key=<?php print($keyval);?>" target='_blank';>Select variables to remove and relearn structure</a>
</ul>
</div>
<ul class="navbar2">
 <li><a href="about_this_page.php#network_structure" target='_blank'>About this page</a>
 <li><a href="help.php" target='_blank'>Help</a>
 <li><a href="../home.php">Home</a>
</ul>




<div id="svg_div">
<br>
<br>


<script>




d3.xml("<?php print($svg_file);?>", function(error, documentFragment){
  if (error) {console.log(error); return;}

  
 var svgNode = documentFragment
    .getElementsByTagName("svg")[0];

  d3.select("#svg_div").node().appendChild(svgNode);
  //nodes = d3.selectAll('.node');
  //links = d3.selectAll('.edge');

  width = svgNode.getBBox().width*5;
  height = svgNode.getBBox().height*5;

  //  nodes
  //  .call(d3.drag()
  //	  .on("start",dragstarted)
  //	  .on("drag",dragged)
  //	  .on("end",dragended));

  //  links
  //  .call(d3.drag()
  //	  .on("start",dragstarted)
  //	  .on("drag",dragged)
  //	  .on("end",dragended));


  //function dragstarted(d) {
  //  d3.select(this).raise().classed("active", true);
  //}

  //function dragged(d) {
  //  this.x = this.x || 0;
  //  this.y = this.y || 0;
  //  this.x += d3.event.dx;
  //  this.y += d3.event.dy;
  //  d3.select(this)
  //    .attr("transform","translate(" + this.x + "," + this.y + ")");
  //}

  //function dragended(d) {
  //  d3.select(this).classed("active", false);
  //}

//console.log(svgNode);
//console.log(nodes);
//console.log(links);

  });

</script>



<script>

    d3.select('#saveButton').on('click', function(){
	 var svgString = getSVGString(d3.select("#svg_div").select('svg').node());
	 svgString2Image( svgString, width, height, 'png', save );
// passes Blob and filesize String to the callback

   function save( dataBlob, filesize ){
   saveAs( dataBlob, "<?php echo $png_file; ?>" ); // FileSaver.js function
  }
      });
// Below are the functions that handle actual exporting:
// getSVGString ( svgNode ) and svgString2Image( svgString, width, height, format, callback )
function getSVGString( svgNode ) {
  //  svgNode.setAttribute('xlink', 'http://www.w3.org/1999/xlink');
  var cssStyleText = getCSSStyles( svgNode );
  appendCSS( cssStyleText, svgNode );

  var serializer = new XMLSerializer();
  var svgString = serializer.serializeToString(svgNode);
  svgString = svgString.replace(/(\w+)?:?xlink=/g, 'xmlns:xlink='); // Fix root xlink without namespace
  svgString = svgString.replace(/NS\d+:href/g, 'xlink:href'); // Safari NS namespace fix

  return svgString;

function getCSSStyles( parentElement ) {
  var selectorTextArr = [];

  // Add Parent element Id and Classes to the list
  selectorTextArr.push( '#'+parentElement.id );
  for (var c = 0; c < parentElement.classList.length; c++)
    if ( !contains('.'+parentElement.classList[c], selectorTextArr) )
      selectorTextArr.push( '.'+parentElement.classList[c] );

  // Add Children element Ids and Classes to the list
  var nodes = parentElement.getElementsByTagName("*");
  for (var i = 0; i < nodes.length; i++) {
    var id = nodes[i].id;
    if ( !contains('#'+id, selectorTextArr) )
      selectorTextArr.push( '#'+id );

    var classes = nodes[i].classList;
    for (var c = 0; c < classes.length; c++)
      if ( !contains('.'+classes[c], selectorTextArr) )
	selectorTextArr.push( '.'+classes[c] );
  }

  // Extract CSS Rules
  var extractedCSSText = "";
  for (var i = 0; i < document.styleSheets.length; i++) {
    var s = document.styleSheets[i];

    try {
      if(!s.cssRules) continue;
    } catch( e ) {
      if(e.name !== 'SecurityError') throw e; // for Firefox
      continue;
    }

    var cssRules = s.cssRules;
    for (var r = 0; r < cssRules.length; r++) {
      if ( contains( cssRules[r].selectorText, selectorTextArr ) )
	extractedCSSText += cssRules[r].cssText;
    }
  }


  return extractedCSSText;

  function contains(str,arr) {
    return arr.indexOf( str ) === -1 ? false : true;
  }

}

function appendCSS( cssText, element ) {
  var styleElement = document.createElement("style");
  styleElement.setAttribute("type","text/css");
  styleElement.innerHTML = cssText;
  var refNode = element.hasChildNodes() ? element.children[0] : null;
  element.insertBefore( styleElement, refNode );
}
}

function svgString2Image( svgString, width, height, format, callback ) {
  var format = format ? format : 'png';

  var imgsrc = 'data:image/svg+xml;base64,'+ btoa( unescape( encodeURIComponent( svgString ) ) ); // Convert SVG string to data URL

  var canvas = document.createElement("canvas");
  var context = canvas.getContext("2d");

  var canvas = document.createElement("canvas");
  var context = canvas.getContext("2d");

  canvas.width = width;
  canvas.height = height;

  var image = new Image();
  image.onload = function() {
    context.clearRect ( 0, 0, width, height );
    context.drawImage(image, 0, 0, width, height);

    canvas.toBlob( function(blob) {
	var filesize = Math.round( blob.length/1024 ) + ' KB';
	if ( callback ) callback( blob, filesize );
      });


  };

  image.src = imgsrc;
}



</script>


</div>
</body>
</html>
