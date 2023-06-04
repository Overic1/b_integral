<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\Entreprise;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class BondecommandeController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    
    #[Route('/bondecommande/list', name: 'bondecommande.list')]
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
        $bonResponse = $httpClient->request('GET', 'https://erp.net2all.online/enseignetesta/htdocs/api/index.php/orders/' . '?DOLAPIKEY=' . 'a3HIuxyzAB2ST4K5vw6cQRUde');
        $statusCode = $bonResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $bonData = $bonResponse->toArray();


            for ($i = 0; $i < count($bonData); $i++) {
                
                    $idfact = $bonData[$i]['ref'];
                    $datefact = $bonData[$i]['date'];
                    $total_ht = $bonData[$i]['total_ht'];
                    $total_tva = $bonData[$i]['total_tva'];
                    $total_ttc = $bonData[$i]['total_ttc'];
                    // $paye = $bonData[$i]['paye'];
                    $idcli = $bonData[$i]['socid'];
                   
                    $datefact = strtotime($datefact); // Convertir la chaîne en horodatage

                    $dateOrigine = new \DateTime();
                    $dateOrigine->setTimestamp($datefact); // Remplacez $datefact par la valeur de votre date en secondes

                    setlocale(LC_TIME, 'fr_FR.UTF-8'); // Définit la locale en français

                    $datefact = $dateOrigine->format('l d F Y');


                    $total_ht = number_format($total_ht, 2, ',', ' ');
                    $total_tva = number_format($total_tva, 2, ',', ' ');
                    $total_ttc = number_format($total_ttc, 2, ',', ' ');

                    // $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties/' . $idcli . '?DOLAPIKEY=' . $apiKey);
                    $clientResponse = $httpClient->request('GET', 'https://erp.net2all.online/enseignetesta/htdocs/api/index.php/thirdparties/' . $idcli . '?DOLAPIKEY=' . 'a3HIuxyzAB2ST4K5vw6cQRUde');
                    $clientData = $clientResponse->toArray();
                    $clientname = $clientData['name'];
                    if(isset($clientData['array_options']['options_n_bpay'])){
                        $bpay = $clientData['array_options']['options_n_bpay'];
                    }
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
                
                ];
            }
        }
 
        
        return $this->render('pages/bondecommande/list.html.twig', [
            'bonData' => $bonData,
            'data' => $data
        ]);
    }
}