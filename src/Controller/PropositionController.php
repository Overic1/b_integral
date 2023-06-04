<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\Entreprise;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PropositionController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/proposition/list', name: 'proposition.list')]
    public function index(HttpClientInterface  $httpClient): Response
    {

        // $user = $this->security->getUser();
        // $apiKey = '';
        // $url = '';
        // if ($user instanceof Entreprise) {
        //     $apiKey = $user->getApiKey();
        //     $url = $user->getBaseUrl();
        // }


        // $proposalResponse = $httpClient->request('GET', $url . 'index.php/warehouses' . '?DOLAPIKEY=' . $apiKey);
        $proposalResponse = $httpClient->request('GET', 'https://erp.net2all.online/enseignetesta/htdocs/api/index.php/proposals/' . '?DOLAPIKEY=' . 'a3HIuxyzAB2ST4K5vw6cQRUde');
        $statusCode = $proposalResponse->getStatusCode();

        $data = [];

        if ($statusCode === 200) {

            $proposalData = $proposalResponse->toArray();


            for ($i = 0; $i < count($proposalData); $i++) {

                $idfact = $proposalData[$i]['ref'];
                $datefact = $proposalData[$i]['date'];
                $total_ht = $proposalData[$i]['total_ht'];
                $total_tva = $proposalData[$i]['total_tva'];
                $total_ttc = $proposalData[$i]['total_ttc'];
                $idcli = $proposalData[$i]['socid'];
                

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
 
        return $this->render('pages/proposition/list.html.twig', [
            'data' => $data
        ]);
    }
}