<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


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


    #[Route('/blank', name: 'bobo')]
    public function dadventisinput(): Response
    {
        return $this->render('pages/pages-blank.html.twig');
    }

    #[Route('/adventisinput', name: 'adventisinput')]
    public function adventisinput(): Response
    {
        return $this->render('pages/adventis_input.html.twig');
    }

    #[Route('/calendar', name: 'calendar')]
    public function calendar(): Response
    {
        return $this->render('pages/calendar.html.twig');
    }

    #[Route('/charts', name: 'charts')]
    public function charts(): Response
    {
        return $this->render('pages/charts.html.twig');
    }

    #[Route('/client', name: 'client')]
    public function client(): Response
    {
        return $this->render('pages/clients.html.twig');
    }

    #[Route('/colum', name: 'colum')]
    public function colum(): Response
    {
        return $this->render('pages/colum.html.twig');
    }

    #[Route('/dashbord', name: 'dashbord')]
    public function dashbord(): Response
    {
        return $this->render('pages/dashbord.html.twig');
    }

    #[Route('/datable', name: 'datable')]
    public function datable(): Response
    {
        return $this->render('pages/datable.html.twig');
    }

    #[Route('/ecommerce', name: 'ecommerce')]
    public function ecommerce(): Response
    {
        return $this->render('pages/e-commerce.html.twig');
    }

    #[Route('/formlayout', name: 'formlayout')]
    public function formlayout(): Response
    {
        return $this->render('pages/formlayout.html.twig');
    }

    #[Route('/invoices', name: 'invoices')]
    public function invoices(): Response
    {
        return $this->render('pages/invoices.html.twig');
    }

    #[Route('/multisection', name: 'multisection')]
    public function multisection(): Response
    {
        return $this->render('pages/multi-section.html.twig');
    }

    #[Route('/notification', name: 'notification')]
    public function notification(): Response
    {
        return $this->render('pages/notification.html.twig');
    }

    #[Route('/oders', name: 'oders')]
    public function oders(): Response
    {
        return $this->render('pages/oders.html.twig');
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('pages/profile.html.twig');
    }

    #[Route('/project', name: 'project')]
    public function project(): Response
    {
        return $this->render('pages/project.html.twig');
    }

    #[Route('/setting', name: 'setting')]
    public function setting(): Response
    {
        return $this->render('pages/settings.html.twig');
    }

    #[Route('/table', name: 'table')]
    public function table(): Response
    {
        return $this->render('pages/table.html.twig');
    }

    #[Route('/task', name: 'task')]
    public function task(): Response
    {
        return $this->render('pages/task.html.twig');
    }

}