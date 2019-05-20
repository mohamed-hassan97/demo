<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/",name="home")
    */
    public function home(){
    	return $this->render('base.html.twig');
    }

    /**
     * @Route("/telechargement",name="tele")
    */
    public function essi(){
    	return $this->render('accueil/index.html.twig');
    }
     /**
     * @Route("/contact",name="contact")
    */
    public function contact(){
    	return $this->render('accueil/contact.html.twig');
    }
}
