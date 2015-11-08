/**
 * Classe Ajax : cette classe gère tout ce qui
 * interaction XmlHttpRequest avec le serveur
 */
var Ajax = {
		
	/**
	 * Permet de parser les données provenant du serveur
	 * @param mixe data : données à parser
	 * */
	parseResult : function(data) {
		
		$.each(data,function(key,val) {
			//Parsage des données ici
		})
	},
	/**
	 * Permet d'envoyer ou récupérer les données sur le serveur
	 * @param string url : url à laquelle la requête sera envoyée
	 * @param Object data : données à envoyer au serveur
	 * @param string requestType : le type de requête à envoyer (GET ou POST)
	 * @param String dataType: Type de format de parsage (json, xml etc)
	 * @param String callback : la fonction de callback à appeler une fois
	 * que la requête s'est exécutée avec succès. 
	 **/
	perform : function(url,data,requestType,dataType,callback) {
		$.ajax({
			  url: url,
			  type: requestType,
			  data: data,
			  dataType: dataType
			}).done(callback);	
	}
}
