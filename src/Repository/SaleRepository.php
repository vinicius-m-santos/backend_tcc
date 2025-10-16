<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Sale::class);

        $this->em = $em;
    }

    public function add(Sale $sale, bool $flush = true): Sale
    {
        $this->em->persist($sale);

        if ($flush) {
            $this->em->flush();
        }

        return $sale;
    }

    public function delete(Sale $sale): void
    {
        $this->em->remove($sale);
        $this->em->flush();
    }

    public function findAllWithProducts(): ?array
    {
        $sales = $this->createQueryBuilder('s')
            ->leftJoin('s.products', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();

        // foreach ($sales as $sale) {
        //     $sale->getProducts()->toArray();
        // }

        return $sales;
    }

    public function getSalesByDate(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.createdAt >= :start')
            ->andWhere('s.createdAt < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function getMonthSalesRelatedToLastMonth(int $companyId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            WITH current_month AS (
                SELECT
                    COALESCE(SUM(s.total), 0) AS total
                FROM
                    sale s
                WHERE
                    s.created_at >= :start
                AND s.created_at < :end
                AND s.company_id = :par_company
            ),
            last_month AS (
                SELECT
                    COALESCE(SUM(s.total), 0) AS total
                FROM
                    sale s
                WHERE
                    s.created_at >= (:start - INTERVAL '1 MONTH')
                AND s.created_at < (:end - INTERVAL '1 MONTH')
                AND s.company_id = :par_company
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

    public function getLastSixMonthsSales(int $companyId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            WITH months AS (
                SELECT generate_series(
                    date_trunc('month', CURRENT_DATE) - interval '5 months',
                    date_trunc('month', CURRENT_DATE),
                    interval '1 month'
                ) AS month_start
            )
            SELECT 
                to_char(m.month_start, 'Month') AS month,
                COALESCE(SUM(s.quantity), 0) AS total_quantity
            FROM months m
            LEFT JOIN sale s
                ON date_trunc('month', s.created_at) = m.month_start
            WHERE s.company_id = :par_company
            GROUP BY m.month_start
            ORDER BY m.month_start;
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            "par_company" => $companyId
        ]);

        return $result->fetchAllAssociative();
    }

    public function getTopFiveMostSold(int $companyId): array
    {
        $qb = $this->createQueryBuilder('s');

        return $qb->select(
                [
                    'sum(s.quantity) as total_sold',
                    'p.id',
                    'p.name',
                    'p.quantityInStock',
                    'sum(s.quantity * p.price) as total_sales'
                ]
            )
            ->innerJoin('s.products', 'p')
            ->where($qb->expr()->eq('s.company', $companyId))
            ->andWhere($qb->expr()->eq('p.company', $companyId))
            ->groupBy('p.id, p.name')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Sale[] Returns an array of Sale objects
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

//    public function findOneBySomeField($value): ?Sale
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
