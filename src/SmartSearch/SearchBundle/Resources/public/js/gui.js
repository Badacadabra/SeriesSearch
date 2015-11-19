$( document ).ready(function() {

    // Activation des listes déroulantes
    $('.ui.dropdown')
        .dropdown()
    ;

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

    // Gestion des clics sur les boutons simples
    $( "#on-off" ).click(function() {
        $( "#screen" ).fadeToggle();
    });

    $( ".config" ).click(function() {
        $( "#intermission" ).show();
    });

    $( "#tv-config" ).click(function() {
        $( "#intermission" ).hide();
    });

    // Gestion dynamique de l'affichage dans l'écran
    var screenButtons = ["search-engine-button", "menu-button", "help-button", "info-button"];
    var screenContents = ["search-engine", "menu", "help", "info", "intermission"];

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
        });
    }

    $( "#reload" ).click(function() {
        location.reload();
    });

    // Gestion du fond d'écran
    $( "body" ).vegas({
        delay: 20000,
        transition: 'zoomOut',
        slides: [
            { src: "../images/home.jpg" },
            { src: "../images/science-fiction.jpg" },
            { src: "../images/war.jpg" },
            { src: "../images/horror.jpg" },
            { src: "../images/road.jpg" },
            { src: "../images/history.jpg" },
            { src: "../images/urban.jpg" },
            { src: "../images/fantasy.jpg" },
            { src: "../images/prison.jpg" },
            { src: "../images/romance.jpg" }
        ],
        animation: 'kenburns'
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
    console.log(selectedCrawlDate);

    $( ".dropdown .item" ).click(function() {
        selectedItem = $( this ).text();
        $('.dropdown').dropdown('set value', $.trim(selectedItem));
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
