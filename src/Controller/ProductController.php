<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/product')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private NormalizerInterface $normalizer;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository, NormalizerInterface $normalizer)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->normalizer = $normalizer;
    }

    #[Route('/create', name: 'create_product', methods: ['POST'])]
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

        if (!isset($data['price']) || !trim($data['price'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['category']) || !trim($data['category'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if ($data['stock'] != 0 && (!isset($data['stock']) || !trim($data['stock']))) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description'] ?? '');
        $product->setPrice($data['price'] ?? 0);

        $category = $this->categoryRepository->find($data['category']);
        if (!$category) {
            throw new UnprocessableEntityHttpException('Category not found');
        }
        $product->setCategory($category);
        $product->setQuantityInStock($data["stock"] ?? 0);
        $product->setActive(!!$data['active']);
        $product->setCompany($user->getCompany());

        $product = $this->productRepository->add($product);
        $normalizedData = $this->normalizer->normalize($product, 'json', ['product_all']);

        return new JsonResponse(['status' => 'Product created', 'product' => $normalizedData], 201);
    }

    #[Route('/all', name: 'get_all_products', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $products = $this->productRepository->findBy(["company" => $user->getCompany()], ["id" => "ASC"]);
        $normalizedData = $this->normalizer->normalize($products, 'json', ['product_all']);

        return new JsonResponse(['products' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $this->productRepository->delete($product);

        return new JsonResponse(['status' => 'Product Deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update_product', methods: ['PUT'])]
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

        if (!isset($data['price']) || !trim($data['price'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['category']) || !trim($data['category'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if ($data['stock'] != 0 && (!isset($data['stock']) || !trim($data['stock']))) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $product = $this->productRepository->find($id);
        if (!$product) {
            throw new UnprocessableEntityHttpException('Product not found');
        }

        $product->setName($data['name']);
        $product->setDescription($data['description'] ?? '');
        $product->setPrice($data['price'] ?? 0);

        $category = $this->categoryRepository->find($data['category']);
        if (!$category) {
            throw new UnprocessableEntityHttpException('Category not found');
        }
        $product->setCategory($category);
        $product->setQuantityInStock($data["stock"] ?? 0);
        $product->setActive(!!$data['active']);

        $product = $this->productRepository->add($product);
        $normalizedData = $this->normalizer->normalize($product, 'json', ['product_all']);

        return new JsonResponse(['status' => 'Product updated', 'product' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'get_product', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $product = $this->productRepository->findOneBy(["id" => $id, "company" => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($product, 'json', ['product_all']);

        return new JsonResponse(['product' => $normalizedData], 200);
    }
}
