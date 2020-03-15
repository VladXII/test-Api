<?php

namespace App\Controller\Product;

use App\Controller\Core\ApiResourceController;
use App\Entity\Brand\Brand;
use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use App\Form\Product\ProductType;


class ProductController extends ApiResourceController
{
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        parent::__construct($entityManager, $paginator);
    }

    /**
     * @Rest\Get(path="/products/{product}")
     * @Rest\View(serializerGroups={"api"})
     * @param Product $product
     * @return View
     */
    public function getProduct(Product $product): View
    {
        return $this->contentResponse($product);
    }

    /**
     * @Rest\Get(path="/products")
     * @Rest\View(serializerGroups={"api"})
     * @param Request $request
     * @return View
     */
    public function getProducts(Request $request): View
    {
        $query = $this->entityManager->getRepository(Product::class)->findAllProducts();

        $items = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->contentResponse($items);
    }

    /**
     * @Rest\Get(path="/products/")
     * @Rest\View(serializerGroups={"api"})
     * @param Request $request
     * @return View
     */
    public function getProductByNpm(Request $request): View
    {
        $npm = $request->query->get('npm');
        $result = $this->entityManager->getRepository(Product::class)->findBy(['npm' => $npm]);

        if ($result) {
            return $this->contentResponse($result);
        }

        return $this->notFoundResponse();
    }

    /**
     * @Rest\Get(path="/products/{brand}")
     * @Rest\View(serializerGroups={"api"})
     * @param Brand $brand
     * @param Request $request
     * @return View
     */
    public function getProductByBrand(Request $request, Brand $brand): View
    {
        $query = $this->entityManager->getRepository(Product::class)->findAllProductsByBrand($brand);

        $items = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->contentResponse($items);
    }

    /**
     * @Rest\Post(path="/products")
     * @Rest\View(serializerGroups={"api"})
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function postCreateProduct(Request $request): View
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->contentResponse($product);
        }

        return $this->badRequestResponse($this->getFormErrors($form));
    }

    /**
     * @Rest\Patch("/products/{id}")
     * @Rest\View(serializerGroups={"api"})
     * @param Request $request
     * @param Product $product
     * @return View
     */
    public function patchProduct(Request $request, Product $product): View
    {
        $form = $this->createForm(Product::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->contentResponse($product);
        }

        return $this->badRequestResponse($this->getFormErrors($form));
    }

    /**
     * @Rest\Delete(path="products/{id}")
     * @param Product $product
     * @return View
     */
    public function deleteProduct(Product $product): View
    {
        if ($product instanceof Product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();

            return $this->noContentResponse();
        }

        return $this->badRequestResponse();
    }
}
