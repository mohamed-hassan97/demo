<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Kreait\Firebase; // Firebase
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Database;

class EnfantController extends AbstractController
{
    /**
    * @var Firebase
    */
   private $firebase;

   public function __construct(Firebase $firebase){
       $this->firebase = $firebase;
   }

   /**
    * @Route("/ajoutenfant", name="ajoutenfant")
    */
   public function addenfant(Request $request)
   {
       $database = new Database($this->firebase);

       /* declarer une variable qui garderas les info de l'enfant */

       
       /* recuperer les informations du formulaire email,mot de passe etc.. */
       $identifiant = $request->request->get('uidenfant');


       
       if($identifiant != null){

           /* avoir les fonctions d'authentification */
           $auth = $this->firebase->getAuth();

           // verifie si l'enfant existe 
           $enfant = $auth->getUser($identifiant);

           //prend l'uid du pere
           $uidpere = $this->getUser()->getUid();
           

           $database->addchild($uidpere,$identifiant);
           


           // redirection vers la page de LOGIN
           return $this->redirectToRoute('enfant');
       }
       $user = $this->getUser();
       return $this->render('profile/profile/ajout.html.twig',['parent'=>$user]);
   }

    /**
    * @Route("/enfant/{id}", name="enfant_show")
    */
   public function show($id){
        $database = new Database($this->firebase);
        $user = $this->getUser();
        $info_enfant = $database->infochild($id);
        if( $info_enfant  != null){
            return $this->render('profile/profil/enfant.html.twig',['parent'=>$user,"key"=>$info_enfant['key'],"enfant"=>$info_enfant['value'] ]);
        }
        else{
            return $this->json("c'est pas votre enfant");
        }
   }

   /**
    * @Route("/enfant/{id}/edit", name="enfant_edit")
    */
    public function edit($id,Request $request){
        $database = new Database($this->firebase);
        $user = $this->getUser();
        $info_enfant = $database->infochild($id);
        if($info_enfant){
        /* recuperer les informations du formulaire email,mot de passe etc.. */
       $nom = $request->request->get('username');
       $email = $request->request->get('email');
       $number = $request->request->get('number');
       $sexe = $request->request->get('sexe');


       
       if($nom != null || $email != null || $number != null || $sexe != null ){

           /* avoir les fonctions d'authentification */
           $auth = $this->firebase->getAuth();

           $change = array();

           if($nom != null){
               $change["displayName"]= $nom;
           }
           if($email != null){
               $change["email"]= $email;
           }
           if($number != null){
               $change["phoneNumer"]= $number;
           }
           if($sexe  != null){
               $change["customAttributes"]["sexe"]= $sexe ;
           }
           $change["customAttributes"]["role"]= ["ROLE_ENFANT"] ;
           $enfant_ch = $auth->updateUser($id,$change);
           $database->updatechild($id,$enfant_ch);
           
           


           // redirection vers la page de LOGIN
           return $this->redirectToRoute('enfant_show',["id"=>$id]);
        }
        else{
            return $this->json("c'est pas votre enfant");
        }
       }

        
        return $this->render('profile/profil/editenfant.html.twig',['parent'=>$user,"key"=>$info_enfant['key'],"enfant"=>$info_enfant['value'] ]);
   }
   
   
   /**
    * @Route("/enfant/{id}/delete", name="enfant_delete")
    */
    public function delete($id){
        $database = new Database($this->firebase);


        $user = $this->getUser();

        $database->deletechild($user->getUid(),$id);

        
        return $this->redirectToRoute("enfant");
   }
  
}
