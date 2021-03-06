<?php

namespace App\Controller;

use App\Service\ReaderService;
use App\Service\ScanDirectoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
      * @Route("/", methods={"GET"}))
      * @Cache(expires="+15 minutes", public=true)
      */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
      * @Route("/scan", methods={"GET"}))
      * @Cache(expires="+2 minutes", public=true)
      */
    public function scan(ScanDirectoryService $service): JsonResponse
    {
        return new JsonResponse(
            [
                'id' => base64_encode('files'),
                'name' => 'files',
                'type' => 'folder',
                'path' => 'files',
                'items' => $service->scan($this->getParameter('kernel.project_dir') . '/public/files'),
            ]
        );
    }

    /**
     * @Route("/read/{path}", requirements={"path"=".+"}, methods={"GET"}))
     * @Cache(maxage="2 weeks", public=true)
     */
    public function read(Request $request, string $path, ReaderService $service): JsonResponse
    {
        $path = $this->getParameter('kernel.project_dir') . '/public/' . rawurldecode($path);
        $downlink = $request->headers->get('x-downlink');

        return new JsonResponse($service->read($path, $downlink));
    }

    /**
     * @Route("/preview/{path}", requirements={"path"=".+"}, methods={"GET"}))
     * @Cache(maxage="2 weeks", public=true)
     */
    public function preview(string $path, ReaderService $service): JsonResponse
    {
        $path = $this->getParameter('kernel.project_dir') . '/public/' . rawurldecode($path);

        return new JsonResponse($service->preview($path));
    }
}
