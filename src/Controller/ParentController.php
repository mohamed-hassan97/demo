<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParentController extends AbstractController
{
    /**
     * @Route("/parent", name="parent")
     */
    public function index()
    {
        $user = $this->getUser();
        return $this->render('profile/profil/parent.html.twig', [
            'parent' => $user
        ]);
    }
    /**
     * @Route("/parent/edit", name="parent_edit")
     */
    public function edit()
    {
        $user = $this->getUser();
        return $this->render('profile/profil/editparent.html.twig', [
            'parent' => $user
        ]);
    }
}

