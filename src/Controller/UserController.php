<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class UserController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/entreprise/user', name: 'user.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $produitResponse = $httpClient->request('GET', $url . 'index.php/users' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $produitResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $produitData = $produitResponse->toArray();


            for ($i = 0; $i < count($produitData); $i++) {
                $email = $produitData[$i]['email'];
                $lastname = $produitData[$i]['lastname'];
                $firstname = $produitData[$i]['firstname'];
                $gender = $produitData[$i]['gender'];
                $photo = $produitData[$i]['photo'];
                $tel = $produitData[$i]['office_phone'];
                
                $data[] = [
                    'email' => $email,
                    // 'address' => $address,
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'gender' => $gender,
                    'photo' => $photo,
                    'tel' => $tel
                ];
            }
        }
        return $this->render('pages/user/list.html.twig', [
            'data' => $data
        ]);
    }


    #[Route('/entreprise/user/new', name: 'user.new', methods: ['GET', 'POST'])]
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
        $email = $request->request->get('email');
        $lastname = $request->request->get('lastname');
        $firstname = $request->request->get('firstname');
        $gender = $request->request->get('gender');
        // $photo = $request->request->get('photo');
        // $tel = $request->request->get('tel');

        
        // Construire le tableau des données à envoyer à l'API
        $userData = [
            'email' => $email,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'gender' => $gender,
            // 'photo' => $photo,
            // 'office_phone' => $tel
        ];

        $response = $httpClient->request('POST', $url . 'index.php/users' . '?DOLAPIKEY=' . $apiKey, [
            'json' => $userData
        ]);

        $statusCode = $response->getStatusCode();

        // Traiter la réponse de l'API
        if ($statusCode === 200) {
            // Utilisateur créé avec succès
            $this->addFlash(
                'success',
                'L\'enregistrement a été éffectué avec succès !'
            );
            return $this->redirectToRoute('facture.list'); 
        }
         
        return $this->render('pages/user/new.html.twig');
    }
}