Ce fichier vas nous servir de tuyau d'échange concernant les éléments externes au projet.
il sera supprimé à la fin du projet.

1) Néttoyage du cache

   j'ai créé un script "clear.sh" à la racine du projet, il contient les instructions 
   permettant de supprimer les fichiers cache et log, puis remettre les bons droits sur
   les répertoire par après (cela enfin d'éviter de les retapés à chaque néttoyage du cache).

2) Le virtualhost
   Pour éviter que les chemins statiques(images, css, js) changent chez chacun de nous, j'ai mis en place 
   un vhost grâce auquel tous nos chemins seront du style "/css/style.css". Vous trouverez ainsi ce vhost
   dans le répertoire "tmp" de la racine, le fichier s'appelle "smart-search.conf".

   La mise en place est très simple chez vous : 
   
   2.1 - Copiez le fichier "smart-search.conf" dans /etc/apache2/site-available/

   2.2 - Ouvrez ainsi le fichier avec un éditeur, et remplacez le chemin de la directive "DocumentRoot",
       ainsi celui de la deuxième directive "Directory", conformement à l'emplacement où vous aurait placer le projet, 
       exp : "/var/www/html/smart-search/web/". il faut inclure le répertoire web à la fin, enfin de pouvoir accéder 
       directement au contrôleur frontal "app_dev.php"

   2.3 - Ouvrez en mode sudo le fichier "/etc/hosts" et rajoutez y ce couple IP/DNS:
       127.0.0.1 www.smart-search.local

   2.4 - Une fois ces étapes terminées, revenez à la console et tapez la commande suivante : 
       "sudo a2ensite smart-search.conf" afin d'activer le site que vous venez de configurer.

   2.5 - Apache vous demandera normalement de récharger le serveur avec la commande "sudo service apache2 reload"

   2.6 - Accédez au site en tapant "www.smart-search.local" dans le navigateur. 
