<?php

namespace SmartSearch\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur principal du bundle Search
 * */
class SearchController extends Controller
{
    /**
     * Action de la page d'acceuil
     * @return $this
     * */
    public function homeAction()
    {
        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', array());
    }
    /**
     * Gère les événements du moteur de recherche
     * @param string $term : le terme à rechercher
     * @return Symfony\Component\HttpFoundation\Response
     * */
    public function ajaxSearchEngineAction($term)
    {
       $fileContent = file_get_contents(__DIR__."/../../../../web/dictionnaire.json");
       $res = json_decode($fileContent,true);
       $data = array();
       for ($i = 0; $i < 77; $i++) {
		   if (stristr($res[$i],$term)) {
			   $row = array('name' => $res[$i], 'id' => $i);
			   $data[] = $row; 
		   }
	   }
       return new Response(json_encode($data));
    }
}
