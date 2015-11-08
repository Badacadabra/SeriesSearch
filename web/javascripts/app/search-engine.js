/**
 * Classe SearchEngine : Gère les moteurs de recherche
 */
SearchEngine = {
		
		/**
		 * Permet de définir un champ input en tant que moteur de recherche
		 * @param HTMLObject elmt  : l'élément input sur lequel la recherche vas s'effectuer
		 * @param string requestUrl : l'url à intérroger pour la récupération
		 * des données à afficher dans le moteur de recherche
		 * @param HTMLObject valueElmt : l'elément html qui contiendra la
		 * valeur de l'élement sélectionné dans la liste de suggestion
		 */
		search : function(elmt, requestUrl, valueElmt) {
			$(elmt).autocomplete({
			       source: function (request, response) {
			               $.ajax({
			                   url: requestUrl+request.term,
			                   type: 'GET',
			                   dataatType: 'json',
			                   success: function(data) {
			                       console.log("request ok");
			                       response($.map(data, function(item) {
			                           console.log("request ok");
			                           return {
			                               label: item.name,
			                               value: item.id
			                           }
			                       }));
			                   }
			               });
			           },
			           minLength: 1,
			           select: function (event, ui) {
			               event.preventDefault();
			               $(valueElmt).val(ui.item.value);                    
			               $(this).val(ui.item.label);  
			           },
			           focus: function( event, ui ) {
			        	   event.preventDefault();
			           }
			       });
		}
}
