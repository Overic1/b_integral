<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BanquecaisseController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/banque', name: 'banque.list')]
    public function banquelist(HttpClientInterface  $httpClient): Response
    {

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $produitResponse = $httpClient->request('GET', $url . 'index.php/bankaccounts' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $produitResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $produitData = $produitResponse->toArray();


            for ($i = 0; $i < count($produitData); $i++) {
                if($produitData[$i]['type'] == 1 ){
                    $label = $produitData[$i]['label'];
                    $banque = $produitData[$i]['bank'];
                    $status = $produitData[$i]['clos'];
                    $numcompte = $produitData[$i]['number'];
                    $comment = $produitData[$i]['comment'];

                   
                    $data[] = [
                        'label' => $label,
                        'banque' => $banque,
                        'status' => $status,
                        'numcompte' => $numcompte,
                        'comment' => $comment
                    ];
                }
               
            }

            return $this->render('pages/banquecaisse/banquelist.html.twig', [
                // 'produitData' => $produitData,
                'data' => $data
                
            ]);
        } else {
            return $this->render('pages/banquecaisse/banquelist.html.twig', [
                'data' => 'pas de données'

            ]);
        }
    }


    #[Route('/caisse', name: 'caisse.list')]
    public function listcaisse(HttpClientInterface  $httpClient): Response
    {



        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $produitResponse = $httpClient->request('GET', $url . 'index.php/bankaccounts' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $produitResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $produitData = $produitResponse->toArray();


            for ($i = 0; $i < count($produitData); $i++) {
                if ($produitData[$i]['type'] == 2 ) {

                    $label = $produitData[$i]['label'];
                    $status = $produitData[$i]['clos'];
                    $comment = $produitData[$i]['comment'];


                    $data[] = [
                        'label' => $label,
                        'status' => $status,
                        'comment' => $comment
                    ];
                }
            }

        return $this->render('pages/banquecaisse/caisselist.html.twig', [
                'data' => $data
            
        ]);
        } else {
            return $this->render('pages/banquecaisse/banquelist.html.twig', [
                'data' => 'pas de données'

            ]);
        }
    }

        
}