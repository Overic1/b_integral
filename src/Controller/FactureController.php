<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use GuzzleHttp\Client;


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
        }

        return $this->render('pages/facture/list.html.twig', [
            'data' => $data
        ]);
    }


    #[Route('/facture/new', name: 'facture.new', methods: ['GET','POST'])]
    public function create(Request $request, HttpClientInterface $httpClient): Response
    {

        // Envoyer la requête POST à l'API pour créer l
        $user = $this->security->getUser();
        $apiKey = '';
        $url = '';

        if ($user instanceof Entreprise) {
            $apiKey = $user->getApiKey();
            $url = $user->getBaseUrl();
        }

        // // Récupérer les données fournies par l
        $total_ht = $request->request->get('total_ht');
        $total_tva = $request->request->get('total_tva');
        $total_ttc = $request->request->get('total_ttc');
        // $paye = $request->request->get('paye');
        $socid = $request->request->get('socid');
        // Construire le tableau des données à envoyer à l'API
        $data = [
            'socid' => $socid,
            'total_ht' => $total_ht,
            'total_tva' => $total_tva,
            'total_ttc' => $total_ttc,
            // 'paye' => $paye,
            // 'normalisation' => $normalisation
        ];
        // dd($data);

        $response = $httpClient->request('POST', $url . 'index.php/invoices' . '?DOLAPIKEY=' . $apiKey, [
            'json' => $data
        ]);

       // $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties?DOLAPIKEY=' . $apiKey);
        // $clientResponse = $httpClient->request('GET', $url . 'index.php/thirdparties' . '?DOLAPIKEY=' . $apiKey);
        
        
        // $clientData = $clientResponse->toArray();
        //     // dd($clientData);
        // for ($i = 0; $i < count($clientData); $i++) {
        //     $name = $clientData[$i]['name'];
        //     $id = $clientData[$i]['socid'];
            
        //     $clients = [
        //         'id' => $id,
        //         'name' => $name
        //     ];
        // } 
        // $client = new \GuzzleHttp\Client();
        // $response = $client->post($url. 'index.php/invoices?DOLAPIKEY='. $apiKey, [
        //     'json' => $data
        // ]);
        

        $statusCode = $response->getStatusCode();

        // Traiter la réponse de l'API
        if ($statusCode === 200) {
            // créé avec succès
            $this->addFlash(
                'success',
                'L\'enregistrement a été éffectué avec succès !'
            );
            return $this->redirectToRoute('facture.list');
        }

        $this->addFlash(
            'error',
            'L\'enregistrement n\'a pas aboutit !'
        );  
        return $this->render('pages/facture/new.html.twig',[
            // 'clients' => $clients   
        ]);
    }
}