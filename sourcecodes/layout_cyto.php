<!DOCTYPE html>
<head>


<!--
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cytoscape-panzoom/2.5.3/cytoscape.js-panzoom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
-->

<link rel="stylesheet" href="./scripts/cytoscape.js-panzoom.css">
<link rel="stylesheet" href="./scripts/font-awesome.css">


<?php

include("header_new.inc");
include("input_validate.php");
$keyval=valid_keyval($_GET["My_key"]);

$dir="./data/";
//In this case, $dir is used by the json command so it is interpreted and turned into /tmp/bnw/ automatically
//$dir="/tmp/bnw/";

$input_json=$dir.$keyval."network.json";
$output_png=$keyval."modified_network.png";

//In this section, $dir is used by php, so we need to use /tmp/bnw
$thrfile="/tmp/bnw/".$keyval."thr.txt";
if(file_exists($thrfile))
  {
    $thr=file_get_contents("$thrfile");
    $thr=trim($thr);
  } else {
  $thr=0.5;
}
?>


<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.7.1/cytoscape.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape-panzoom/2.5.3/cytoscape-panzoom.js"></script>
<script src="https://unpkg.com/dagre@0.7.4/dist/dagre.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cytoscape-dagre@2.2.2/cytoscape-dagre.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.js"></script>
-->
<script src="./scripts/jquery.min.js"></script>
<script src="./scripts/cytoscape.min.js"></script>
<script src="./scripts/dagre.js"></script>
<script src="./scripts/cytoscape-dagre.min.js"></script>
<script src="./scripts/cytoscape-panzoom.js"></script>
<script src="./scripts/lodash.js"></script>


<style type = "text/css">

#cy {
   width: 80%;
   height: 80%;
position: absolute;
/*float: top;*/
top: 3em;
left: 9em;
overflow: auto;
border: 2px solid;
border-radius: 0.5em;
}


#loading {
position: absolute;
display: block;
top: 10%;
width: 45%;
color: #000;
font-size: 8em;
text-align: center;
}

#loading.loaded {
display: none;
}

#filterOut {
  color: black;
}

</style>

<script>

  //  var default_layout = { name: 'breadthfirst',
  //		       directed: true,
  //		       maximal: true,
  //		       grid: true,
  //		       spacingFactor: 1,
  //			 fit: true, //whether to fit the viewport to the graph
  //		       padding: 10 // the padding on fit
  //}

var default_layout = { name: 'dagre',
		       fit: true,
		       padding: 30,
		       spacingFactor: 1.25
  }

  //$.getJSON("<?php print($input_json);?>",function (data) {
  $.getJSON("./data/sample_network.json",function (dumm) {
      //    console.log(data);
    //     document.addEventListener('DOMContentLoaded', function(){
      var cy = window.cy = cytoscape({
	container: document.getElementById('cy'),
	    elements: data,
	    layout: default_layout,
	    style: [
		    { 
		    selector: 'node',
			style: {
			'label': 'data(label)',
			  'font-size': 20,
			 'shape': 'roundrectangle',
			 'text-halign': 'center',
			 'text-valign': 'center',
			 'width': 'label',
			 'height': 'label',
			 'padding': '6px',
			 'color': 'black',
			 'background-color': 'white',
			 'border-style': 'solid',
			 'border-color': 'black',
			 'border-width': '2px'
			  }
		    }, 
		    {
		    selector: '.selected1',
			style:{
			'color': 'green',
			'shape': 'ellipse',
			  'background-color': '#DCDCDC',
			 'border-color': 'green',
			  'border-width': '2px',
			 'border-style': 'dashed'
			  }
		    },
		    {
		    selector: '.selected2',
			style:{
			'color': 'red',
			'shape': 'octagon',
			 'border-color': 'red',
			  'border-width': '2px',
			  'background-color': '#DCDCDC',
			  'border-style': 'dashed'
			  }
		    },
		    {
		    selector: 'edge',
			style: {
			'line-color': 'black',
			  'curve-style': 'bezier',
			  //'target-endpoint': 'outside-to-node-or-label',
			  'width': '3px',
			 'target-arrow-shape': 'triangle',
			 'target-arrow-color': 'black',
			 'control-point-step-size': '140px',
			 'arrow-scale': '2'
			  }
		    },
		    {
		    selector: '.selected3',
			style:{
			'line-color': 'red',
			  'target-arrow-color': 'red',
			  'line-style': 'dashed'
			  }
		    }
		    ]
	    });
      //       });



      //window.onload=function() {

  var eles = cy.filter(); //var containing all elements so they can be restored after being removed

  // the default values of each option are outlined below:
  var defaults = {
  zoomFactor: 0.05, // zoom factor per zoom tick
  zoomDelay: 45, // how many ms between zoom ticks
  minZoom: 0.1, // min zoom level
  maxZoom: 10, // max zoom level
  fitPadding: 30, // padding when fitting
  panSpeed: 10, // how many ms in between pan ticks
  panDistance: 10, // max pan distance per tick
  panDragAreaSize: 75, // the length of the pan drag box in which the vector for panning is calculated (bigger = finer control of pan speed and direction)
  panMinPercentSpeed: 0.25, // the slowest speed we can pan by (as a percent of panSpeed)
  panInactiveArea: 8, // radius of inactive area in pan drag box
  panIndicatorMinOpacity: 0.5, // min opacity of pan indicator (the draggable nib); scales from this to 1.0
  zoomOnly: false, // a minimal version of the ui only with zooming (useful on systems with bad mousewheel resolution)
  fitSelector: undefined, // selector of elements to fit
  animateOnFit: function(){ // whether to animate on fit
      return false;
    },
  fitAnimationDuration: 1000, // duration of animation on fit

  // icon class names
  sliderHandleIcon: 'fa fa-minus',
  zoomInIcon: 'fa fa-plus',
  zoomOutIcon: 'fa fa-minus',
  resetIcon: 'fa fa-expand',
  };



  cy.panzoom( defaults );

  $("#loading").addClass("loaded");

//This is probably more complex than it needs to be; 
// the goal is to ensure:
// 1) No more than two nodes can be selected at a time.
// 2) Only one parent node (selected1) and only one child node (selected2) 
//        can be selected
cy.on('tap', 'node', function(event) {
    if(cy.filter("node.selected1").length == 0) {
      event.target.addClass('selected1');
      event.target.removeClass('selected2');
    } else {
      event.target.removeClass('selected1');
      if(cy.filter("node.selected1").length == 1) {
	if(cy.filter("node.selected2").length == 0) {
	  event.target.addClass('selected2');
	} else {
	  event.cy.filter("node.selected2").removeClass("selected2");
	}
      } else {
	event.cy.filter("node.selected1").removeClass("selected1");
	event.cy.filter("node.selected2").removeClass("selected2");
      }
    }
  });
//The code below works with older versions of Cytoscape.js (before v3)
//cy.on('tap', 'node', function(event) {
//    if(cy.filter("node.selected1").length == 0) {
//      event.cyTarget.addClass('selected1');
//      event.cyTarget.removeClass('selected2');
//    } else {
//      event.cyTarget.removeClass('selected1');
//      if(cy.filter("node.selected1").length == 1) {
//	if(cy.filter("node.selected2").length == 0) {
//	  event.cyTarget.addClass('selected2');
//	} else {
//	  event.cy.filter("node.selected2").removeClass("selected2");
//	}
//      } else {
//	event.cy.filter("node.selected1").removeClass("selected1");
//	event.cy.filter("node.selected2").removeClass("selected2");
//      }
//    }
//  });


$("#addEdge").click(function (e) {
    if(cy.filter("node.selected1").length !=1)
      return;
    if(cy.filter("node.selected1").length !=1)
      return;
    var edge = new Object();
    edge.group = 'edges';
    edge.data = {source: cy.filter("node.selected1")[0].data('id'), target: cy.$("node.selected2")[0].data('id'), weight: 1.1};
    cy.add(edge);
    cy.filter("node.selected1").removeClass('selected1',false);
    cy.filter("node.selected2").removeClass('selected2',false);
  });


//cy.on('tap', 'edge', function(event) {
//    event.cyTarget.toggleClass('selected3');
//  });

cy.on('tap', 'edge', function(event) {
    event.target.toggleClass('selected3');
  });

$("#deleteEdge").click(function (e) {
    var tEdges = cy.filter("edge.selected3");
    for (var i = 0; i < tEdges.length; i++)
      {
       cy.remove(tEdges[i]);
      }
     eles = cy.filter();
  });


//$("#saveNetwork").click(function (e) {
//    console.log(cy.elements().jsons());
//});

$("#downloadNetwork").click(function (e) {
    var png64 = cy.png();
    $(this).attr('href',png64);
    $(this).attr('download',"<?php print($output_png);?>");
  });

$("#saveNetwork").click(function (e) {
    //var jsonObject = JSON.stringify(cy.elements().jsons());
    
    $("#loading").removeClass("loaded");
    var node_labs = cy.nodes().map(function (ele) {
	return ele.data('label');
      });
    var sources = cy.edges().map(function (ele) {
	return ele.data('source');
      });
    var targets = cy.edges().map(function (ele) {
	return ele.data('target');
      });
    var weights = cy.edges().map(function (ele) {
	return ele.data('weight');
      });
      
    var old_key = "<?php print($keyval);?>";
    $.post("modified_network.php", {
          old_key : old_key,
	  nnodes : node_labs.length,
	  node_labs : JSON.stringify(node_labs),
	  nedges : sources.length,
	  sources : JSON.stringify(sources),
	  targets : JSON.stringify(targets),
	  weights : JSON.stringify(weights)
	  },
      function( data ) {
      	window.location.href = data;
      });
    //    $.post("modified_network.php", {
    //  str : old_key,
	  //json : jsonObject
  //    	  },
});





$('#pos_slide').change(function (e) {
    eles.restore();
      pos_slide_val = $('#pos_slide').val();
      cy.$("edge[weight < " + pos_slide_val + "]").remove();
    });

//  };
});



</script>




</head>


<body>
<!-- Site navigation menu -->
<ul class="navbar">
 <li><a id="addEdge" href="#">Add edge between selected nodes</a></li>
    <li><a id="deleteEdge" href="#">Remove selected edges</a></li>
<?php
    $filename1="/tmp/bnw/".$keyval."structure_input_temp.txt";
if(file_exists($filename1))
  {?>
 <li><a id="weightFilter" href="#">Filter edges by weight:</a>
  <form name="weightValue">
    <output name="filterOut" id="filterOut" style="color:black"><?php print($thr);?></output><br>
  <input type="range" name="weightOutputName" id= "pos_slide" min="<?php print($thr);?>" max="1" value ="<?php print($thr);?>" step="0.01" list="weight" style="display: inline; width: 120px" oninput="filterOut.value = pos_slide.value">
  </form>
  </li>
<?php
											}
?>
 <li><a id="saveNetwork" href="#">Use modified network</a></li>
</ul>
<ul class="navbar2">
   <li class="noHover"><p>Network ID:<br><?php print($keyval);?></p></li>
 <li><a id="downloadNetwork" href="#">Save network as PNG</a></li>
</ul>
<ul class="navbar2">
 <li><a href="add_remove_howto.htm" target='_blank'>About this page</a> 
 <li><a href="help.php" target='_blank'>Help</a> 
 <li><a href="../home.php">Home</a>
</ul>

<div id="cy"></div>
<div id="loading">
    <span class="fa fa-refresh fa-spin"></spin>
</div>

</body>

</html>