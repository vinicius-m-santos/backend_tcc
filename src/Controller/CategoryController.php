<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/category', methods: ['POST'])]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ProductRepository $productRepository;
    private NormalizerInterface $normalizer;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, NormalizerInterface $normalizer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->normalizer = $normalizer;
    }

    #[Route('/create', name: 'create_category', methods: ['POST'])]
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

        $category = new Category();
        $category->setName($data['name']);
        $category->setDescription($data['description'] ?? '');
        $category->setActive(!!$data['active']);
        $category->setCompany($user->getCompany());

        $category = $this->categoryRepository->add($category);
        $normalizedData = $this->normalizer->normalize($category, 'json', ['category_all']);

        return new JsonResponse(['status' => 'Category created', 'category' => $normalizedData], 201);
    }

    #[Route('/all', name: 'get_all_categories', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $categories = $this->categoryRepository->findBy(["company" => $user->getCompany()], ["id" => "ASC"]);
        $normalizedData = $this->normalizer->normalize($categories, 'json', ['category_all']);

        return new JsonResponse(['categories' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function delete(Category $category): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $product = $this->productRepository->findOneBy(["category" => $category->getId()]);
        if ($product) {
            throw new UnprocessableEntityHttpException('There are products related to category');
        }
        $this->categoryRepository->delete($category);

        return new JsonResponse(['status' => 'Category Deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update_category', methods: ['PUT'])]
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

        $category = $this->categoryRepository->find($id);
        $category->setName($data['name']);
        $category->setDescription($data['description'] ?? '');
        $category->setActive(!!$data['active']);

        $category = $this->categoryRepository->add($category);
        $normalizedData = $this->normalizer->normalize($category, 'json', ['category_all']);

        return new JsonResponse(['status' => 'Category updated', 'category' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'get_category', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $category = $this->categoryRepository->findOneBy(["id" => $id, "company" => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($category, 'json', ['category_all']);

        return new JsonResponse(['category' => $normalizedData], 200);
    }
}
