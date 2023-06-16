<?php

namespace App\Controller;

use App\Entity\Entreprise;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/entreprise')]
// #[IsGranted('ROLE_USER')]
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

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }


        $bonResponse = $httpClient->request('GET', $url . 'index.php/orders' . '?DOLAPIKEY=' . $apiKey);
        $statusCode = $bonResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $bonData = $bonResponse->toArray();


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
    
               return $this->render('pages/bondecommande/new.html.twig', [
        ]);
    }
    
}