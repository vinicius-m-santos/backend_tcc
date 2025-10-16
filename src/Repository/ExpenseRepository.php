<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 */
class ExpenseRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Expense::class);

        $this->em = $em;
    }

    public function add(Expense $expense, bool $flush = true): Expense
    {
        $this->em->persist($expense);

        if ($flush) {
            $this->em->flush();
        }

        return $expense;
    }

    public function delete(Expense $expense): void
    {
        $this->em->remove($expense);
        $this->em->flush();
    }

    public function getMonthExpensesRelatedToLastMonth(int $companyId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            WITH current_month AS (
                SELECT
                    COALESCE(SUM(e.total), 0) AS total
                FROM
                    expense e
                WHERE
                    e.created_at >= :start
                AND e.created_at < :end
                AND e.company_id = :par_company
            ),
            last_month AS (
                SELECT
                    COALESCE(SUM(e.total), 0) AS total
                FROM
                    expense e
                WHERE
                    e.created_at >= (:start - INTERVAL '1 MONTH')
                AND e.created_at < (:end - INTERVAL '1 MONTH')
                AND e.company_id = :par_company
            )
            SELECT
                current_month.total AS current_month_total,
                last_month.total AS last_month_total
            FROM current_month, last_month
        ";

        $stmt = $conn->prepare($sql);

        $start = (new \DateTimeImmutable('first day of this month 00:00:00'))->format('Y-m-d H:i:s');
        $end = (new \DateTimeImmutable('first day of next month 00:00:00'))->format('Y-m-d H:i:s');

        $result = $stmt->executeQuery([
            "start" => $start,
            "end" => $end,
            "par_company" => $companyId
        ]);

        return $result->fetchAssociative();
    }
//    /**
//     * @return Expense[] Returns an array of Expense objects
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

//    public function findOneBySomeField($value): ?Expense
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
