function tabulate_caption(container,data,caption_text) {
    var table = d3.select(container).append('table');
    var caption = table.append("caption").text(caption_text);
    //var titles = d3.keys(data[0]);
    var titles = data.columns;
    var headers = table.append('thead').append('tr')
        .selectAll('th')
        .data(titles).enter()
        .append('th')
        .text(function (d) {
	    return d;
	    });

    var rows = table.append('tbody').selectAll('tr')
        .data(data).enter()
        .append('tr');
    rows.selectAll('td')
        .data(function (d) {
		return titles.map(function (k) {
			return { 'value': d[k], 'name': k};
		    });
	    }).enter()
        .append('td')
        .attr('data-th', function (d) {
		return d.name;
	    })
        .text(function (d) {
		return d.value;
	    });
    return table;
}

function tabulate_header_row(container,data,caption_text) {
    var table = d3.select(container).append('table');
    var caption = table.append("caption").text(caption_text);
    //var titles = d3.keys(data[0]);
    var titles = data.columns;
    for (var i=0; i<data.length  ;i++) {
	delete data[i][""];
	data[i]["From\\To"] = titles[i]; 
    }
    //var titles = d3.keys(data[0]);
    var titles = data.columns;
    titles.pop();
    titles.unshift("From\\To");
    var headers = table.append('thead').append('tr')
        .selectAll('th')
        .data(titles).enter()
        .append('th')
	.attr("class", function(d) {
		if (d == "From\\To") {
		    return "corner-header"
			}
	    })
        .text(function (d) {
	    return d;
	    });

    var rows = table.append('tbody').selectAll('tr')
        .data(data).enter()
        .append('tr');
    rows.selectAll('td')
        .data(function (d) {
		return titles.map(function (k) {
			return { 'value': d[k], 'name': k};
		    });
	    }).enter()
        .append('td')
        .attr('data-th', function (d) {
		return d.name;
	    })
	.attr("class", function(d) {
		if (d.name == "From\\To") {
		    return "horizontal-header"
			}
	    })
        .text(function (d) {
		return d.value;
	    });
 
    

    return table;
}
