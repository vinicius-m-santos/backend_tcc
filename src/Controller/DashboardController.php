<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Repository\CompanyRepository;
use App\Repository\ExpenseCategoryRepository;
use App\Repository\ExpenseRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/dashboard', methods: ['POST'])]
class DashboardController extends AbstractController
{
    public function __construct(
        private SaleRepository $saleRepository, 
        private ProductRepository $productRepository, 
        private ExpenseRepository $expenseRepository, 
        private NormalizerInterface $normalizer,
        private ExpenseCategoryRepository $expenseCategoryRepository,
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('/sales/month', name: 'dashboard_month_sale', methods: ['GET'])]
    public function getMonthSales(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $sales = $this->saleRepository->getMonthSalesRelatedToLastMonth($user->getCompany()->getId());
        $normalizedData = $this->normalizer->normalize($sales, 'json', ['groups' => 'sale_all']);

        return new JsonResponse(['sales' => $normalizedData], 200);
    }

    #[Route('/sales/lastSixMonths', name: 'dashboard_last_six_months_sales', methods: ['GET'])]
    public function getSalesLastSixMonths(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $sales = $this->saleRepository->getLastSixMonthsSales($user->getCompany()->getId());
        $normalizedData = $this->normalizer->normalize($sales, 'json', ['groups' => 'sale_all']);

        return new JsonResponse(['sales' => $normalizedData], 200);
    }

    #[Route('/expense/expensePerCategory', name: 'dashboard_expense_per_category', methods: ['GET'])]
    public function getExpensePerCategory(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $expensePerCategory = $this->expenseCategoryRepository->getExpensePerCategory($user->getCompany()->getId());
        $normalizedData = $this->normalizer->normalize($expensePerCategory, 'json', ['groups' => 'expense_category_all']);

        return new JsonResponse(['expense_per_category' => $normalizedData], 200);
    }

    #[Route('/sales/topFiveMostSold', name: 'dashboard_top_five_most_sold', methods: ['GET'])]
    public function getTopFiveMostSold(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $mostSoldProducts = $this->saleRepository->getTopFiveMostSold($user->getCompany()->getId());
        $normalizedData = $this->normalizer->normalize($mostSoldProducts, 'json', ['groups' => 'top_five_most_sold']);

        return new JsonResponse(['top_five_most_sold' => $normalizedData], 200);
    }

    #[Route('/expenses/month', name: 'dashboard_month_expense', methods: ['GET'])]
    public function getMonthExpenses(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $expenses = $this->expenseRepository->getMonthExpensesRelatedToLastMonth($user->getCompany()->getId());
        $normalizedData = $this->normalizer->normalize($expenses, 'json', ['groups' => 'expense_all']);

        return new JsonResponse(['expenses' => $normalizedData], 200);
    }

    #[Route('/products/quantityInStock', name: 'dashboard_products_in_stock', methods: ['GET'])]
    public function getQuantityInStock(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $quantityInStock = $this->productRepository->getQuantityInStock($user->getCompany()->getId());
        $lowStockProducts = $this->productRepository->getLowStockProducts($user->getCompany()->getId());

        $stockData = [
            "quantityInStock" => $quantityInStock,
            "lowStockProducts" => $lowStockProducts
        ];

        $normalizedData = $this->normalizer->normalize($stockData, 'json', ['groups' => 'product_all']);

        return new JsonResponse(['stockData' => $normalizedData], 200);
    }

    #[Route('/all', name: 'dashboard_get_all_sales', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $sales = $this->saleRepository->findAll();
        $normalizedData = $this->normalizer->normalize($sales, 'json', ['groups' => 'sale_list']);

        return new JsonResponse(['sales' => $normalizedData], 200);
    }

    #[Route('/products', name: 'dashboard_get_all_products', methods: ['GET'])]
    public function getAllProducts(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $normalizedData = $this->normalizer->normalize($products, 'json', ['groups' => 'product_all']);

        return new JsonResponse(['products' => $normalizedData], 200);
    }
}
