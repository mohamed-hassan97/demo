<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Security\User\Aide;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Database;
use Kreait\Firebase; // Firebase

/**
  * Require ROLE_ADMIN pour *tout* acceder a ce controller.
  *
 */
class ProfileController extends AbstractController
{

    /**
     * @var Firebase
     */
    private $firebase;


    public function __construct(Firebase $firebase){
        $this->firebase = $firebase;
        
    }
    /**
    * @Route("/profile", name="profile")
    */
    public function profile(){

        $user = $this->getUser();

        $uid = $user->getUid();
        
        $all_uid_enfant = $this->allchildof($uid);

        

        //initinaliser tableau vide
        $allinfo =array();

        // l'information de chaque enfant 
        foreach ($all_uid_enfant as $enfant) {
            $info = $this->infochild($enfant);
            $allinfo[$info['key']]= $info['value'];
        }
        
        return $this->json($allinfo);
    }
    /**
    * @Route("/map", name="map")
    */
    
    public function map(){

        $user = $this->getUser();
        $uid = $user->getUid();

        $value = $this->allchildof($uid);

        $info =array();
        if (empty($value)) {

            return $this->redirectToRoute("ajoutenfant");
        } else {

            return $this->render('profile/profile/map.html.twig',['parent'=>$user]);
        }
    }
    /**
    * @Route("/profil", name="profil")
    */
    public function profil(){

        $user = $this->getUser();

        

        
        return $this->render('profile/profile/principal.html.twig',['parent'=>$user]);
    }
    /**
    * @Route("/all", name="all")
    */
    public function all(){
        
        $user = $this->getUser();
        $uid = $user->getUid();
        
        $all_uid_enfant = $this->allchildof($uid);



        //initinaliser tableau vide
        $allinfo =array();

        // l'information de chaque enfant 
        foreach ($all_uid_enfant as $enfant) {
            $info = $this->infochild($enfant);
            $allinfo[$info['key']]= $info['value'];
        }
        
        return $this->json($allinfo);
    }
    /**
    * @Route("/enfant", name="enfant")
    */
    public function enfant(){

        $user = $this->getUser();
        $uid = $user->getUid();

        $value = $this->allchildof($uid);

        $allinfo =array();
        if (empty($value)) {
            return $this->redirectToRoute("ajoutenfant");
        } else {
            foreach ($value as $enfant) {
                $info = $this->infochild($enfant);
                $allinfo[$info['key']]= $info['value'];
            }
            
            return $this->render('profile/profile/information.html.twig',['parent'=>$user,'children'=>$allinfo]);
            
        }
        
        
    }
    public function allchildof($uidparent){

        $reference = 'users/'.$uidparent.'/enfant';
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();
        
        
        return $snapshot->getValue();
    }
    public function infochild($uidchild){
        $reference = 'users/'.$uidchild;
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();
        
        $key =  $snapshot->getKey();
        
        $enfant = ["key"=>$key,"value"=>$snapshot->getValue()];
        return $enfant;
    }
}
