<?php

namespace App\Controller\Admin;

use PDO;
use App\Entity\Entreprise;
use App\Entity\Installation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpKernel\KernelInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');   
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('B Integral');
    }

     public function configureCrud():    Crud
    {
        return Crud::new()
            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            // ->overrideTemplate('label/null', 'admin/labels/my_null_label.html.twig')

            ->overrideTemplates([
                'crud/index' => 'admin/index.html.twig',
                'label/null' => 'admin/labels/my_null_label.html.twig',
                // 'crud/edit' => 'admin/edit.html.twig',
                // 'crud/delete' => 'admin/delete.html.twig',   
                // 'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
            ])
        ;
    }

    
    /**
     * Fonction d'assotiation de entreprise et de installation
     * Copie du fichier d'installation dans le dossier de l'entreprise
     */
    #[Route('/admin/entreprise/{installationId}/{entrepriseId}', name: 'entreprise.assoc')]
    public function associerInstallation(
        EntityManagerInterface $manager,
         $entrepriseId,
        $installationId,
        KernelInterface $kernel,
    ): Response {
        
        $entreprise = $manager->getRepository(Entreprise::class)->find($entrepriseId);
        // dd($entreprise);
        $installation = $manager->getRepository(Installation::class)->find($installationId);
        // dd($installation);
        if (!$entreprise || !$installation) {
            throw $this->createNotFoundException('Entreprise ou installation non trouvée.');
        }


        /**
         * Association d'une entreprise a une installation
         */
        if ($entreprise->getInstallation() == null) {

            // $base = 'https://' .
            //     str_replace(' ', '', strtolower($installation->getSousDomaine())) . '.' .
            //     str_replace(' ', '', strtolower($installation->getDomaine())) . '/' .
            //     str_replace(' ', '', strtolower($entreprise->getNom())) . '/htdocs/api/';


            // $entreprise->setBaseUrl($base);
            $entreprise->setInstallation($installation);
            
            // dd($entreprise);
            $manager->persist($entreprise);
            $manager->flush();
            $this->addFlash(
                'success',
                'L\'entreprise "' . $entreprise->getNom() .
                    '" a été associée a l\'installation "' . $installation->getNom() . '" avec succès !'
            );
        } else {
            $this->addFlash(
                'info',
                'L\'entreprise "' . $entreprise->getNom() .
                    '" est déjà associée a une installation !'
            );
        }

        /**
         * Copie du fichier d'installation dans le dossier entreprise
         */
        $fichier = '/fichier_d_installation.rar';
        $source = $kernel->getProjectDir() . '/src' . $fichier;

        $destination = $kernel->getProjectDir() . '/' . 
        str_replace(' ', '', strtolower($entreprise->getInstallation()->getSousDomaine())) . '.' . 
        str_replace(' ', '', strtolower($entreprise->getInstallation()->getDomaine())) . '/htdocs/' . 
        str_replace(' ', '', strtolower($entreprise->getNom()));

        $filesystem = new Filesystem();

        // Vérifier si le dossier de destination existe
        if (!$filesystem->exists($destination)) {
            // Créer le dossier de destination
            try {
                $filesystem->mkdir($destination);
                // $this->addFlash('success','oklm');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création du dossier de destination : ' . $e->getMessage());
                // return $this->redirectToRoute('admin');
            }   
            
            //Copier le fichier d'installation vers la destination
            try {
                $filesystem->copy($source,
                    $destination . $fichier
                );
                $this->addFlash('success', 'Le fichier d\'installation a été copié avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la copie du fichier d\'installation : ' . $e->getMessage());
            }
        }
       

        return $this->redirectToRoute('admin');
         // return $this->render('admin/.html.twig');

    }


    
    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }
    
    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('Entreprise', 'fa-solid fa-building', Entreprise::class);
        // yield MenuItem::linkToCrud('Installation', 'fa-thin fa-building', Installation::class);

            
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
            yield MenuItem::linkToCrud('Entreprise', 'fa-solid fa-building', Entreprise::class);
            yield MenuItem::section();
            yield MenuItem::section('Installation');
            yield MenuItem::linkToCrud('Installation', 'fa fa-tags', Installation::class);
            yield MenuItem::linkToRoute('Entreprise', 'fa fa-file-text', 'assoc_inst_ent');

        $dbUser = 'root';
        $dbpassword = '';
        $serveurName = 'localhost';

    }

    public function CreateDb($entityInstance)
    {
        if (!$entityInstance instanceof Entreprise) return;

        $dbName = $entityInstance->getNomDeLaBase();
        $dbUser = $entityInstance->getUser();
        $dbpassword = $entityInstance->getPassBase();
        $serveurName = 'localhost';

        try {
            $sql = "CREATE DATABASE $dbName";
            $dbco = new \PDO("mysql:host=$serveurName", $dbUser, $dbpassword);
            $dbco->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $dbco->exec($sql);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
       

    }
}