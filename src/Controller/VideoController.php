<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\Type\VideoType;
use App\Repository\VideoRepository;
use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VideoController extends AbstractController
{
    #[Route('/', name: 'video')]
    public function index(RequestStack $requestStack, VideoRepository $videoRepository, VideoService $videoService): Response
    {
        $request = $requestStack->getCurrentRequest();
        $videoForm = $this->createForm(VideoType::class, $videoRepository->new());
        $videoForm->handleRequest($request);

        if ($videoForm->isSubmitted()) {
           return  $videoService->handleVideoForm($videoForm);
        }

        return $this->render('video/index.html.twig',
            [
                'videoForm' => $videoForm->createView(),

            ]
        );
    }
}
