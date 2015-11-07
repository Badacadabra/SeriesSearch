<?php

namespace SmartSearch\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller
{
    public function homeAction()
    {
        return $this->render('SmartSearchSearchBundle:Search:index.html.twig', array());
    }
}
