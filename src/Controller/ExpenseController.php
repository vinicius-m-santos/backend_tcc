<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Repository\ExpenseCategoryRepository;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/expense', methods: ['POST'])]
class ExpenseController extends AbstractController
{
    private ExpenseRepository $expenseRepository;
    private ExpenseCategoryRepository $expenseCategoryRepository;
    private NormalizerInterface $normalizer;

    public function __construct(ExpenseRepository $expenseRepository, ExpenseCategoryRepository $expenseCategoryRepository, NormalizerInterface $normalizer)
    {
        $this->expenseRepository = $expenseRepository;
        $this->expenseCategoryRepository = $expenseCategoryRepository;
        $this->normalizer = $normalizer;
    }

    #[Route('/create', name: 'create_expense', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['name']) || !trim($data['name'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['description']) || !trim($data['description'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['total']) || !trim($data['total'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['category'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $expense = new Expense();
        $expense->setName($data['name']);
        $expense->setDescription($data['description'] ?? '');
        $expense->setTotal($data['total']);

        $expenseCategory = $this->expenseCategoryRepository->find($data['category']);

        if (!$expenseCategory === null) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $expense->setCompany($user->getCompany());
        $expense->setExpenseCategory($expenseCategory);

        $expense = $this->expenseRepository->add($expense);
        $normalizedData = $this->normalizer->normalize($expense, 'json', ['expense_all']);

        return new JsonResponse(['status' => 'Expense created', 'expense' => $normalizedData], 201);
    }

    #[Route('/category/create', name: 'create_expense_category', methods: ['POST'])]
    public function addExpenseCategory(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['name']) || !trim($data['name'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $expenseCategory = new ExpenseCategory();
        $expenseCategory->setName($data['name']);
        $expenseCategory->setCompany($user->getCompany());

        $expenseCategory = $this->expenseCategoryRepository->add($expenseCategory);
        $normalizedData = $this->normalizer->normalize($expenseCategory, 'json', ['expense_category_all']);

        return new JsonResponse(['status' => 'Expense Category created', 'expense_category' => $normalizedData], 201);
    }

    #[Route('/category/all', name: 'get_all_expense_categories', methods: ['GET'])]
    public function allExpenseCategories(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $expenseCategories = $this->expenseCategoryRepository->findBy(["company" => $user->getCompany()], ["id" => "ASC"]);
        $normalizedData = $this->normalizer->normalize($expenseCategories, 'json', ['expense_category_all']);

        return new JsonResponse(['expense_categories' => $normalizedData], 200);
    }

    #[Route('/category/{id}', name: 'update_expense_category', methods: ['PUT'])]
    public function updateExpenseCategory(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['name']) || !trim($data['name'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $expenseCategory = $this->expenseCategoryRepository->find($id);
        $expenseCategory->setName($data['name']);

        $expenseCategory = $this->expenseCategoryRepository->add($expenseCategory);
        $normalizedData = $this->normalizer->normalize($expenseCategory, 'json', ['expense_category_all']);

        return new JsonResponse(['status' => 'Expense Category updated', 'expense_category' => $normalizedData], Response::HTTP_OK);
    }

    #[Route('/category/{id}', name: 'delete_expense_category', methods: ['DELETE'])]
    public function deleteExpenseCategory(ExpenseCategory $expenseCategory): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $this->expenseCategoryRepository->delete($expenseCategory);

        return new JsonResponse(['status' => 'Expense Category deleted'], Response::HTTP_OK);
    }

    #[Route('/category/{id}', name: 'get_expense_category', methods: ['GET'])]
    public function getExpenseCategory(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $expense = $this->expenseCategoryRepository->findOneBy(["id" => $id, "company" => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($expense, 'json', ['expense_category_all']);

        return new JsonResponse(['expense_category' => $normalizedData], 200);
    }

    #[Route('/all', name: 'get_all_expenses', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $expenses = $this->expenseRepository->findBy(["company" => $user->getCompany()], ["id" => "ASC"]);
        $normalizedData = $this->normalizer->normalize($expenses, 'json', ['expense_all']);

        return new JsonResponse(['expenses' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'delete_expense', methods: ['DELETE'])]
    public function delete(Expense $expense): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $this->expenseRepository->delete($expense);

        return new JsonResponse(['status' => 'Expense Deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update_expense', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['name']) || !trim($data['name'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['description']) || !trim($data['description'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['total']) || !trim($data['total'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $expense = $this->expenseRepository->find($id);
        $expense->setName($data['name']);
        $expense->setDescription($data['description'] ?? '');
        $expense->setTotal($data['total']);

        $expense = $this->expenseRepository->add($expense);
        $normalizedData = $this->normalizer->normalize($expense, 'json', ['expense_all']);

        return new JsonResponse(['status' => 'Expense updated', 'expense' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'get_expense', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $expense = $this->expenseRepository->findOneBy(["id" => $id, "company" => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($expense, 'json', ['expense_all']);

        return new JsonResponse(['expense' => $normalizedData], 200);
    }
}
