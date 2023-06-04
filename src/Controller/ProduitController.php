<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/produit/list', name: 'produit.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $produitResponse = $httpClient->request('GET', $url . 'index.php/products' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $produitResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $produitData = $produitResponse->toArray();


            for ($i = 0; $i < count($produitData); $i++) {
                $ref = $produitData[$i]['ref'];
                $label = $produitData[$i]['label'];
                $description = $produitData[$i]['description'];
                $price = $produitData[$i]['price'];
                $price_ttc = $produitData[$i]['price_ttc'];
                $tva = $produitData[$i]['tva_tx'];
                $stock_reel = $produitData[$i]['stock_reel'];
                if (isset($produitData[$i]['array_options']['options_taxspecific'])) {
                    $taxspecific = $produitData[$i]['array_options']['options_taxspecific'];
                } else {
                    $taxspecific = 'non defini'; 
                }

                if (isset($produitData[$i]['array_options']['options_taxgroup'])) {
                    $taxgroup = $produitData[$i]['array_options']['options_taxgroup'];
                } else {
                    $taxgroup = 'non defini';
                }
                
                if (isset($produitData[$i]['array_options']['options_url_image'])) {
                    // $img = $produitData[$i]['array_options']['options_url_image'];
                    $img = 1;
                } else {
                    $img = 1;
                }

                $price = number_format($price, 2, ',', ' ');
                $price_ttc = number_format($price_ttc, 2, ',', ' ');
                $tva = number_format($tva, 2, ',', ' ');

                $data[] = [
                    'ref' => $ref,
                    'label' => $label,
                    'description' => $description,
                    'price' => $price,
                    'price_ttc' => $price_ttc,
                    'stock_reel' => $stock_reel,
                    'tva' => $tva,
                    'taxspecific' => $taxspecific,
                    'taxgroup' => $taxgroup,
                    'img' => $img,
                ];
            }
        }
        return $this->render('pages/produit/list.html.twig', [
            // 'produitData' => $produitData,
            'data' => $data

        ]);
    }

    #[Route('/produit/new', name: 'produit.new', methods: ['GET', 'POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {
        $form = $this->createForm(ProduitType::class);
        

        // Envoyer la requête POST à l'API pour créer l'utilisateur
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        // // Récupérer les données fournies par l'utilisateur
        $label = $request->request->get('label');
        $description = $request->request->get('description');
        $type = $request->request->get('type');
        $barcode = $request->request->get('barcode');
        $ref = $request->request->get('ref');
        $price = $request->request->get('price');
        $price_ttc = $request->request->get('price_ttc');
        $status_buy = $request->request->get('status_buy');
        $status = $request->request->get('status');
        $tva_tx = $request->request->get('tva_tx');
        $options_taxgroup = $request->request->get('options_taxgroup');
        $options_taxspecific = $request->request->get('options_taxspecific');
        $options_qty = $request->request->get('options_qty');
        // $options_categories = $request->request->get('options_categories');
        // $options_url_image = $request->request->get('options_url_image');

        $codeEnseigne = "RXUX";
        $randomNumber = rand(100000, 999999);
        $ref = "1" . $codeEnseigne . "-" . $randomNumber;
        
        

        // Construire le tableau des données à envoyer à l'API
        $entrepotData = [
            "label" => $label,
            "description" => $description,
            "type" => "0",
            "barcode" => $barcode,
            "ref" => $ref,
            "price" => $price,
            "price_ttc" => $price_ttc,
            "status_buy" => $status_buy,
            "status" => $status,
            "tva_tx" => $tva_tx,
            "array_options" => [
                "options_taxgroup" => $options_taxgroup ,
                "options_taxspecific" => $options_taxspecific,
                "options_qty" => "valeur de stock_reel",
                "options_categories" => "id_categorie",
                "options_url_image" => "valeur de img"
            ]
        ];

        $response = $httpClient->request('POST', $url . 'index.php/products' . '?DOLAPIKEY=' . $apiKey, [
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
            return $this->redirectToRoute('produit.list');
        }

        return $this->render('pages/produit/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}