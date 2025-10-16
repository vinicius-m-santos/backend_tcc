<?php

namespace App\Repository;

use App\Entity\ExpenseCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseCategory>
 */
class ExpenseCategoryRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, ExpenseCategory::class);

        $this->em = $em;
    }

    public function add(ExpenseCategory $expenseCategory, bool $flush = true): ExpenseCategory
    {
        $this->em->persist($expenseCategory);

        if ($flush) {
            $this->em->flush();
        }

        return $expenseCategory;
    }

    public function delete(ExpenseCategory $expenseCategory): void
    {
        $this->em->remove($expenseCategory);
        $this->em->flush();
    }

    public function getExpensePerCategory(int $companyId): array
    {
        $start = (new \DateTimeImmutable('first day of this month 00:00:00'))->format('Y-m-d H:i:s');
        $end = (new \DateTimeImmutable('first day of next month 00:00:00'))->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('ec')
            ->select('ec.name, sum(e.total) as total_amount_expenses')
            ->innerJoin('ec.expenses', 'e')
            ->where('e.createdAt >= :start')
            ->andWhere('e.createdAt < :end')
            ->andWhere('e.company = :par_company')
            ->groupBy('ec.name')
            ->setParameter(':start', $start)
            ->setParameter(':end', $end)
            ->setParameter(':par_company', $companyId)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return ExpenseCategory[] Returns an array of ExpenseCategory objects
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

//    public function findOneBySomeField($value): ?ExpenseCategory
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
