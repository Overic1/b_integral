<?php

namespace App\Controller;

use App\Form\TiersType;
use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/entreprise')]
//#[IsalGranted('ROLE_USER')]
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
    #[Route('/entreprise/tiers/list', name: 'tiers.list')]
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
                if (isset($clientData[$i]['array_options']['options_n_bpay'])) {
                    $bpay = $clientData[$i]['array_options']['options_n_bpay'];
                } else {
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
    #[Route('/entreprise/tiers/prospects/list', name: 'prospects.list')]
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
                if ($prospect == 1) {
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
    #[Route('/entreprise/tiers/fournisseurs/list', name: 'fournisseurs.list')]
    public function fournisseurslist(HttpClientInterface  $httpClient): Response
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
                $fournisseur = $clientData[$i]['fournisseur'];
                if ($fournisseur == 1) {
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
    #[Route('/entreprise/tiers/clients/list', name: 'clients.list')]
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
                if ($client ==  1) {
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


    
    #[Route('/entreprise/tiers/new', name: 'tiers.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {
        // Créer le formulaire et l'associer à la requête
        $form = $this->createForm(TiersType::class);
        $form->handleRequest($request);

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
            $codeEnseigne = $user->getCodeEntreprise();
            $entrepriseid = $user->getId();
        }

        // code_client
        $randomNumber = rand(100000, 999999);
        $ref = $codeEnseigne . "-" .'TR' . $randomNumber;
        
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();
                $type = $formData['type'];
                if($type == 'c'){
                    $client = 1;
                    $prospect = 0;
                    $fournisseur = 0;
                }elseif($type == 'f'){
                    $client = 0;
                    $prospect = 0;
                    $fournisseur = 1;  
                }elseif($type == 'p'){
                  $client = 0;
                    $prospect = 1;
                    $fournisseur = 0; 
                }
            // Construire le tableau des données à envoyer à l'API
            $tiersData = [
                // "userId" => 753,
                "code_client" => $ref,
                "enseigneId" => $entrepriseid,
                "address" => $formData['address'],
                "name" => $formData['nom'],
                "lastname" => $formData['prenom'],
                "email" => $formData['email'],
                "type_tiers" => "1",
                "client" => $client,
                "prospect" => $prospect,
                "fournisseur" => $fournisseur,
                "phone" => $formData['phone'],
                "tva_assuj" => $formData['tva_assuj'],
                "civility_id" => $formData['civility_id'],
                "country_id" => "1",
                "state_id" => "1"
            ];
            // dd($tiersData);


            $clientResponse = $httpClient->request('GET', 'https://business.net2all.online/public/index.php/api/users');
            $statusCode1 = $clientResponse->getStatusCode();
            $cptexiste = false;
            if ($statusCode1 === 200) {
                // $clientData = json_decode($clientResponse->getContent(), true);
                $clientData = $clientResponse->toArray();
                // dd($clientData);
                // var_dump($clientData);
                    // foreach($clientData as $clientData){
                // for ($i = 0; $i < count($clientData); $i++) {
                //     $email = $clientData["hydra:member"][$i]["email"];
                //    // $clientname = $clientData[$i]['name'];
                //     if ($email == $tiersData['email']) {
                //         $cptexiste = true;
                //     }
                // }
            }
            
            // if ($cptexiste == true) {
                // Envoyer la requête POST à l'API pour créer le tiers
                
                try {

                    $response = $httpClient->request('POST', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey, [
                        'json' => $tiersData
                    ]);


                    $statusCode = $response->getStatusCode();

                    // Traiter la réponse de l'API
                    if ($statusCode === 200) {
                        // tiers créé avec succès
                        $this->addFlash(
                            'success',
                            'Le client a été effectué avec succès !'
                        );
                        return $this->redirectToRoute('tiers.list');
                    }
                } catch (RequestException $e) {
                    // Gérer les erreurs de requête HTTP
                    $errorMessage = 'Une erreur est survenue lors de la communication avec l\'API : ';
                    $this->addFlash(
                        'error',
                        'Une erreur est survenue lors de la communication avec l\'API : '
                    );
                    // return $this->redirectToRoute('tiers.new');
                }
            // }else {
            //     try {
                    
            //         dd($tiersData);
            //         $data = [
            //                 "email" => "So@gmail.com",
            //                 "entreprises" => [    
            //                     "nom" => "TOHOU",
            //                     "prenoms" => "Mauelle"
            //                 ],
            //             ];
            //         $response2 = $httpClient->request('POST', 'https://business.net2all.online/public/index.php/api/users', [
            //             'json' => $tiersData
            //         ]);
            //         $statusCode2 = $response2->getStatusCode();

            //         $response = $httpClient->request('POST', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey, [
            //             'json' => $tiersData
            //         ]);
            //         $statusCode = $response->getStatusCode();

            //         // Traiter la réponse de l'API
            //         if ($statusCode === 200 && $statusCode2 === 200) {
            //             // tiers créé avec succès
            //             $this->addFlash(
            //                 'success',
            //                 'Le client a été effectué avec succès !'
            //             );
            //             return $this->redirectToRoute('tiers.list');
            //         }
            //     } catch (RequestException $e) {
            //         // Gérer les erreurs de requête HTTP
            //         $errorMessage = 'Une erreur est survenue lors de la communication avec l\'API : ';
            //         $this->addFlash(
            //             'error',
            //             $errorMessage
            //         );
            //         // return $this->redirectToRoute('tiers.new');
            //     }
            // }
        }
        return $this->render('pages/tiers/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}