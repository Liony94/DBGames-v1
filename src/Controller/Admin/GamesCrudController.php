<?php

namespace App\Controller\Admin;

use App\Entity\Games;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GamesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Games::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
