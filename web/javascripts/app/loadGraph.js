// Le canevas et la taille des formes seront différents selon la définition d'écran
var vizWidth, vizHeight, linkDistance, radiusCircle, borderRect, scaleStar, textX, textY;

if ($( window ).width() >= 1400) {
	width = 782;
	height = 620;
	linkDistance = 200;
	radiusCircle = 40;
	borderRect = 50;
	scaleStar = 1.5;
	textX = 0;
	textY = -43;
} else {
	width = 570;
	height = 451;
	linkDistance = 130;
	radiusCircle = 30;
	borderRect = 40;
	scaleStar = 1;
	textX = 0;
	textY = -33;
}

// Initialisation d'un graphe de force
var force = d3.layout.force()
	.charge(-2000)
	.gravity(0.1)
	.linkDistance(linkDistance)
	.size([width, height]);

var tip = d3.tip()
	.attr('class', 'd3-tip')
	.html(function(d) { return "<span>" + d.name + "</span>"; });

// Création du canevas et création du graphe à partir des données
var svg = d3.select("#screen")
	.append("svg")
	.attr("width", 782)
	.attr("height", 620)
	.style("background-color", "#F2F2F2")
	.call(tip);

d3.json("/tmp/search_result.json", function(error, graph) {
  if (error) throw error;

  force
	  .nodes(graph.nodes)
	  .links(graph.links)
	  .start();

  var link = svg.selectAll(".link")
	  .data(graph.links)
	  .enter().append("line")
	  .attr("class", "link")
	  .style("fill", "silver")
	  
  var gnodes = svg.selectAll("g.gnode")
	  .data(graph.nodes)
	  .enter()
	  .append("g")
	  .classed("gnode", true)


	var node = gnodes.append("rect")
	  .attr("class", "node")
	  .attr("width", borderRect)
	  .attr("height", borderRect)
	  .attr("x", -25)
	  .attr("y", -25)
	  .style("fill", "silver")
	  .on('mouseover', tip.show)
	  .on('mouseout', tip.hide)
	  .on("click", function(d) {
			alert("Ok");
	  }).call(force.drag);
	  
	  var labels = gnodes.append("text")
	  .attr("x", textX)
	  .attr("y", textY)
	  .style("fill", "black")
	  .text(function(d) { return d.name; 
		  })
	  

// On enlève l'écouteur d'événement (clic) sur le premier nœud (central)
d3.select('.node').on('click', null);

  force.on("tick", function() {
	link.attr("x1", function(d) { return d.source.x; })
		.attr("y1", function(d) { return d.source.y; })
		.attr("x2", function(d) { return d.target.x; })
		.attr("y2", function(d) { return d.target.y; });

	  gnodes.attr("transform", function(d) {
		return 'translate(' + [d.x, d.y] + ')';
	  });
  });
});

