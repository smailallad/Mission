<?php
namespace AppBundle\Model;
 
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
 
class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Mot de passe actuel incorrect."
     * )
     */
    protected $oldPassword;
     
    protected $password;
             
     
    function getOldPassword() {
        return $this->oldPassword;
    }
 
    function getPassword() {
        return $this->password;
    }
 
    function setOldPassword($oldPassword) {
        $this->oldPassword = $oldPassword;
        return $this;
    }
 
    function setPassword($password) {
        $this->password = $password;
        return $this;
    }
}