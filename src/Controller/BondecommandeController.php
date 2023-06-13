<?php

namespace App\Controller;

use App\Entity\Entreprise;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BondecommandeController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    
    #[Route('/entreprise/bondecommande/list', name: 'bondecommande.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {

        // $user = $this->security->getUser();
        // $apiKey = '';
        // $url = '';
        // if ($user instanceof Entreprise) {
        //     $apiKey = $user->getApiKey();
        //     $url = $user->getBaseUrl();
        // }


        // $bonResponse = $httpClient->request('GET', $url . 'index.php/warehouses' . '?DOLAPIKEY=' . $apiKey);
        $bonResponse = $httpClient->request('GET', 'https://erp.net2all.online/enseignetesta/htdocs/api/index.php/warehouses/' . '?DOLAPIKEY=' . 'a3HIuxyzAB2ST4K5vw6cQRUde');
        $statusCode = $bonResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $bonData = $bonResponse->toArray();

// dd($bonData);
            for ($i = 0; $i < count($bonData); $i++) {
                
                    $ref = $bonData[$i]['ref'];
                    $datebon = $bonData[$i]['date_creation'];
                    $total_ht = $bonData[$i]['total_ht'];
                    $total_tva = $bonData[$i]['total_tva'];
                    $total_ttc = $bonData[$i]['total_ttc'];
                    $pays = $bonData[$i]['country'];
                    
                    $datebon = strtotime($datebon); // Convertir la chaîne en horodatage

                    $dateOrigine = new \DateTime();
                    $dateOrigine->setTimestamp($datebon); // Remplacez $datebon par la valeur de votre date en secondes

                    setlocale(LC_TIME, 'fr_FR.UTF-8'); // Définit la locale en français

                    $datebon = $dateOrigine->format('l d F Y');


                    $total_ht = number_format($total_ht, 2, ',', ' ');
                    $total_tva = number_format($total_tva, 2, ',', ' ');
                    $total_ttc = number_format($total_ttc, 2, ',', ' ');

                   
                $data[] =[
                    'ref' => $ref,
                    'datebon' => $datebon,
                    'total_ht' => $total_ht,
                    'total_tva' => $total_tva,
                    'total_ttc' => $total_ttc,
                    'pays' => $pays
                ];
            }
        }
 
        
        return $this->render('pages/bondecommande/list.html.twig', [
           'data' => $data
        ]);
    }


    #[Route('/entreprise/bondecommande/new', name: 'bondecommande.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {
        // Créer le formulaire et l'associer à la requête
        $form = $this->createForm(BondecommandeType::class);
        $form->handleRequest($request);


        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
            $codeEnseigne = $user->getCodeEntreprise();
        }

        // $codeEnseigne = "RXUX";

        $randomNumber = rand(100000, 999999);
        $ref = $codeEnseigne . "-" . 'PT' . '-' . $randomNumber;

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            // Construire le tableau des données à envoyer à l'API
            $bondecommandeData = [
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
                "array_options" => [
                    "options_qty" => $formData['options_qty'],
                ]
            ];
            // dd($bondecommandeData);
            try {

                // Envoyer la requête POST à l'API pour créer le bondecommande
                $response = $httpClient->request('POST', $url . 'index.php/products' . '?DOLAPIKEY=' . $apiKey, [
                    'json' => $bondecommandeData
                ]);

                $statusCode = $response->getStatusCode();

                // Traiter la réponse de l'API
                if ($statusCode === 200) {
                    // bondecommande créé avec succès
                    $this->addFlash(
                        'success',
                        'Le bondecommande a été effectué avec succès !'
                    );
                    return $this->redirectToRoute('bondecommande.list');
                }
            } catch (RequestException $e) {
                // Gérer les erreurs de requête HTTP
                $errorMessage = 'Une erreur est survenue lors de la communication avec l\'API : ';
                $this->addFlash(
                    'error',
                    $errorMessage
                );
                // return $this->redirectToRoute('bondecommande.new');
            }
        }
        return $this->render('pages/bondecommande/newpro.html.twig', [
            // 'form' => $form->createView(),
        ]);
    }
    
}