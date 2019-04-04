<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    
    /**
    * @Route("/profile", name="profile")
    */
    public function profile(){

        $user = $this->getUser();


        return $this->render('profile/profile.html.twig',['parent'=>$user]);
    }
    /**
    * @Route("/profil", name="profil")
    */
    public function profil(){

        $user = $this->getUser();


        return $this->render('profile/profil.html.twig',['parent'=>$user]);
    }
}
