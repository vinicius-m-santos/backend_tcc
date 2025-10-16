<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/sale', methods: ['POST'])]
class SaleController extends AbstractController
{
    private SaleRepository $saleRepository;
    private ProductRepository $productRepository;
    private NormalizerInterface $normalizer;

    public function __construct(SaleRepository $saleRepository, ProductRepository $productRepository, NormalizerInterface $normalizer)
    {
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->normalizer = $normalizer;
    }

    #[Route('/create', name: 'create_sale', methods: ['POST'])]
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

        if (!isset($data['quantity']) || !trim($data['quantity'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['products']) || !count($data['products'])) {
            throw new UnprocessableEntityHttpException('No products found');
        }

        $sale = new Sale();
        $sale->setName($data['name']);
        $sale->setDescription($data['description'] ?? '');
        $sale->setQuantity($data['quantity']);

        $total = 0;
        foreach ($data['products'] as $productId) {
            $product = $this->productRepository->find($productId);
            if (!$product) {
                throw new UnprocessableEntityHttpException('Product not found');
            }

            $sale->addProduct($product);
            $total += (float) $product->getPrice();
        }

        $sale->setTotal($total);
        $sale->setCompany($user->getCompany());

        $sale = $this->saleRepository->add($sale);
        $normalizedData = $this->normalizer->normalize($sale, 'json', ['groups' => 'sale_all']);

        return new JsonResponse(['status' => 'Sale created', 'sale' => $normalizedData], 201);
    }

    #[Route('/all', name: 'get_all_sales', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $sales = $this->saleRepository->findBy(['company' => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($sales, 'json', ['groups' => 'sale_list']);

        return new JsonResponse(['sales' => $normalizedData], 200);
    }

    #[Route('/products', name: 'get_all_products_for_sale', methods: ['GET'])]
    public function getAllProducts(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $products = $this->productRepository->findBy(['company' => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($products, 'json', ['groups' => 'product_all']);

        return new JsonResponse(['products' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'delete_sale', methods: ['DELETE'])]
    public function delete(Sale $sale): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $this->saleRepository->delete($sale);

        return new JsonResponse(['status' => 'Sale Deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update_sale', methods: ['PUT'])]
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

        if (!isset($data['quantity']) || !trim($data['quantity'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $sale = $this->saleRepository->find($id);
        $sale->setName($data['name']);
        $sale->setDescription($data['description'] ?? '');
        $sale->setQuantity($data['quantity']);
        $sale->clearProducts();

        $total = 0;
        foreach ($data['products'] as $productId) {
            $product = $this->productRepository->find($productId);
            if (!$product) {
                throw new UnprocessableEntityHttpException('Product not found');
            }

            $sale->addProduct($product);
            $total += (float) $product->getPrice();
        }

        $sale->setTotal($total);

        $sale = $this->saleRepository->add($sale);
        $normalizedData = $this->normalizer->normalize($sale, 'json', ['sale_all']);

        return new JsonResponse(['status' => 'Sale updated', 'sale' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'get_sale', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $sale = $this->saleRepository->findOneBy(['id' => $id, 'company' => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($sale, 'json', ['sale_all']);

        return new JsonResponse(['sale' => $normalizedData], 200);
    }
}
