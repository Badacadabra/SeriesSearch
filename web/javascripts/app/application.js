/**
 * Classe Principale de l'application
 * Permet d'initialiser les différentes classes indépendantes
 */
var App = {
	/**
	 * Initialisation des fonctions de la classe
	 */
	init : function() {
		SearchEngine.search("#main-search-engine",'/app_dev.php/ajax/search/',"#result-val");
		//ajax.perform()
	}
}
/**
 * Lancement de l'application
 * */
$(document).ready(function() {
	App.init();
});
