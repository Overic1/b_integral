<?php

namespace App\Controller\Admin;

use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstallationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
// use Symfony\Component\DependencyInjection\Loader\Configurator\request;

class EntrepriseCrudController extends AbstractCrudController
{
    public $dbName;
    public static function getEntityFqcn(): string
    {
        return Entreprise::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        // return [
            yield IdField::new('id')->hideOnForm();  // Masquer sur le formulaire d'édition
            yield TextField::new('nom')->setColumns(6);
            // yield EmailField::new('codeEntreprise')->setColumns(6);
            yield EmailField::new('email')->setColumns(6);
            yield TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez le mot de passe'],
                'constraints' => [
                    new Length(
                        [
                            'min' => 6,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                            // Vous pouvez également spécifier une longueur maximale si nécessaire.
                        ]
                    ),
                        ],
            ])->hideOnIndex();
            yield UrlField::new('base_url')->setRequired(false)->setColumns(3);
            yield TextField::new('api_key')->setRequired(false)->setColumns(3);
            yield ImageField::new('logo')->setBasePath('uploads/')->setColumns(3)
            ->setUploadDir('public/uploads/')
            ->setRequired(false);
            yield TelephoneField::new('num_tel')->setRequired(false)->setColumns(3);
            yield IntegerField::new('num_ifu')->setRequired(false)->setColumns(3);
            yield IntegerField::new('num_nim')->setRequired(false)->setColumns(3);
            // yield AssociationField::new('installation')->setColumns(3)
            //     ->setRequired(false)
            //     ->setFormTypeOptions([
            //         'query_builder'=>function(InstallationRepository $installation)
            //         {
            //             return $installation->createQueryBuilder('i')
            //             ->orderBy('i.nom', 'ASC');
            //         }
            //     ]);

            // yield TextField::new('nomDeLaBase')->hideOnForm()->setColumns(3);
            // yield TextField::new('user')->hideOnForm()->setColumns(3);
            // yield TextField::new('passBase')->hideOnForm()->setColumns(3);
            

        // ];
    }


    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Entreprise) return;

        $entityInstance->setNomDeLaBase();
        $entityInstance->setPassBase();
        $entityInstance->setUser();
        $entityInstance->setCodeEntreprise();


        $pass = $entityInstance->getPassword();
        // $passh = hashPassword($pass);
        $plainPassword = $pass;
        $passh = password_hash($plainPassword, PASSWORD_DEFAULT);
        $entityInstance->setPassword($passh);
       
        parent::persistEntity($em, $entityInstance);        
    }

   


    #[Route('/admin/entreprise', name: 'assoc_inst_ent')]
    public function list(EntrepriseRepository $repo, InstallationRepository $repoi): Response
    {
        // Code pour générer la liste de toutes les entreprises
        $entreprises = $repo->findAll();
        $installations = $repoi->findAll();
        return $this->render('admin/entreprise/list.html.twig', [
            'entreprises' => $entreprises,
            'installations' => $installations
        ]);
    }


   

}