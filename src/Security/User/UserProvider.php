<?php 
namespace App\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


use App\Security\User\Aide;
use Kreait\Firebase;

class UserProvider implements UserProviderInterface
{

    /**
     * @var Firebase
     */
    private $firebase;
    public function __construct(Firebase $firebase)
    {
        $this->firebase = $firebase;
        
    }
    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    

    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Aide) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->fetchUser($user);
    }

    public function supportsClass($class)
    {
        return Aide::class === $class;
    }

    private function fetchUser($user)
    {
        // make a call to your webservice here

        $auth = $this->firebase->getAuth();
        $us = $auth->getUserByEmail($user->getEmail());
        
        
        if(empty($us)){
            $us=false;
        }
         
        // pretend it returns an array on success, false if there is no user

        if ($us) {
            $util = $this->information($us->uid);
            $email = $util['email'];
            $salt  = false;


            // ...
            $new = new Aide($util['displayName'],$email, $user->getPassword());
            $new->setUid($util['uid']);


            if (array_key_exists('photoUrl',$util)) {
                $new->setPhotoUrl($util['photoUrl']);

            } else {
                $new->setPhotoUrl("");

            }
            if (array_key_exists('phoneNumber',$util) ) {

                $new->setPhoneNumber($util['phoneNumber']);
            } else {

                $new->setPhoneNumber();
            }
            $new->setEmail($util['email']);
            $roles = array($util['compte']);
            $new->setRoles($roles);
            return $new ;
        }
            throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $user->getEmail()));
        
    }
    public function information($uid){
        $reference = "users/".$uid;
        $snapshot = $this->firebase->getDatabase()->getReference($reference)->getSnapshot();

        return $snapshot->getValue();
    }
}
