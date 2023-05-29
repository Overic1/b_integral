<?php

namespace App\Controller\Admin;

use App\Entity\Installation;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
// use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InstallationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Installation::class;
    }

    //  public function configureCrud(Crud $crud): Crud
    // {
    //     return $crud
    //         // ...

    //         ->overrideTemplate('crud/index', 'admin/index.html.twig')

    //         ->overrideTemplates([
    //             // 'crud/field/text' => 'admin/product/field_id.html.twig',
    //             // 'label/null' => 'admin/labels/my_null_label.html.twig',
    //         ])
    //     ;
    // }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),  // Masquer sur le formulaire d'édition
            TextField::new('nom'),
            TextField::new('serveur'),
            TextField::new('domaine'),
            TextField::new('sous_domaine'),
            TextField::new('dossier')->setRequired(false),

            // TextEditorField::new('description'),
        ];
    }
}