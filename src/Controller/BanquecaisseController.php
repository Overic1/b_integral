<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\BanquecaisseType;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        
        }
    }



    #[Route('/banquecaisse/new', name: 'banquecaisse.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {


        $contriesResponse = $httpClient->request('GET', 'https://webstocks.myn2a.online/ccibdev/public/index.php/api/countries');
        $statusCode = $contriesResponse->getStatusCode();


        $contriesData = [];
        if($statusCode == 200){
            $contriesData = $contriesResponse->toArray();
            $contriesData = $contriesData["hydra:member"];
            for ($i = 0; $i < count($contriesData); $i++){
                $choices[$i][$contriesData[$i]['label']] = $contriesData[$i]['id'];                
            }
        }
        // rsort($choices);
        // dd($choices);

        
        // Créer le formulaire et l'associer à la requête
        $form = $this->createForm(BanquecaisseType::class,null, [
            'choices' => $choices,
        ]);
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
        $ref = $codeEnseigne . "-" . 'BC' . '-' . $randomNumber;

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            // Construire le tableau des données à envoyer à l'API
            $banquecaisseData = [
                "label" => $formData["label"],
                "ref" => $ref,
                "type" => $formData["type"],
                "clos" => $formData["clos"],
                "currency_code" => $formData["currency_code"],
                "country_id" => $formData["country_id"]
            ];
            // dd($banquecaisseData);
            try {

                // Envoyer la requête POST à l'API pour créer le banquecaisse
                $response = $httpClient->request('POST', $url . 'index.php/bankaccounts' . '?DOLAPIKEY=' . $apiKey, [
                    'json' => $banquecaisseData
                ]);

                $statusCode = $response->getStatusCode();
                // dd($statusCode);
                // Traiter la réponse de l'API
                if ($statusCode === 200) {
                    // banquecaisse créé avec succès
                    // dd(var_dump($formData["type"]));
                    if($formData["type"] == 1){
                        $this->addFlash(
                            'success',
                            'La banque été créer avec succès !'
                        );
                        return $this->redirectToRoute('banque.list');
                    }else{
                        $this->addFlash(
                            'success',
                            'La caisse été créer avec succès !'
                        );
                        return $this->redirectToRoute('caisse.list');
                    }
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
        return $this->render('pages/banquecaisse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}