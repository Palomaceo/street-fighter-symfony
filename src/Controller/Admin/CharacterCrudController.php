<?php

namespace App\Controller\Admin;

use App\Entity\Character;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CharacterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Character::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            IntegerField::new('strength'),
            IntegerField::new('speed'),
            IntegerField::new('durability'),
            IntegerField::new('power'),
            IntegerField::new('combat'),
            AssociationField::new('user'),
        ];
    }
    
}
