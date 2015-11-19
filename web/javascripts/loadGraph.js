function loadGraph() {

    // Initialisation d'un graphe de force
    var force = d3.layout.force()
        .charge(-2000)
        .gravity(0.1)
        .linkDistance(150)
        .size([750, 470]);

    var tip = d3.tip()
        .attr('class', 'd3-tip')
        .html(function(d) { return "<span>" + d.name + "</span>"; });

    // Création du canevas et création du graphe à partir des données
    var svg = d3.select("#force-layout")
        .append("svg")
        .attr("width", 750)
        .attr("height", 470)
        .call(tip);

    d3.json("/tmp/search_result.json", function(error, graph) {

        if (error) throw error;

        force
            .nodes(graph.nodes)
            .links(graph.links)
            .start();

        var link = svg.selectAll(".link")
            .data(graph.links)
            .enter()
            .append("line")
            .attr("class", "link")

        var gnodes = svg.selectAll("g.gnode")
            .data(graph.nodes)
            .enter()
            .append("g")
            .classed("gnode", true)

        var node = gnodes.append("image")
            .attr("class", "node")
            .attr("xlink:href", function(d) { return d.url })
            .attr("width", 87)
            .attr("height", 150)
            .attr("x", -50)
            .attr("y", -50)
            .on('mouseover', tip.show)
            .on('mouseout', tip.hide)
            .call(force.drag);

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

}

