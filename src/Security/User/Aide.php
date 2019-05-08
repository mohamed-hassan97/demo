<?php

namespace App\Security\User;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
//use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this username")
 */
class Aide implements UserInterface,\Serializable,EquatableInterface
{

    
    private $uid;

      
      private $id;

      private $username;
      
      private $email;
  
      
      private $emailVerified;
  
      
      private $phoneNumber;
  
      
      private $password;
  
      
      private $displayName;
  
      
      private $photoUrl;
  
      
      private $disabled;

      private $salt;
  
  
      /**
       * @var array
       */
      private $roles;
  
      public function __construct($username,$email,$password, $salt=false, array $roles=['ROLE_USER'])
      {

        $this->username =$username;
        $this->displayName=$username;
        $this->email =$email;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;

      }
  
      public function getUsername(): ?string
      {
          return $this->username;
      }
  
      public function setUsername(string $username): self
      {
        $this->username = $username;
  
        return $this;
      }
      
  
  
      public function getId(): ?int
      {
          return $this->id;
      }
  
      public function getEmail(): ?string
      {
          return $this->email;
      }
  
      public function setEmail(string $email): self
      {
          $this->email = $email;
  
          return $this;
      }
  
      public function getEmailVerified(): ?bool
      {
          return $this->emailVerified;
      }
  
      public function setEmailVerified(bool $emailVerified): self
      {
          $this->emailVerified = $emailVerified;
  
          return $this;
      }
  
      public function getPhoneNumber(): ?int
      {
          return $this->phoneNumber;
      }
  
      public function setPhoneNumber(?int $phoneNumber): self
      {
          $this->phoneNumber = $phoneNumber;
  
          return $this;
      }
  
      public function getPassword(): ?string
      {
          return $this->password;
      }
  
      public function setPassword(string $password): self
      {
          $this->password = $password;
  
          return $this;
      }
  
      public function getDisplayName(): ?string
      {
          return $this->displayName;
      }
  
      public function setDisplayName(string $displayName): self
      {
          $this->displayName = $displayName;
  
          return $this;
      }
  
      public function getPhotoUrl(): ?string
      {
          return $this->photoUrl;
      }
  
      public function setPhotoUrl(string $photoUrl): self
      {
          $this->photoUrl = $photoUrl;
  
          return $this;
      }
  
      public function getDisabled(): ?bool
      {
          return $this->disabled;
      }
  
      public function setDisabled(bool $disabled): self
      {
          $this->disabled = $disabled;
  
          return $this;
      }
  
      public function getUid(): ?string
      {
          return $this->uid;
      }
  
      public function setUid(?string $uid): self
      {
          $this->uid = $uid;
  
          return $this;
      }
  
      
  
      public function getSalt(){
        return $this->salt;
      }
  
      public function eraseCredentials(){
  
      }

      public function setRoles(array $role){
          $this->roles = $role;
        return $this;
      }
      public function getRoles(){
        return $this->roles;
      }
      public function serialize()
    {
        return serialize(array(
            $this->uid,
            $this->username,
            $this->email,
            $this->salt,
            $this->password,
            $this->roles,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->uid,
            $this->username,
            $this->email,
            $this->salt,
            $this->password,
            $this->roles,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Aide) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
      
  
}
