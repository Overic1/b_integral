<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
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

     #[Route('/entreprise/acceuil', name: 'index')]
    public function index(HttpClientInterface  $httpClient): Response
    {
        // $apiKey = '4NURInpqF2Rl47v1tcyAqQcN2S695GfL98kWhU5X3faWS5dVOz0y4AjPz22k25QtN4';
                
        // $url = 'https://erp.myn2a.online/laurexstore/htdocs/api/index.php/invoices';
        $user = $this->security->getUser();
        $apiKey = '';
        $url = ''; 
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl() . 'index.php/';
        }

        $responsefact = $httpClient->request('GET', $url . 'invoices' . '?DOLAPIKEY=' . $apiKey);
        $responseBon = $httpClient->request('GET', $url . 'orders' . '?DOLAPIKEY=' . $apiKey);
        $responsePro = $httpClient->request('GET', $url . 'products' . '?DOLAPIKEY=' . $apiKey);
        $responseEn = $httpClient->request('GET', $url . 'warehouses' . '?DOLAPIKEY=' . $apiKey);
        $responseColl = $httpClient->request('GET', $url . 'users' . '?DOLAPIKEY=' . $apiKey);
        $responseTiers = $httpClient->request('GET', $url . 'thirdparties' . '?DOLAPIKEY=' . $apiKey);
        $responseProCom = $httpClient->request('GET', $url . 'proposals' . '?DOLAPIKEY=' . $apiKey);
        $responseBanCai = $httpClient->request('GET', $url . 'bankaccounts' . '?DOLAPIKEY=' . $apiKey);
       
        $statusCode1 = $responsefact->getStatusCode();
        $statusCode2 = $responseBon->getStatusCode();
        $statusCode3 = $responsePro->getStatusCode();
        $statusCode4 = $responseEn->getStatusCode();
        $statusCode5 = $responseColl->getStatusCode();
        $statusCode6 = $responseTiers->getStatusCode();
        $statusCode7 = $responseProCom->getStatusCode();
        $statusCode8 = $responseBanCai->getStatusCode();
        
        if ($statusCode1 === 200 ) {
            $factNonPaye = 0;
            $jsonData = $responsefact->toArray();
            foreach ($jsonData as $jsonData) {
                if($jsonData['paye'] == 0){
                    $factNonPaye = $factNonPaye + 1;
                }
            }
            $factCount = count($jsonData);
        }else{
            $factCount = 0;
        }
        if ($statusCode2 === 200) {
            $jsonData = $responseBon->toArray();
            $BonCount = count($jsonData);
        } 
        else {
            $BonCount = 0;
        }
        if ($statusCode3 === 200) {
            $nbProNoStock = 0;
            $jsonData = $responsePro->toArray();
            $ProCount = count($jsonData);
            for ($i = 0; $i < count($jsonData); $i++) {
                // dd( $ProCount[$i]['stock_reel']);
                $stockPro = $jsonData[$i]['stock_reel'];
                if($stockPro == 0){
                    $nbProNoStock = $nbProNoStock + 1;
                }
            }
        } else {
            $ProCount = 0;
        }
        if ($statusCode4 === 200) {
            $jsonData = $responseEn->toArray();
            $EnCount = count($jsonData);
        } else {
            $EnCount = 0;
        }
        if ($statusCode5 === 200) {
            $jsonData = $responseColl->toArray();
            $CollCount = count($jsonData);
        } else {
            $CollCount = 0;
        }
        // if ($statusCode6 === 200) {
        //     $jsonData = $responseTiers->toArray();
        //     $TiersCount = count($jsonData);
        // } else {
        //     $TiersCount = 0;
        // }
        if ($statusCode7 === 200) {
            $jsonData = $responseProCom->toArray();
            $ProComCount = count($jsonData);
        } else {
            $ProComCount = 0;
        }
        if ($statusCode8 === 200) {
            $jsonData = $responseBanCai->toArray();
            $BanCaiCount = count($jsonData);
        } else {
            $BanCaiCount = 0;
        }

        // *********************************
        
        if ($statusCode6 === 200) {
            $jsonData = $responseTiers->toArray();
            $TiersCount = count($jsonData);
            $countprospect = 0;
            $countfour = 0;
            $countclient = 0;
            for ($i = 0; $i < count($jsonData); $i++) {
                $prospect = $jsonData[$i]['prospect'];
                $fournisseur = $jsonData[$i]['fournisseur'];
                $client = $jsonData[$i]['client'];
                
                if ($prospect == 1) {
                    $countprospect = $countprospect + 1;
                }
                if ($fournisseur == 1) {
                    $countfour = $countfour + 1;
                }
                if ($client == 1) {
                    $countclient = $countclient + 1;
                }
            }
        }

        
        return $this->render('pages/index.html.twig', [
            'factCount' => $factCount,
            'BonCount' => $BonCount,
            'ProCount' => $ProCount,
            'EnCount' => $EnCount,
            'CollCount' => $CollCount,
            'TiersCount' => $TiersCount,
            'ProComCount' => $ProComCount,
            'BanCaiCount' => $BanCaiCount,
            'countprospect' => $countprospect,
            'countfour' => $countfour,
            'countclient' => $countclient,
            'factNonPaye' => $factNonPaye,
            'nbProNoStock' => $nbProNoStock
        ]);
    }


    #[Route('/entreprise/adventisinput', name: 'adventisinput')]
    public function adventisinput(): Response
    {
        return $this->render('pages/adventis_input.html.twig');
    }

    #[Route('/entreprise/calendar', name: 'calendar')]
    public function calendar(): Response
    {
        return $this->render('pages/calendar.html.twig');
    }

    #[Route('/entreprise/charts', name: 'charts')]
    public function charts(): Response
    {
        return $this->render('pages/charts.html.twig');
    }

    #[Route('/entreprise/client', name: 'client')]
    public function client(): Response
    {
        return $this->render('pages/clients.html.twig');
    }

    #[Route('/entreprise/colum', name: 'colum')]
    public function colum(): Response
    {
        return $this->render('pages/colum.html.twig');
    }

    #[Route('/entreprise/dashbord', name: 'dashbord')]
    public function dashbord(): Response
    {
        return $this->render('pages/dashbord.html.twig');
    }

    #[Route('/entreprise/datable', name: 'datable')]
    public function datable(): Response
    {
        return $this->render('pages/datable.html.twig');
    }

    #[Route('/entreprise/ecommerce', name: 'ecommerce')]
    public function ecommerce(): Response
    {
        return $this->render('pages/e-commerce.html.twig');
    }

    #[Route('/entreprise/formlayout', name: 'formlayout')]
    public function formlayout(): Response
    {
        return $this->render('pages/formlayout.html.twig');
    }

    #[Route('/entreprise/invoices', name: 'invoices')]
    public function invoices(): Response
    {
        return $this->render('pages/invoices.html.twig');
    }

    #[Route('/entreprise/multisection', name: 'multisection')]
    public function multisection(): Response
    {
        return $this->render('pages/multi-section.html.twig');
    }

    #[Route('/entreprise/notification', name: 'notification')]
    public function notification(): Response
    {
        return $this->render('pages/notification.html.twig');
    }

    #[Route('/entreprise/oders', name: 'oders')]
    public function oders(): Response
    {
        return $this->render('pages/oders.html.twig');
    }

    #[Route('/entreprise/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('pages/profile.html.twig');
    }

    #[Route('/entreprise/project', name: 'project')]
    public function project(): Response
    {
        return $this->render('pages/project.html.twig');
    }

    #[Route('/entreprise/setting', name: 'setting')]
    public function setting(): Response
    {
        return $this->render('pages/settings.html.twig');
    }

     #[Route('/entreprise/table', name: 'table')]
    public function table(): Response
    {
        return $this->render('pages/settings.html.twig');
    }

    #[Route('/entreprise/task', name: 'task')]
    public function task(): Response
    {
        return $this->render('pages/task.html.twig');
    }

}