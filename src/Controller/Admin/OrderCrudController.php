<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\LineOrderType;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        
        $config = parent::configureFields($pageName);
        $config[]=
            CollectionField::new('lineOrders')
            ->setEntryType(LineOrderType::class)
            ->allowAdd(true)
            ->allowDelete(false);
        return $config;
    }
    
}































