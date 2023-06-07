<?php

namespace App\Controller;

use App\Form\FactureType;
use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FactureController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/facture/list', name: 'facture.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        $responsefact = $httpClient->request('GET', $url . 'index.php/invoices' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $responsefact->getStatusCode();

        // $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey);
        // $statusCode2 = $responsefact->getStatusCode();


        $data = [];
        
        if ($statusCode === 200) {
            $factData = $responsefact->toArray();
            
            
            foreach($factData as $factData){
                $idfact = $factData['id'];
                $datefact = $factData['date'];
                $total_ht = $factData['total_ht'];
                $total_tva = $factData['total_tva'];
                $total_ttc = $factData['total_ttc'];
                $paye = $factData['paye'];
                $idcli = $factData['socid'];
                if (isset($factData['array_options']['options_ref_dgi'])) {
                    $normalisation = 1;
                }else{
                    $normalisation = 0;
                }

                $dateOrigine = new \DateTime();
                $dateOrigine->setTimestamp($datefact); // Remplacez $datefact par la valeur de votre date en secondes

                setlocale(LC_TIME, 'fr_FR.UTF-8'); // Définit la locale en français

                $datefact = $dateOrigine->format('l d F Y');


                $total_ht = number_format($total_ht, 2, ',', ' ');
                $total_tva = number_format($total_tva, 2, ',', ' ');
                $total_ttc = number_format($total_ttc, 2, ',', ' ');
                
                $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties/' . $idcli . '?DOLAPIKEY=' . $apiKey);
                $clientData = $clientResponse->toArray();
                $clientname = $clientData['name'];
                $bpay = $clientData['array_options']['options_n_bpay'];
                $codeclient = $clientData['code_client'];
            
                
                $data[] =[
                    'idfact' => $idfact,
                    'client' => $clientname,
                    'bpay' => $bpay,
                    'codeclient' => $codeclient,
                    'datefact' => $datefact,
                    'total_ht' => $total_ht,
                    'total_tva' => $total_tva,
                    'total_ttc' => $total_ttc,
                    'paye' => $paye,
                    'normalisation' => $normalisation
                ];
            }
        }

        return $this->render('pages/facture/list.html.twig', [
            'data' => $data
        ]);
    }


    #[Route('/facture/new', name: 'facture.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {
        // Créer le formulaire et l'associer à la requête
        $form = $this->createForm(FactureType::class);
        $form->handleRequest($request);

        $codeEnseigne = "RXUX";
        $randomNumber = rand(100000, 999999);
        $ref = "1" . $codeEnseigne . "-" . $randomNumber;

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            // Construire le tableau des données à envoyer à l'API
            $produitData = [
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
                    "options_taxgroup" => $formData['options_taxgroup'],
                    "options_taxspecific" => $formData['options_taxspecific'],
                    "options_qty" => $formData['options_qty'],
                    // "options_categories" => "id_categorie",
                    // "options_url_image" => "valeur de img"
                ]
            ];
            // dd($produitData);
            try {
                // Envoyer la requête POST à l'API pour créer le facture
                $user = $this->security->getUser();
                $apiKey = '';
                $url = '';

                if ($user instanceof Entreprise) {
                    $apiKey = $user->getApiKey();
                    $url = $user->getBaseUrl();
                }

                $response = $httpClient->request('POST', $url . 'index.php/invoices' . '?DOLAPIKEY=' . $apiKey, [
                    'json' => $produitData
                ]);

                $statusCode = $response->getStatusCode();

                // Traiter la réponse de l'API
                if ($statusCode === 200) {
                    // Produit créé avec succès
                    $this->addFlash(
                        'success',
                        'La facture a été effectué avec succès !'
                    );
                    return $this->redirectToRoute('facture.list');
                }
            } catch (RequestException $e) {
                // Gérer les erreurs de requête HTTP
                $errorMessage = 'Une erreur est survenue lors de la communication avec l\'API : ';
                $this->addFlash(
                    'error',
                    $errorMessage
                );
                // return $this->redirectToRoute('facture.new');
            }
        }
        return $this->render('pages/facture/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}