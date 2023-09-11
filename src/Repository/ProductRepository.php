<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function findAllByFilters($filter, $min, $max, $categories)
    {
        $tags = [
            "price_desc" => "p.price desc",
            "price_asc" => "p.price asc",
            "name_desc" => "p.name desc",
            "name_asc" => "p.name asc"
        ];

        $qb = $this->createQueryBuilder("p");
        if($categories) {
            $orX = $qb->expr()->orX();
            $i = 0;
            foreach ($categories as $category) {
                $i++;
                $orX->add($qb->expr()->isMemberOf(':category' . $i, 'p.categories'));
                $qb->setParameter('category' . $i, $category);
            }
            $qb->andWhere($orX);
        }
        $qb
            ->andWhere("p.price BETWEEN :min AND :max")
            ->setParameter("min", $min)
            ->setParameter("max", $max);


        if (array_key_exists($filter, $tags)) {
            $qb->add("orderBy", ($tags[$filter]));
        }


        return $qb->getQuery()->getResult();
    }




    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */


    public function findAllByCategory($categories, $filter)
    {
        $tags = [
            "price_desc" => "p.price desc",
            "price_asc" => "p.price asc",
            "name_desc" => "p.name desc",
            "name_asc" => "p.name asc"
        ];

        //À NOTER LE 'MEMBER OF' QUI PERMET DE VERIFIER TOUTES LES CATÉGORIES (RELATION MANY TO MANY)
        // FAIRE UN SIMPLE JOIN SI RELATION MANY TO ONE
        $qb = $this->createQueryBuilder("p");
        $i = 0;
        foreach ($categories as $category) {
            $i++;
            $qb->orWhere(':category' . $i . ' MEMBER OF p.categories')
                ->setParameter('category' . $i, $category);
            echo $category->getId();
        }
        if (array_key_exists($filter, $tags)) {
            $qb->add("orderBy", ($tags[$filter]));
        }


        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */

    public function findAllLike($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :val')
            ->setParameter('val', "%" . $value . "%")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
