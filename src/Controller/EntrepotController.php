<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class EntrepotController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/entreprise/entrepot', name: 'entrepots.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $entrepotResponse = $httpClient->request('GET', $url . 'index.php/warehouses' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $entrepotResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $entrepotData = $entrepotResponse->toArray();


            for ($i = 0; $i < count($entrepotData); $i++) {
                // $ref = $entrepotData[$i]['ref'];
                $libelle = $entrepotData[$i]['libelle'];
                $description = $entrepotData[$i]['description'];
                $lieu = $entrepotData[$i]['lieu'];
                $statut = $entrepotData[$i]['statut'];
                // if (isset($entrepotData[$i]['array_options']['options_taxspecific'])) {
                //     $taxspecific = $entrepotData[$i]['array_options']['options_taxspecific'];
                // } else {
                //     $taxspecific = 'non defini';
                // }

                $data[] = [
                    // 'ref' => $ref,
                    'libelle' => $libelle,
                    // 'label' => $label,
                    'description' => $description,
                    'lieu' => $lieu,
                    'statut' => $statut
                ];
            }
        }
        return $this->render('pages/entrepot/list.html.twig', [
            'data' => $data
        ]);
    }

    #[Route('/entreprise/entrepot/new', name: 'entrepots.new', methods: ['GET', 'POST'])]
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

        return $this->render('pages/entrepot/new.html.twig');
    }
}