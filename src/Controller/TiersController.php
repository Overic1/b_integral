<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TiersController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Listes des tiers
     *
     * @param HttpClientInterface $httpClient
     * @return Response
     */
    #[Route('/tiers/list', name: 'tiers.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $clientResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {
            
            $clientData = $clientResponse->toArray();


            for ($i = 0; $i < count($clientData); $i++) {
                $clientid = $clientData[$i]['barcode'];
                $clientname = $clientData[$i]['name'];
                if(isset($clientData[$i]['array_options']['options_n_bpay'])){
                $bpay = $clientData[$i]['array_options']['options_n_bpay'];
                }else{
                    $bpay = 'nom defini';
                }
                $codeclient = $clientData[$i]['code_client'];

                $data[] = [
                    'clientid' => $clientid,
                    'client' => $clientname,
                    'bpay' => $bpay,
                    'codeclient' => $codeclient
                ];
            }
        }    

        return $this->render('pages/tiers/list.html.twig', [
            'data' => $data
        ]);
    }


    /**
     * Listes des prospects
     *
     * @param HttpClientInterface $httpClient
     * @return Response
     */
    #[Route('/tiers/prospects/list', name: 'prospects.list')]
    public function prospectslist(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $clientResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $clientData = $clientResponse->toArray();

            for ($i = 0; $i < count($clientData); $i++) {
                $prospect = $clientData[$i]['prospect'];
                if( $prospect == 1 ){
                    $clientid = $clientData[$i]['barcode'];
                    $clientname = $clientData[$i]['name'];
                    if (isset($clientData[$i]['array_options']['options_n_bpay'])) {
                        $bpay = $clientData[$i]['array_options']['options_n_bpay'];
                    } else {
                        $bpay = 'nom defini';
                    }
                    $codeclient = $clientData[$i]['code_client'];

                    $data[] = [
                        'prospect' => $prospect,
                        'clientid' => $clientid,
                        'client' => $clientname,
                        'bpay' => $bpay,
                        'codeclient' => $codeclient
                    ];
                }
                
            }
        }

        return $this->render('pages/tiers/prospectslist.html.twig', [
            'data' => $data,
            'clientData' => $clientData
        ]);
    }


    /**
     * Listes des fournisseurs
     *
     * @param HttpClientInterface $httpClient
     * @return Response
     */
    #[Route('/tiers/fournisseurs/list', name: 'fournisseurs.list')]
    public function fournisseurslist(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise
        ) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $clientResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $clientData = $clientResponse->toArray();

            for ($i = 0; $i < count($clientData); $i++) {
                $fournisseur = $clientData[$i]['fournisseur'];
                if($fournisseur == 1 ){
                    $clientid = $clientData[$i]['barcode'];
                    $clientname = $clientData[$i]['name'];
                    if (isset($clientData[$i]['array_options']['options_n_bpay'])) {
                        $bpay = $clientData[$i]['array_options']['options_n_bpay'];
                    } else {
                        $bpay = 'nom defini';
                    }
                    $codeclient = $clientData[$i]['code_client'];

                    $data[] = [
                        'fournisseur' => $fournisseur,
                        'clientid' => $clientid,
                        'client' => $clientname,
                        'bpay' => $bpay,
                        'codeclient' => $codeclient
                    ];
                }
            }
        }

        return $this->render('pages/tiers/fournisseurslist.html.twig', [
                'data' => $data
            ]);
    }


    /**
     * Listes des clients
     *
     * @param HttpClientInterface $httpClient
     * @return Response
     */
    #[Route('/tiers/clients/list', name: 'clients.list')]
    public function clientslist(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $clientResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $clientData = $clientResponse->toArray();

            for ($i = 0; $i < count($clientData); $i++) {
                $client = $clientData[$i]['client'];
                if($client ==  1){
                    $clientid = $clientData[$i]['barcode'];
                    $clientname = $clientData[$i]['name'];
                    if (isset($clientData[$i]['array_options']['options_n_bpay'])) {
                        $bpay = $clientData[$i]['array_options']['options_n_bpay'];
                    } else {
                        $bpay = 'nom defini';
                    }
                    $codeclient = $clientData[$i]['code_client'];

                    $data[] = [
                        // 'client' => $client,
                        'clientid' => $clientid,
                        'clientname' => $clientname,
                        'bpay' => $bpay,
                        'codeclient' => $codeclient
                    ];
                }
                
            }
        }

        return $this->render('pages/tiers/clientslist.html.twig', [
            'data' => $data
        ]);
    }
}