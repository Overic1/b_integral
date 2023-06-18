<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/entreprise')]
//#[IsalGranted('ROLE_USER')]
class EntrepriseController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
 
    #[Route('/dashboard', name: 'index')]
    public function index(HttpClientInterface  $httpClient): Response
    {

        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';
        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl() . 'index.php/';
        }

        $responsefact = $httpClient->request('GET', $url . 'invoices' . '?DOLAPIKEY=' . $apiKey);
        $responseBon = $httpClient->request('GET', $url . 'orders' . '?DOLAPIKEY=' . $apiKey);
        $responsePro = $httpClient->request('GET', $url . 'products' . '?DOLAPIKEY=' . $apiKey);
        $responseEn = $httpClient->request('GET', $url . 'warehouses' . '?DOLAPIKEY=' . $apiKey);
        $responseColl = $httpClient->request('GET', $url . 'users' . '?DOLAPIKEY=' . $apiKey);
        $responseTiers = $httpClient->request('GET', $url . 'thirdparties' . '?DOLAPIKEY=' . $apiKey);
        $responseProCom = $httpClient->request('GET', $url . 'proposals' . '?DOLAPIKEY=' . $apiKey);
        $responseBanCai = $httpClient->request('GET', $url . 'bankaccounts' . '?DOLAPIKEY=' . $apiKey);

        $statusCode1 = $responsefact->getStatusCode();
        $statusCode2 = $responseBon->getStatusCode();
        $statusCode3 = $responsePro->getStatusCode();
        $statusCode4 = $responseEn->getStatusCode();
        $statusCode5 = $responseColl->getStatusCode();
        $statusCode6 = $responseTiers->getStatusCode();
        $statusCode7 = $responseProCom->getStatusCode();
        $statusCode8 = $responseBanCai->getStatusCode();

        if ($statusCode1 === 200) {
            $factNonPaye = 0;
            $jsonData = $responsefact->toArray();
            foreach ($jsonData as $jsonData) {
                if ($jsonData['paye'] == 0) {
                    $factNonPaye = $factNonPaye + 1;
                }
            }
            $factCount = count($jsonData);
        } else {
            $factCount = 0;
        }
        if ($statusCode2 === 200) {
            $jsonData = $responseBon->toArray();
            $BonCount = count($jsonData);
        } else {
            $BonCount = 0;
        }
        if ($statusCode3 === 200) {
            $nbProNoStock = 0;
            $jsonData = $responsePro->toArray();
            $ProCount = count($jsonData);
            for ($i = 0; $i < count($jsonData); $i++) {
                // dd( $ProCount[$i]['stock_reel']);
                $stockPro = $jsonData[$i]['stock_reel'];
                if ($stockPro == 0) {
                    $nbProNoStock = $nbProNoStock + 1;
                }
            }
        } else {
            $ProCount = 0;
        }
        if ($statusCode4 === 200) {
            $jsonData = $responseEn->toArray();
            $EnCount = count($jsonData);
        } else {
            $EnCount = 0;
        }
        if ($statusCode5 === 200) {
            $jsonData = $responseColl->toArray();
            $CollCount = count($jsonData);
        } else {
            $CollCount = 0;
        }
        if ($statusCode6 === 200) {
            $jsonData = $responseTiers->toArray();
            $TiersCount = count($jsonData);
            $countprospect = 0;
            $countfour = 0;
            $countclient = 0;
            for ($i = 0; $i < count($jsonData); $i++) {
                $prospect = $jsonData[$i]['prospect'];
                $fournisseur = $jsonData[$i]['fournisseur'];
                $client = $jsonData[$i]['client'];

                if ($prospect == 1) {
                    $countprospect = $countprospect + 1;
                }
                if ($fournisseur == 1) {
                    $countfour = $countfour + 1;
                }
                if ($client == 1) {
                    $countclient = $countclient + 1;
                }
            }
        }
        if ($statusCode7 === 200) {
            $jsonData = $responseProCom->toArray();
            $ProComCount = count($jsonData);
        } else {
            $ProComCount = 0;
        }
        if ($statusCode8 === 200) {
            $CaisseCount = 0;
            $BanqueCount = 0;
            $jsonData = $responseBanCai->toArray();
            for ($i = 0; $i < count($jsonData); $i++) {
                if ($jsonData[$i]['type'] == 1) {
                    $BanqueCount = $BanqueCount + 1;
                }
                if ($jsonData[$i]['type'] == 2) {
                    $CaisseCount = $CaisseCount + 1;
                }
            }
        }

        // *********************************




        return $this->render('pages/entreprise/index.html.twig', [
            'factCount' => $factCount,
            'BonCount' => $BonCount,
            'ProCount' => $ProCount,
            'EnCount' => $EnCount,
            'CollCount' => $CollCount,
            'TiersCount' => $TiersCount,
            'ProComCount' => $ProComCount,
            'countprospect' => $countprospect,
            'countfour' => $countfour,
            'countclient' => $countclient,
            'factNonPaye' => $factNonPaye,
            'nbProNoStock' => $nbProNoStock,
            'CaisseCount' => $CaisseCount,
            'BanqueCount' => $BanqueCount
        ]);
    }


     #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('pages/entreprise/profile.html.twig');
    }


}