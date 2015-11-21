$( document ).ready(function() {

    // Activation des listes déroulantes
    $('.ui.dropdown')
        .dropdown()
    ;

    // Activation et gestion des datepickers
    $( "#from" ).datepicker({ dateFormat: "yy-mm-dd" });
    $( "#to" ).datepicker({ dateFormat: "yy-mm-dd" });

    $( "#from" ).change(function() {
        var value = $( this ).val();
        $( "#form_keyword" ).val( "FROM:" + value );
    });

    $( "#to" ).change(function() {
        var value = $( this ).val();
        $( "#form_keyword" ).val( $( "#form_keyword" ).val() + " TO:" + value );
    });

    // Activation des tooltips personnalisées
    $( "[title!='']" ).qtip({
        style: {
            classes: 'qtip-light'
        }
    });

    // Activation du survol dynamique de la télécommande
    $( "#remote" ).maphilight();

    // Drag & drop sur la télécommande
    $( "#remote-wrapper" ).draggable({ containment: "parent", scroll: false });

    // Validation du formulaire de recherche
    $( "form" ).submit(function(e) {
        if ($( "#form_keyword" ).val() == "") {
            e.preventDefault();
            $( "#search" ).addClass( "error" );
            $( "#form_keyword" ).attr( "placeholder", "Veuillez entrer une requête." );
        }
    });

    $( "#form_keyword" ).keyup(function() {
        if ($( this ).val() != "") {
            $( "#search" ).removeClass( "error" );
            $( "#form_keyword" ).attr( "placeholder", "Une série vous tente ?" );
        }
    });

    // Activation de la timeline
    $().timelinr({
        prevButton: '#prev-date',
        nextButton: '#next-date'
    });

    // Gestion des clics sur les boutons simples
    var intermission = null;

    function playIntermission() {
        if(intermission == null) {
            intermission = new Audio('/sounds/intermission.ogg');
            intermission.play();
        }
    }

    $( "#on-off" ).click(function() {
        $( "#screen" ).fadeToggle();
        if ($( "#intermission" ).is(":visible")) {
            $( "#intermission" ).fadeToggle();
        }
        if (intermission != null) {
            intermission.pause();
            intermission = null;
        }
        var onOff = new Audio("/sounds/tv-on-off.ogg");
        onOff.play();
    });

    $( ".config" ).click(function() {
        $( "#intermission" ).show();
        playIntermission();
    });

    $( "#tv-config" ).click(function() {
        $( "#intermission" ).hide();
        if (intermission != null) {
            intermission.pause();
            intermission = null;
        }
    });

    $( "#mute" ).click(function() {
        var audio = $( "audio" );
        audio[0].pause();
        audio.removeAttr( "autoplay" );
        Cookies.set( "soundState", "off", { expires: 1 } );
    });

    $( "#sound" ).click(function() {
        var audio = $( "audio" );
        audio.attr( "autoplay", "autoplay" );
        audio[0].play();
        Cookies.set( "soundState", "on", { expires: 1 } );
    });

    // Gestion dynamique de l'affichage dans l'écran
    var screenButtons = ["search-engine-button", "serps-button", "force-layout-button", "timeline-button", "pie-chart-button", "menu-button", "help-button", "info-button"];
    var screenContents = ["search-engine", "serps", "force-layout", "timeline", "pie-chart", "menu", "help", "info", "intermission"];

    for (var i = 0; i < screenButtons.length; i++) {
        switchScreenContent(screenButtons[i], screenContents[i]);
    }

    function switchScreenContent(buttonClass, contentToShow) {
        $( "." + buttonClass ).click(function() {
            for (var i = 0; i < screenContents.length; i++) {
                if (screenContents[i] == contentToShow) {
                    $( "#" + screenContents[i] ).fadeIn();
                } else {
                    $( "#" + screenContents[i] ).hide();
                }
            }
            // Ajustement pour éventuellement couper le son de l'intermission
            if (intermission != null)
                intermission.pause();
        });
    }

    // Gestion plus fine de la visualisation
    $( ".force-layout-button" ).click(function() {
        if ($( "#force-layout" ).is( ":empty" )) {
            loadGraph();
        }
    });

    $( ".pie-chart-button" ).click(function() {
        if ($( "#pie-chart" ).is( ":empty" )) {
            loadPieChart();
        }
    });

    // Gestion des ambiances
    $( "body" ).vegas({
        delay: 20000,
        transition: 'zoomOut',
        slide: Cookies.get( "indexAmbiance" ),
        slides: [
            { src: "/images/home.jpg" },
            { src: "/images/science-fiction.jpg" },
            { src: "/images/war.jpg" },
            { src: "/images/horror.jpg" },
            { src: "/images/road.jpg" },
            { src: "/images/history.jpg" },
            { src: "/images/urban.jpg" },
            { src: "/images/fantasy.jpg" },
            { src: "/images/prison.jpg" },
            { src: "/images/romance.jpg" }
        ],
        animation: 'kenburns',
        walk: function (index, slideSettings) {
            // console.log("Slide index " + index + " image " + slideSettings.src);
            var audio = $( "audio" );

            $( "audio source" ).attr( "src", "/sounds/" + index + ".ogg");
            audio[0].pause();
            audio[0].load();

            Cookies.set( "indexAmbiance", index, { expires: 1 } );

            if (Cookies.get( "soundState" ) == "on") {
                audio.attr( "autoplay", "autoplay" );
                audio[0].play();
            }

        }
    });

    $( "body" ).vegas('pause');

    var actions = ["play", "pause", "previous", "next"];
    for (var i = 0; i < actions.length; i++) {
        slideshowControls(actions[i]);
    }

    function slideshowControls(action) {
        $( "#" + action ).click(function() {
            $( "body" ).vegas(action);
        });
    }

    var backgrounds = ["home", "sci-fi", "war", "horror", "road", "history", "urban", "fantasy", "prison", "romance"];
    for (var i = 0; i < backgrounds.length; i++) {
        slideshowNumbers(backgrounds[i], i);
    }

    function slideshowNumbers(backgroundName, backgroundIndex) {
        $( "." + backgroundName).click(function() {
            $( "body" ).vegas('jump', backgroundIndex);
        });
    }

    // Gestion de la date de crawl (dropdown)
    var selectedCrawlDate = $('.dropdown').dropdown('get value');

    $( ".dropdown .item" ).click(function() {
        selectedItem = $( this ).text();
        $('.dropdown').dropdown('set value', $.trim(selectedItem));
    });

    // Gestion de la scrollbar
    $( ".screen-content:not(.serps-button)" ).click(function() {
        $( "#screen" ).mCustomScrollbar( "disable", true );
    });

    $( ".serps-button" ).click(function() {
        $( "#screen" ).mCustomScrollbar( "update" );
    });

    $( "#screen" ).mCustomScrollbar({
        callbacks: {
            onUpdate:function() {
                if ( $( "#results" ).length || $( "#specific-series" ).length) {
                    $( "#screen" ).mCustomScrollbar( "scrollTo", "#serps");
                }
            }
        }
    });

});

// Configuration de maphilight
$.fn.maphilight.defaults = {
    fill: true,
    fillColor: '000000',
    fillOpacity: 0.3,
    stroke: true,
    strokeColor: 'ffffff',
    strokeOpacity: 1,
    strokeWidth: 3,
    fade: true,
    alwaysOn: false,
    neverOn: false,
    groupBy: false,
    wrapClass: true,
    shadow: false,
    shadowX: 0,
    shadowY: 0,
    shadowRadius: 6,
    shadowColor: '000000',
    shadowOpacity: 0.8,
    shadowPosition: 'outside',
    shadowFrom: false
}
