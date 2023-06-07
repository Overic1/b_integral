<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entreprise;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BanquecaisseController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * Listes des banqwues
     *
     * @param HttpClientInterface $httpClient
     * @return Response
     */
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


        $banqueResponse = $httpClient->request('GET', $url . 'index.php/bankaccounts' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $banqueResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $banqueData = $banqueResponse->toArray();


            for ($i = 0; $i < count($banqueData); $i++) {
                if($banqueData[$i]['type'] == 1 ){
                    $label = $banqueData[$i]['label'];
                    $banque = $banqueData[$i]['bank'];
                    $status = $banqueData[$i]['clos'];
                    $numcompte = $banqueData[$i]['number'];
                    $comment = $banqueData[$i]['comment'];

                   
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
                // 'banqueData' => $banqueData,
                'data' => $data
                
            ]);
        } else {
            return $this->render('pages/banquecaisse/banquelist.html.twig', [
                'data' => 'pas de données'

            ]);
        }
    }

    /**
     *  Listes des Caisses
     *
     * @param HttpClientInterface $httpClient
     * @return Response
     */
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


        $caisseResponse = $httpClient->request('GET', $url . 'index.php/bankaccounts' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $caisseResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $caisseData = $caisseResponse->toArray();


            for ($i = 0; $i < count($caisseData); $i++) {
                if ($caisseData[$i]['type'] == 2 ) {

                    $label = $caisseData[$i]['label'];
                    $status = $caisseData[$i]['clos'];
                    $comment = $caisseData[$i]['comment'];


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



    #[Route('/banquecaisse/new', name: 'banquecaisse.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {
        // Créer le formulaire et l'associer à la requête
        $form = $this->createForm(banquecaisseType::class);
        $form->handleRequest($request);


        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
            $codeEnseigne = $user->getCodeEntreprise();
        }


        $randomNumber = rand(100000, 999999);
        $ref = $codeEnseigne . "-" . 'PT' . '-' . $randomNumber;

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            // Construire le tableau des données à envoyer à l'API
            $banquecaisseData = [
                "label" => $formData['label'],
                "description" => $formData['description'],
                "type" => "0",
                "barcode" => $formData['barcode'],
                "ref" => $ref,
                "price" => $formData['price'],
                "price_ttc" => $formData['price_ttc'],
                "status_buy" => $formData['status_buy'],
                "status" => $formData['status'],
                "tva_tx" => $formData['tva_tx'],
                // "stock_reel" => $formData['stock_reel'],
            ];
            // dd($banquecaisseData);
            try {

                // Envoyer la requête POST à l'API pour créer le banquecaisse
                $response = $httpClient->request('POST', $url . 'index.php/products' . '?DOLAPIKEY=' . $apiKey, [
                    'json' => $banquecaisseData
                ]);

                $statusCode = $response->getStatusCode();

                // Traiter la réponse de l'API
                if ($statusCode === 200) {
                    // banquecaisse créé avec succès
                    $this->addFlash(
                        'success',
                        'Le banquecaisse a été effectué avec succès !'
                    );
                    return $this->redirectToRoute('banquecaisse.list');
                }
            } catch (RequestException $e) {
                // Gérer les erreurs de requête HTTP
                $errorMessage = 'Une erreur est survenue lors de la communication avec l\'API : ';
                $this->addFlash(
                    'error',
                    $errorMessage
                );
                // return $this->redirectToRoute('banquecaisse.new');
            }
        }
        return $this->render('pages/banquecaisse/newpro.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}