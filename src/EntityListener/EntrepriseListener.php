<?php

namespace App\EntityListener;

use App\Entity\Entreprise;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EntrepriseListener
{
    private UserPasswordHasherInterface $hasher; 

    public  function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }   

    public function prePersist(Entreprise $entreprise)
    {
        $this->encodePassword($entreprise);
    }

    public function preUpdate(Entreprise $entreprise)
    {
        $this->encodePassword($entreprise);
    }

    
    /**
     * Encode password based un plainpassword
     *
     * @param Entreprise $entreprise
     * @return void
     */
    public function encodePassword(Entreprise $entreprise)
    {
        if($entreprise->getPlainPassword() === null){
             return;
        }

        $entreprise->setPassword(
            $this->hasher->hashPassword(
                $entreprise,
                $entreprise->getPlainPassword()
            )
            );
            $entreprise->setPlainPassword(null);
    }
}



?>