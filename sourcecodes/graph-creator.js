var colors = d3.scaleOrdinal(d3.schemeCategory10);

var svg = d3.select("svg"),
    width = +svg.attr("width"),
    height = +svg.attr("height"),
    node,
    link;

svg.append('defs').append('marker')
    .attrs({'id':'arrowhead',
		'viewBox':'-0 -5 10 10',
		'refX':13,
		'refY':0,
		'orient':'auto',
		'markerWidth':13,
		'markerHeight':13,
		'xoverflow':'visible',
		'stroke-width':'4px',
		'stroke-opacity':0.8})
    .append('svg:path')
    .attr('d', 'M 0,-5 L 10 ,0 L 0,5')
    .attr('fill', '#999')
    .style('stroke','none');

var simulation = d3.forceSimulation()
    .force("link", d3.forceLink().id(function (d) {return d.id;}).distance(400).strength(0))
    //    .force("charge", d3.forceManyBody())
    //.force("center", d3.forceCenter(width / 2, height / 2));

d3.json("graph.json", function (error, graph) {
        if (error) throw error;
        update(graph.links, graph.nodes);
    })

    function update(links, nodes) {
    link = svg.selectAll(".link")
	.data(links)
	.enter()
	.append("line")
	.attr("class", "link")
	.attr('marker-end','url(#arrowhead)')
    .attr('stroke',"#999")
    .attr('stroke-opacity',.8)
    .attr('stroke-width',"1px");

    link.append("title")
	.text(function (d) {return d.type;});

    edgepaths = svg.selectAll(".edgepath")
	.data(links)
	.enter()
	.append('path')
	.attrs({
                'class': 'edgepath',
                'fill-opacity': 0,
                'stroke-opacity': 0,
                'id': function (d, i) {return 'edgepath' + i}
            })
	.style("pointer-events", "none");

    edgelabels = svg.selectAll(".edgelabel")
	.data(links)
	.enter()
	.append('text')
	.style("pointer-events", "none")
	.attrs({
                'class': 'edgelabel',
                'id': function (d, i) {return 'edgelabel' + i},
                'font-size': 17,
                'fill': '#aaa',
		'dx': 0,
		'dy': -4
            });

    edgelabels.append('textPath')
	.attr('xlink:href', function (d, i) {return '#edgepath' + i})
	.style("text-anchor", "middle")
	.style("pointer-events", "none")
	.attr("startOffset", "50%")
	.attr("startOffset", "50%")
	.text(function (d) {return d.type});

    node = svg.selectAll(".node")
	.data(nodes)
	.enter()
	.append("g")
	.attr("class", "node")
	.call(d3.drag()
	      .on("start", dragstarted)
	      .on("drag", dragged)
	      //.on("end", dragended)
	      );

    node.append("circle")
	.attr("r", 5)
        .style("fill","#999");
    //	.attr("r", 5)
    //	.style("fill", function (d, i) {return colors(i);})

    node.append("title")
	.text(function (d) {return d.name;});

    node.append("text")
       .text(function (d) {return d.name;})
       .attr("x",10)
       .attr("dy", ".35em")
       .attr("font","20px")
       .attr("font-size",20)
    //.attr("dy", -3)
    //	.text(function (d) {return d.name+":"+d.label;});

    simulation
        .nodes(nodes)
        .on("tick", ticked);

    simulation.force("link")
        .links(links);
}

function ticked() {
        link
            .attr("x1", function (d) {return d.source.x;})
            .attr("y1", function (d) {return d.source.y;})
            .attr("x2", function (d) {return d.target.x;})
            .attr("y2", function (d) {return d.target.y;});

        node
            .attr("transform", function (d) {return "translate(" + d.x + ", " + d.y + ")";});

        edgepaths.attr('d', function (d) {
		return 'M ' + d.source.x + ' ' + d.source.y + ' L ' + d.target.x + ' ' + d.target.y;
	    });

        edgelabels.attr('transform', function (d) {
		if (d.target.x < d.source.x) {
		    var bbox = this.getBBox();

		    rx = bbox.x + bbox.width / 2;
		    ry = bbox.y + bbox.height / 2;
		    return 'rotate(180 ' + rx + ' ' + ry + ')';
		}
		else {
		    return 'rotate(0)';
		}
	    });
}

function dragstarted(d) {
    if (!d3.event.active) simulation.alphaTarget(0.3).restart()
			      d.fx = d.x;
    d.fy = d.y;
}

function dragged(d) {
    d.fx = d3.event.x;
    d.fy = d3.event.y;
}

//    function dragended(d) {
//        if (!d3.event.active) simulation.alphaTarget(0);
//        d.fx = undefined;
//        d.fy = undefined;
//    }



d3.select('#saveButton').on('click', function(){
	var svgString = getSVGString(svg.node());
	svgString2Image( svgString, 2*width, 2*height, 'png', save ); // passes Blob and filesize String to the callback

	function save( dataBlob, filesize ){
	    saveAs( dataBlob, 'D3 vis exported to PNG.png' ); // FileSaver.js function
	}
    });

// Below are the functions that handle actual exporting:
// getSVGString ( svgNode ) and svgString2Image( svgString, width, height, format, callback )
function getSVGString( svgNode ) {
    svgNode.setAttribute('xlink', 'http://www.w3.org/1999/xlink');
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
