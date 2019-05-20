<?php

namespace App\Security;

use App\Security\User\Aide;
use App\Security\User\UserProvider;
use Kreait\Firebase;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

class FirebaseAuthenticator extends AbstractFormLoginAuthenticator
{
use TargetPathTrait;

    private $userProvider;
    
    /**
     * @var Firebase
     */
    private $firebase;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(UserProviderInterface $userProvider,Firebase $firebase, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userProvider = $userProvider;
        $this->firebase = $firebase;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return 'security_login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }
    public function findfirebase($email, $password, $lockVersion = null)
    {
        $auth = $this->firebase->getAuth();
        
        $user=null;
        try {
            $us = $auth->verifyPassword($email, $password);
            $user = $this->information($us->uid);

            $new = new Aide($user['displayName'],$user['email'],$password);
            $new->setUid($user['uid']);

            if (array_key_exists('photoUrl',$user)   ) {
                $new->setPhotoUrl($user['photoUrl']);

            } else {
                $new->setPhotoUrl("");

            }

            if (array_key_exists('phoneNumber',$user)  ) {

                $new->setPhoneNumber($user['phoneNumber']);
            } else {

                $new->setPhoneNumber();
            }
            
            $new->setPhotoUrl();
            $roles = array($user['compte']);


            $new->setRoles($roles);


            $properties = [
                'metadata' => [
                    'lastLoginAt' => date('Y-m-d\TH:i:sP')
                ]
                
            ];
            // the last time user access 
            $auth->updateUser($user['uid'],$properties);
            
        
            return $new;
        } catch (InvalidPassword $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }
        
        
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        $auth = $this->firebase->getAuth();
        
        $user = $this->findfirebase($credentials['email'],$credentials['password']);
        
        
        
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }


        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        
       
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('profil'));
    }

    protected function getLoginUrl()
    {
        
        return $this->urlGenerator->generate('security_login');
    }

    public function information($uid){
        $reference = "users/".$uid;
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();

        return $snapshot->getValue();
    }

    
}
