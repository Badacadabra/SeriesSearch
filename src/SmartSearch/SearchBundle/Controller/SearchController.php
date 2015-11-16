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

            return $this->redirect($this->generateUrl('smart_search_keyword', array("keyword" => $keywordCleaned)));
        }
        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', array("form" => $form->createView()));
    }



    /**
     * Retourne la liste des résultats pour une expression-clé donnée
     * @return $this
     * */
    public function searchAction(Request $request, $keyword)
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

            return $this->redirect($this->generateUrl('smart_search_keyword', array("keyword" => $keywordCleaned)));
        }

        $keywordArray = explode("+", $keyword);

        $keyword = str_replace("+", " ", $keyword);

        $dateCrawl = "2015-11-13";

        $results = $this->displayResults($keywordArray, $dateCrawl);

        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', array("form" => $form->createView(), "results" => $results, "keywordArray" => $keywordArray, "keyword" => $keyword));
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

}
