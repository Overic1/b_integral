<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Entreprise;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;



#[Route('/entreprise')]
//#[IsalGranted('ROLE_USER')]
class PropositionController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/entreprise/proposition/list', name: 'proposition.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $proposalResponse = $httpClient->request('GET', $url . 'index.php/warehouses' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $proposalResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $proposalData = $proposalResponse->toArray();


            for ($i = 0; $i < count($proposalData); $i++) {

                $idfact = $proposalData[$i]['ref'];
                if(isset($proposalData[$i]['date'])){

                    $datefact = $proposalData[$i]['date'];
                    
                    $datefact = strtotime($datefact); // Convertir la chaîne en horodatage

                    $dateOrigine = new \DateTime();
                    $dateOrigine->setTimestamp($datefact); // Remplacez $datefact par la valeur de votre date en secondes

                    setlocale(LC_TIME, 'fr_FR.UTF-8'); // Définit la locale en français

                    $datefact = $dateOrigine->format('l d F Y');
                }

                $total_ht = $proposalData[$i]['total_ht'];
                $total_tva = $proposalData[$i]['total_tva'];
                $total_ttc = $proposalData[$i]['total_ttc'];

                
                $total_ht = number_format($total_ht, 2, ',', ' ');
                $total_tva = number_format($total_tva, 2, ',', ' ');
                $total_ttc = number_format($total_ttc, 2, ',', ' ');

                    
                if(isset($proposalData[$i]['socid'])){
                    $idcli = $proposalData[$i]['socid'];
                    $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties/' . $idcli . '?DOLAPIKEY=' . $apiKey);
                    // $clientResponse = $httpClient->request('GET', 'https://erp.net2all.online/enseignetesta/htdocs/api/index.php/thirdparties/' . $idcli . '?DOLAPIKEY=' . 'a3HIuxyzAB2ST4K5vw6cQRUde');
                    $clientData = $clientResponse->toArray();
                    $clientname = $clientData['name'];
                    if (isset($clientData['array_options']['options_n_bpay'])) {
                        $bpay = $clientData['array_options']['options_n_bpay'];
                    }
                    $codeclient = $clientData['code_client'];

                    $data[] = [
                        'idfact' => $idfact,
                        'client' => $clientname,
                        'bpay' => $bpay,
                        'codeclient' => $codeclient,
                        'datefact' => $datefact,
                        'total_ht' => $total_ht,
                        'total_tva' => $total_tva,
                        'total_ttc' => $total_ttc,

                    ];
                }
            }
        }
 
        return $this->render('pages/proposition/list.html.twig', [
            'data' => $data
        ]);
    }

    #[Route('/entreprise/proposition/new', name: 'proposition.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {

        // Envoyer la requête POST à l'API pour créer l'utilisateur
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        // // Récupérer les données fournies par l'utilisateur
        $libelle = $request->request->get('libelle');
        $description = $request->request->get('description');
        $lieu = $request->request->get('lieu');
        $statut = $request->request->get('statut');

        // Construire le tableau des données à envoyer à l'API
        $entrepotData = [
            'libelle' => $libelle,
            'description' => $description,
            'lieu' => $lieu,
            'statut' => $statut
        ];

        $response = $httpClient->request('POST', $url . 'index.php/warehouses' . '?DOLAPIKEY=' . $apiKey, [
            'json' => $entrepotData
        ]);

        $statusCode = $response->getStatusCode();

        // Traiter la réponse de l'API
        if ($statusCode === 200) {
            // Utilisateur créé avec succès
            $this->addFlash(
                'success',
                'L\'enregistrement a été éffectué avec succès !'
            );
            return $this->redirectToRoute('entrepots.list');
        }

        return $this->render('pages/proposition/new.html.twig');
    }
}