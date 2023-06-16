<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'acceuil')]
    public function acceuil(): Response
    {
        if( $this->security->getUser()){
            $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
    
                return $this->render('/homepage.html.twig', [
                    'isAdmin' => $isAdmin,

                ]);
        }else{
                return $this->render('/homepage.html.twig', [
                ]);
            }
        
    }

}