<?php

namespace App\Controller\Brand;

use App\Controller\Core\ApiResourceController;
use App\Entity\Brand\Brand;
use App\Entity\Form\Brand\BrandType;
use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;

class BrandController extends ApiResourceController
{
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        parent::__construct($entityManager, $paginator);
    }

    /**
     * @Rest\Get(path="/brands")
     * @Rest\View(serializerGroups={"api"})
     * @param $request
     * @return View
     */
    public function getBrands($request): View
    {
        $query = $this->entityManager->getRepository(Brand::class)->findAllBrands();

        $items = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->contentResponse($items);
    }

    /**
     * @Rest\Get(path="/brands/{name}")
     * @Rest\View(serializerGroups={"api"})
     * @param string $name
     * @return View
     */
    public function getBrandByName(string $name): View
    {
        $result = $this->entityManager->getRepository(Brand::class)->findOneBy(['name' => $name]);

        if ($result) {
            return $this->contentResponse($result);
        }

        return $this->notFoundResponse();
    }

    /**
     * @Rest\Get(path="/brands/{brand}")
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
     * @Rest\Post(path="/brands")
     * @Rest\View(serializerGroups={"api"})
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function postCreateBrand(Request $request): View
    {
        $brand = new Brand();

        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($brand);
            $this->entityManager->flush();

            return $this->contentResponse($brand);
        }

        return $this->badRequestResponse($this->getFormErrors($form));
    }

    /**
     * @Rest\Patch("/brands/{id}")
     * @Rest\View(serializerGroups={"api"})
     * @param Request $request
     * @param Brand $brand
     * @return View
     */
    public function patchBrand(Request $request,Brand $brand): View
    {
        $form = $this->createForm(Brand::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($brand);
            $this->entityManager->flush();

            return $this->contentResponse($brand);
        }

        return $this->badRequestResponse($this->getFormErrors($form));
    }

    /**
     * @Rest\Delete(path="/brands/{id}")
     * @param Brand $brand
     * @return View
     */
    public function deleteBrand(Brand $brand): View
    {
        if ($brand instanceof Brand) {
            $this->entityManager->remove($brand);
            $this->entityManager->flush();

            return $this->noContentResponse();
        }

        return $this->badRequestResponse();
    }
}
