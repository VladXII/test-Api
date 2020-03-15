<?php

namespace App\Controller\Core;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\Proxy;

/**
 * Class ApiResourceController
 * @package App\Controller
 */
class ApiResourceController extends AbstractFOSRestController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * RegularTimeController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @param mixed $data
     * @return View
     */
    protected function notFoundResponse($data = null): View
    {
        return $this->view($data, Response::HTTP_NOT_FOUND);
    }

    /**
     * @param mixed $data
     * @return View
     */
    protected function badRequestResponse($data = null): View
    {
        return $this->view($data, Response::HTTP_BAD_REQUEST);
    }

    protected function forbiddenResponse($data = null): View
    {
        return $this->view($data, Response::HTTP_FORBIDDEN);
    }

    protected function conflictResponse()
    {
        return $this->view([],Response::HTTP_CONFLICT);
    }

    /**
     * @return View
     */
    protected function noContentResponse(): View
    {
        return $this->view([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed $data
     * @return View
     */
    protected function contentResponse($data = null): View
    {
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @param mixed $data
     * @return View
     */
    protected function resourceCreatedResponse($data = null): View
    {
        return $this->view($data, Response::HTTP_CREATED);
    }

    /**
     * @param View $view
     * @return Response
     */
    protected function createView(View $view): Response
    {
        return $this->handleView($view);
    }

    /**
     * @param FormInterface $form
     * @param $resource
     * @return View
     * @throws \Exception
     */
    protected function updateResource(FormInterface $form, $resource): View
    {
        $response = $this->createResourceResponse($form, $resource);

        if($response->getStatusCode() === Response::HTTP_CREATED) {
            $response->setStatusCode(Response::HTTP_OK);
        }

        return $response;
    }

    /**
     * @param FormInterface $form
     * @param $resource
     * @return mixed|null
     * @throws \Exception
     */
    protected function createResource(FormInterface $form, $resource)
    {

        if (!$this->isEntity($resource)) {
            throw new \Exception('Resource must be an entity!');
        }

        if ($form->isSubmitted() && $form->isValid() !== false) {
            $em = $this->getDoctrine()->getManager();
            try {

                $em->persist($resource);
                $em->flush();

                return $resource;
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        return null;
    }

    /**
     * @param FormInterface $form
     * @param $resource
     * @return View
     * @throws \Exception
     */
    protected function createResourceResponse(FormInterface $form, $resource): View
    {
        $resource = $this->createResource($form, $resource);

        if($resource) {
            return $this->resourceCreatedResponse($resource);
        }

        return $this->badRequestResponse();
    }

    /**
     * @param $resource
     * @return View
     */
    public function deleteResource($resource): View
    {
        if(!$this->isEntity($resource)) {
            return $this->badRequestResponse();
        }

        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($resource);
            $em->flush();

            return $this->noContentResponse();
        } catch (\Exception $exception) {
            return $this->badRequestResponse();
        }
    }

    /**
     * @param FormInterface $form
     * @param boolean $deep
     * @return array
     */
    protected function getFormErrors(FormInterface $form, $deep = true): array
    {
        $errors = $this->getErrorMessages($form);

        if(!$deep) {
            foreach ($errors as $key => $error) {
                $errors[$key] = $error[0];
            }
        }

        return $errors;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors[$form->getName()][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessageTemplate();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    /**
     * @param $class
     * @return bool
     */
    protected function isEntity($class): bool
    {
        $em = $this->getDoctrine()->getManager();
        if (is_object($class)) {
            $class = ($class instanceof Proxy)
                ? get_parent_class($class)
                : get_class($class);
        }

        return ! $em->getMetadataFactory()->isTransient($class);
    }

    /**
     * @param array $keys
     * @param array $arr
     * @return bool
     */
    protected function arrayKeysExists(array $keys, array $arr): bool
    {
       return !array_diff_key(array_flip($keys), $arr);
    }
}
