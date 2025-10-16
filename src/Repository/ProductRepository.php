<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Product::class);

        $this->em = $em;
    }

    public function add(Product $product, bool $flush = true): Product
    {
        $this->em->persist($product);

        if ($flush) {
            $this->em->flush();
        }

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->em->remove($product);
        $this->em->flush();
    }

    public function getQuantityInStock(int $companyId): mixed
    {
        return $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.quantityInStock), 0)')
            ->where('p.company = :par_company')
            ->setParameter('par_company', $companyId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLowStockProducts(int $companyId): mixed
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.quantityInStock <= 10')
            ->andWhere('p.company = :par_company')
            ->setParameter('par_company', $companyId)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
