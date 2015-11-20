function loadPieChart() {

    var pie = new d3pie("pie-chart", {
        "header": {
            "title": {
                "text": "Genres",
                "fontSize": 35,
                "font": "courier"
            },
            "subtitle": {
                "text": "Répartition *",
                "color": "#999999",
                "fontSize": 15,
                "font": "courier"
            },
            "location": "pie-center",
            "titleSubtitlePadding": 10
        },
        "footer": {
            "text": "* Déterminée à partir des résultats renvoyés pour la dernière requête",
            "color": "#999999",
            "fontSize": 15,
            "font": "courier",
            "location": "bottom-center"
        },
        "size": {
            "canvasHeight": 470,
            "canvasWidth": 750,
            "pieInnerRadius": "60%",
            "pieOuterRadius": "80%"
        },
        "data": {
            "sortOrder": "label-asc",
            "content": [
                {
                    "label": "Toto",
                    "value": 9,
                    "color": "#2383c1"
                },
                {
                    "label": "Tata",
                    "value": 7,
                    "color": "#64a61f"
                },
                {
                    "label": "Titi",
                    "value": 10,
                    "color": "#7b6788"
                },
                {
                    "label": "Tutu",
                    "value": 2,
                    "color": "#a05c56"
                },
                {
                    "label": "Tete",
                    "value": 7,
                    "color": "#961919"
                },
                {
                    "label": "Tyty",
                    "value": 3,
                    "color": "#d8d239"
                },
                {
                    "label": "Totoo",
                    "value": 5,
                    "color": "#e98125"
                },
                {
                    "label": "Tataa",
                    "value": 5,
                    "color": "#d0743c"
                },
                {
                    "label": "Titii",
                    "value": 1,
                    "color": "#6ada6a"
                },
                {
                    "label": "Tutuu",
                    "value": 1,
                    "color": "#0b6197"
                }
            ]
        },
        "labels": {
            "outer": {
                "format": "label-percentage1",
                "pieDistance": 20
            },
            "inner": {
                "format": "none"
            },
            "mainLabel": {
                "fontSize": 15
            },
            "percentage": {
                "color": "#999999",
                "fontSize": 13,
                "decimalPlaces": 0
            },
            "value": {
                "color": "#cccc43",
                "fontSize": 13
            },
            "lines": {
                "enabled": true,
                "color": "#777777"
            },
            "truncation": {
                "enabled": true
            }
        },
        "tooltips": {
            "enabled": true,
            "type": "placeholder",
            "string": "{label}: {value}, {percentage}%"
        },
        "effects": {
            "pullOutSegmentOnClick": {
                "speed": 400,
                "size": 10
            }
        },
        "misc": {
            "colors": {
                "segmentStroke": "#000000"
            }
        }
    });

}
