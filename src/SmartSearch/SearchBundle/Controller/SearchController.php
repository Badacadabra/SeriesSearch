<?php

namespace SmartSearch\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Contrôleur principal du bundle Search
 * */
class SearchController extends Controller
{
    /**
     * Action de la page d'acceuil
     * @return $this
     * */
    public function homeAction(Request $request)
    {
        
        //$this->createIndexAction("2015-11-13");
        
        $form = $this->createFormBuilder()
            ->add('keyword', 'text', 
                array(
                    'constraints' => array(
                        new NotBlank(),
                        ),
                    'attr' => array(
                        'placeholder' => '',
                        )
                    )
                )
			->add('dateCrawl','choice', array('label'=>'Gender',
                'choices'   => $this->getDates(),   
            ))
            ->add('Go!', 'submit')
            ->getForm();

        $form->handleRequest($request);
		
        if ($form->isValid()) {

            $keyword = $form->get('keyword')->getData();
            $keywordArray = explode(" ", $keyword);
            $listeKeywords = array();
			
			
            foreach($keywordArray as $motcle) {
                $listeKeywords[] = $this->cleanContent($motcle);
            }

            $keywordCleaned = implode("+", $listeKeywords);
			$dateCrawl = $form->get('dateCrawl')->getData();
            return $this->redirect($this->generateUrl('smart_search_keyword', 
									array("keyword" => $keywordCleaned, "dateCrawl" => $dateCrawl)));
        }
        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', array("form" => $form->createView()));
    }



    /**
     * Retourne la liste des résultats pour une expression-clé donnée
     * @return $this
     * */
    public function searchAction(Request $request, $keyword, $dateCrawl)
    {

        $form = $this->createFormBuilder()
            ->add('keyword', 'text', 
                array(
                    'constraints' => array(
                        new NotBlank(),
                        ),
                    'attr' => array(
                        'placeholder' => '',
                        )
                    )
                )
			->add('dateCrawl','choice', array('label'=>'Gender',
                'choices'   => $this->getDates(),   
            ))
            ->add('Go!', 'submit')
            ->getForm();

        $form->handleRequest($request);
        //var_dump($form->get('dateCrawl')->getData());die;
        if ($form->isValid()) {

            $keyword = $form->get('keyword')->getData();
            
            $keywordArray = explode(" ", $keyword);
            $listeKeywords = array();

            foreach($keywordArray as $motcle) {
                $listeKeywords[] = $this->cleanContent($motcle);
            }
            
            $keywordCleaned = implode("+", $listeKeywords);
			$dateCrawl = $form->get('dateCrawl')->getData();
            return $this->redirect($this->generateUrl('smart_search_keyword',
								array(
										"keyword" => $keywordCleaned, 
										"dateCrawl" => $dateCrawl //Date de crawl provenant du formulaire
									)));
        }
        $keywordArray = explode("+", $keyword);


        $keyword = str_replace("+", " ", $keyword);

        $dateCrawl = "2015-11-13";

        $results = $this->displayResults($keywordArray, $dateCrawl);

        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', array("form" => $form->createView(), "results" => $results, "keywordArray" => $keywordArray, "keyword" => $keyword));

        //Condition pour les requêtes de type from:date to:date
        //"#^from:[a-z0-9-+]to:[a-z0-9]#i"
        //"#^(from|to)#i"
        if (preg_match("#^(from|to)#i",$keyword)) {
			$results = $this->displayResultsByCustomQuery($keywordArray);
		} else {
			$results = $this->displayResults($keywordArray, $dateCrawl);
		}
		//var_dump($results);die;
		$keyword = str_replace("+", " ", $keyword);
        //Formatage des données pour D3.js
		$this->generateGraphJsonFile($results,$keywordArray);
        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', 
								array(
									  "form" => $form->createView(), "results" => $results,
									  "keywordArray" => $keywordArray, "keyword" => $keyword,
									  "dateCrawl" => $dateCrawl //Date de crawl provenant du paramètre de la route
								));

    }
    // ***********************************
    // Méthode de création de l'index à partir des documents scrappés
    // ***********************************
    public function createIndexAction($dateinput)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new \DateTime($dateinput);

        $reviews = $em->getRepository('SmartSearchSearchBundle:Review')->findBy(array("dateCrawl" => $date), array(), 1000);
        $collection = array();

        foreach($reviews as $review) {
            $txt = $this->cleanContent($review->getTitle()).$this->cleanContent($review->getContent());
            $collection[$review->getId()] = $txt;
        }

        $dictionary = array();
        $docCount = array();

        foreach($collection as $idReview => $review) {

            $terms = explode(' ', $review);
            $docCount[$idReview] = count($terms);

            foreach($terms as $term) {

                if(!isset($dictionary[$term])) {
                    $dictionary[$term] = array('df' => 0, 'postings' => array());
                }

                if(!isset($dictionary[$term]['postings'][$idReview])) {
                    $dictionary[$term]['df']++;
                    $dictionary[$term]['postings'][$idReview] = array('tf' => 0);
                }

                $dictionary[$term]['postings'][$idReview]['tf']++;

            }

        }

        $index = array('docCount' => $docCount, 'dictionary' => $dictionary);

        $indexJson = json_encode($index); // On encode les résultats en JSON 
        $fp = fopen(__DIR__.'/../../../../web/docs/index_'.$dateinput, 'w');
        fwrite($fp, serialize($indexJson)); // On serialize les donnes pour gagner en rapidité et on les écrit dans le fichier index.json
        fclose($fp);

        return $this->redirect($this->generateUrl('smart_search_homepage', array()));

    }


    // ***********************************
    // Méthode de récupération de l'index
    // ***********************************
    private function getIndex($dateCrawl)
    {
        $indexJson = file_get_contents(__DIR__."/../../../../web/docs/index_".$dateCrawl);
        $index = json_decode(unserialize($indexJson));
        return $index;
    }


    // ***********************************
    // Méthode de classement des résultats
    // ***********************************
    private function getResults($query, $dateCrawl)
    {
        // $index = $this->createIndex($dateCrawl);
        $index = $this->getIndex($dateCrawl);
        // var_dump($index);
        $matchDocs = array();
        $docCount = count($index->docCount);

        $return = "";
        foreach($query as $term) {

            if(isset($index->dictionary->$term)) {

                $entry = $index->dictionary->$term;

                foreach($entry->postings as $idReview => $posting) {

                    if(!isset($matchDocs[$idReview])) {
                        $matchDocs[$idReview] = $posting->tf * log($docCount + 1 / $entry->df + 1, 2);
                    } else {
                        $matchDocs[$idReview] += $posting->tf * log($docCount + 1 / $entry->df + 1, 2);
                    }
                }
            }
        }  

        // Normalisation
        foreach($matchDocs as $idReview => $score) {
                $matchDocs[$idReview] = $score/$index->docCount->$idReview;
        }

        arsort($matchDocs); // On tri par ordre décroissant
        $results = array_slice($matchDocs, 0, 10, true); // On ne garde que le TOP 10.
        return $results;
    }

    

    // ***********************************
    // Méthode pour récupérer la liste des entités à partir des résultats obtenus par la méthode getResults()
    // ***********************************
    private function displayResults($query, $dateCrawl) 
    {
        $em = $this->getDoctrine()->getManager();

        $results = $this->getResults($query, $dateCrawl);
        $reviews = array();

        foreach($results as $idReview => $score) {

            $review = $em->getRepository('SmartSearchSearchBundle:Review')->find($idReview);
            $serie = $em->getRepository('SmartSearchSearchBundle:Serie')->findOneBy(array('name' => $review->getNameSerie()));

            $reviews[] = array($review, $serie);

        }

        return $reviews;
    }


    // ***********************************
    // Méthode pour nettoyer du contenu (suppression des virgules, des points et transformation du texte en minuscule)
    // ***********************************
    private function cleanContent($txt)
    {
        $txtCleaned = mb_strtolower($txt, 'UTF-8');
        $txtCleaned = $this->stripAccents($txtCleaned);
        $txtCleaned = str_replace(",", "", $txtCleaned);
        $txtCleaned = str_replace(".", "", $txtCleaned);
        
        return $txtCleaned;
    }


    // ***********************************
    // Méthode pour remplacer les caractères accentués par leur équivalent sans accent
    // ***********************************
    private function stripAccents($str, $encoding='utf-8'){

        // transformer les caractères accentués en entités HTML
        $str = htmlentities($str, ENT_NOQUOTES, $encoding);
     
        // remplacer les entités HTML pour avoir juste le premier caractères non accentués
        // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
        $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
     
        // Remplacer les ligatures tel que : Œ, Æ ...
        // Exemple "Å“" => "oe"
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        // Supprimer tout le reste
        $str = preg_replace('#&[^;]+;#', '', $str);
     
        return $str;
    }
    /**
     * Permet de renvoyer les dates de crawl
     * */
    private function getDates()
    {
		/*$em = $this->getDoctrine()->getManager();
        $listDates = $em->getRepository('SmartSearchSearchBundle:Review')->findDistinctDate();
        $dates = array();
        foreach($listDates as $date) {
			$formatedDate = $date['dateCrawl']->format('Y-m-d');
			$dates[$formatedDate] = $formatedDate; //Pour avoir la date comme value dans les balises "option" de la select list.
		}*/
		$dates = array("2015-11-13" => "2015-11-13","2015-11-10" => "2015-11-10");
		return $dates;
	}
	/**
	 * Permet d'afficher site des critiques
	 * @param int id : l'identifiant de la critique
	 * @param dateCrawl : la date du crawl, enfin de pouvoir
	 * récupérer le bon fichier html pour chaque critique
	 * */
	public function displayReviewAction($id,$dateCrawl) 
	{
		$review = $this->getDoctrine()->getRepository("SmartSearchSearchBundle:Review")->findOneBy(array("idReview" =>$id ));
		$template = $review->getFile($dateCrawl);
		ob_start();
		require_once($template);
		$html = ob_get_contents();
		ob_end_clean();
		return new Response($html);
	}
	public function graphAction()
	{
		return $this->render("SmartSearchSearchBundle:Search:result-graph.html.twig",array());
	}
	/**
	 * Permet de créer le fichier json pour le graphe
	 * @param array $results : le résultat à partir du 
	 * quel le fichier json sera créé
	 * @param string $keywordArray : mot clé déclencheur de la requête
	 * */
	public function generateGraphJsonFile(array $results, $keywordArray)
	{
		if (sizeof($results) > 0) {
			$nodes = array();
			$nodes[] = array("name" => implode(" ",$keywordArray));
			$links = array();
			$j = 1;
			for( $i =0; $i < sizeof($results); $i++ ) {
				$nodes[] = array("name" => $results[$i][0]->getTitle());
				$links[] = array("source" => 0, "target" => $j++ );
			}
			$data = array("nodes" => $nodes, "links"=> $links);
			file_put_contents(__DIR__.'/../../../../web/tmp/search_result.json', json_encode($data));
		}
	}
	/**
	 * Renvoie le résultat des requêtes du type from:Startdate to:dateEndDate
	 * @param array $query : la requête à analyser
	 * */
	public function displayResultsByCustomQuery($query)
	{
		$customQueryFromSide = explode(":",$query[0])[1]; //La partie "from" de la requête
		$customQueryToSide = explode(":",$query[1])[1]; //La partie "to" de la requête
		$reviewRepository = $this->getDoctrine()->getRepository('SmartSearchSearchBundle:Review');
		$reviews = $reviewRepository->findByCustomDate($customQueryFromSide,$customQueryToSide);
		$data = array();
		if (sizeof($reviews) > 0) {
			foreach ($reviews as $review) {
				$serie = $this->getDoctrine()
							  ->getRepository('SmartSearchSearchBundle:Serie')
							  ->findOneBy(array('name' => $review->getNameSerie()));
				$data[] = array($review, $serie);
			}
		} 
		return $data;
	}
}
