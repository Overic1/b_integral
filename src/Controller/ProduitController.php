<?php

namespace App\Controller;

use App\Form\ProduitType;
use App\Entity\Entreprise;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;



class ProduitController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/entreprise/produit/list', name: 'produit.list')]
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
            // dd($produitData);
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
                    $img = $produitData[$i]['array_options']['options_url_image'];
                    // $img = 1;
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
    

    #[Route('/entreprise/produit/new', name: 'produit.new', methods: ['GET', 'POST'])]
        
    public function create(
        Request$request, 
        HttpClientInterface $httpClient, 
        EntityManagerInterface $manager, 
        KernelInterface $kernel
    ): Response
    {

        $form = $this->createForm(ProduitType::class);
        $form->handleRequest($request);

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
            $codeEnseigne = $user->getCodeEntreprise();
            $entrepriseId = $user->getId();
        }

        $entreprise = $manager->getRepository(Entreprise::class)->find($entrepriseId);
        
        
        $randomNumber = rand(100000, 999999);
        $ref = $codeEnseigne . "-" .'PT'. '-' . $randomNumber; 
        
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $formData = $form->getData();

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Générez un nom de fichier unique
                $fileName = md5(uniqid()) . '.' . $imageFile->guessExtension();

                $destination = $kernel->getProjectDir() . '/' .
                str_replace(' ', '', strtolower($entreprise->getInstallation()->getSousDomaine())) . '.' .
                str_replace(' ', '', strtolower($entreprise->getInstallation()->getDomaine())) . '/htdocs/' .
                str_replace(' ', '', strtolower($entreprise->getNom())) . '/Documents';

                // Déplacez le fichier vers le dossier de destination
                // $destination = $this->getParameter('kernel.project_dir') . '/' . $destination;
                $imageFile->move($destination, $fileName);
                
            }
            // else{
               
            //     $defaultImagePath = $this->getParameter('kernel.project_dir') . '/public/images/default.jpg';
            //     $fileName = 'default.jpg';
            //     $imageFile = new File($defaultImagePath);
               
                
            //     // Générez un nom de fichier unique
            //     $fileName = md5(uniqid()) . '.' . $imageFile->guessExtension();

            //     $destination = $kernel->getProjectDir() . '/' .
            //     str_replace(' ', '', strtolower($entreprise->getInstallation()->getSousDomaine())) . '.' .
            //     str_replace(' ', '', strtolower($entreprise->getInstallation()->getDomaine())) . '/htdocs/' .
            //     str_replace(' ', '', strtolower($entreprise->getNom())) . '/Documents';

            //     // Copier le fichier vers le dossier de destination
               
            //     $filesystem = new Filesystem();
            //     $filesystem->copy($defaultImagePath, $destination . '/' . $fileName);

            // }
            
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
                // "stock_reel" => $formData['stock_reel'],
                "array_options" => [
                    "options_qty" => $formData['options_qty'],
                    "options_url_image" => $fileName,
                ]
            ];
            // dd($produitData);
            // dd($fileName);
            
                try {
        
                    // Envoyer la requête POST à l'API pour créer le produit
                $response = $httpClient->request('POST', $url . 'index.php/products' . '?DOLAPIKEY=' . $apiKey, [
                    'json' => $produitData
                ]);

                $statusCode = $response->getStatusCode();

                // Traiter la réponse de l'API
                if ($statusCode === 200) {
                    // Produit créé avec succès
                    $this->addFlash(
                        'success',
                        'Le produit a été effectué avec succès !'
                    );
                    return $this->redirectToRoute('produit.list');
                }
            } catch (RequestException $e) {
                // Gérer les erreurs de requête HTTP
                $errorMessage = 'Une erreur est survenue lors de la communication avec l\'API : ';
                $this->addFlash(
                    'error',
                    $errorMessage
                );
                // return $this->redirectToRoute('produit.new');
            }
        }
        return $this->render('pages/produit/newpro.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}