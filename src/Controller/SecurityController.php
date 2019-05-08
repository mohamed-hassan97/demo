<?php

namespace App\Controller;


use App\Security\User\Aide;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use App\Controller\SecurityController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Kreait\Firebase; // Firebase
class SecurityController extends AbstractController
{
    /**
     * @var Firebase
     */
    private $firebase;

    public function __construct(Firebase $firebase){
        $this->firebase = $firebase;
    }

    // Inscription
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request)
    {
        

        /* declarer une variable qui garderas les info de l'user */
    	$user = new Aide(null,null,null);
        
        /** recuperer les informations du formulaire email,mot de passe etc.. */
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $role = $request->request->get('role');
        $sexe = $request->request->get('sexe');
        $username  = $request->request->get('username');
        $age = $request->request->get('age');
        $phone = $request->request->get('phone');

        
        if($email != null && $password != null){

            /* avoir les fonctions d'authentification */
            $auth = $this->firebase->getAuth();
            
            
            $r1=random_int(0,9);
            $r2=random_int(0,9);
            $r3=random_int(0,9);
            // regrouper tout les informations dans un array 
            $userProperties = [
                'email' => $email,
                'password' => $password,
                'displayName' => $username,
                'photoUrl' => 'https://picsum.photos/200/300/?random',
                'phoneNumber' => $phone
            ];

            
            $createdUser = (array)$auth->createUser($userProperties);
            $uid = $createdUser['uid'];
            $createdUser['password']=$password;


            $properties = null;
            switch ($sexe) {
                case 'masculin':
                    $properties['customAttributes']["sexe"]='masculin';
                    break;
                case 'feminin':
                    $properties['customAttributes']["sexe"]='feminin';
                    break;
                default:
                    # code...
                    break;
            }

            switch ($role) {
                case 'enfant':
                    $properties['customAttributes']["role"]=array('ROLE_ENFANT');
                    break;
                case 'parent':
                    $properties['customAttributes']["role"]=array('ROLE_PARENT');
                    break;
                
                default:
                    break;
            }
            $_SESSION["porp"]=$properties;

            $this->ajoutdatabase($uid,$createdUser,$properties);

            $updatedUser = $auth->updateUser($uid, $properties);
                    

            // redirection vers la page de LOGIN
            return $this->redirectToRoute('security_login');
    	}


        return $this->render('security/registration.html.twig');
    }


    /**
     * @Route("/forget",name="forget")
     */
    public function forget(Request $request){

        /* declarer une variable qui garderas les info de l'user */
    	$user = new Aide(null,null,null);
        
        /** recuperer les informations du formulaire email,mot de passe etc.. */
        $email = $request->request->get('email');

        if($email != null ){
            $auth = $this->firebase->getAuth();

            $exist  = $auth->getUserByEmail($email);

            if($exist){

                $auth->sendPasswordResetEmail($email);
                
            }
            

        }
        return $this->render("security/forget.html.twig");
    }
    

    


    /**
    * @Route("/connexion", name="security_login")
    */
    public function login(AuthenticationUtils $authenticationUtils ): Response{

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
    * @Route("/deconnexion", name="security_logout")
    */
    public function logout(){

    }

    public function ajoutdatabase($uid,$createdUser,$properties){
        $createdUser['compte']=$properties['customAttributes']['role'][0];
        $createdUser['sexe']=$properties['customAttributes']['sexe'];
        
        $reference = "users";
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();    
        $updates = [
            $uid.'/'=> $createdUser
        ];
        
        $snapshot->getReference()->update($updates);
    }

}
