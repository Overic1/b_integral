<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;
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

                // echo $datefact;


                // $dateOrigine = new \DateTime();
                // $dateOrigine->setTimestamp($datefact); // Remplacez $timestamp par la valeur de votre date en secondes
                // $formatter = new IntlDateFormatter('fr', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                // $datefact = $formatter->format($dateOrigine);

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
           

            return $this->render('pages/facture/list.html.twig', [
                'data' => $data
            ]);
        }else {
            return $this->render('pages/facture/list.html.twig', [
                'jsonCount' => 'pas de données',
                'jsonData' => 'pas de data'
                
            ]);
        }
    }
}