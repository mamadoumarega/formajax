<?php

namespace App\Service;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

class VideoService
{
    protected EntityManagerInterface $entityManager;
    protected Environment $environment;
    protected ParameterBagInterface $parameterBag;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Environment $environment
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Environment $environment,
        ParameterBagInterface $parameterBag
    )
    {
        $this
            ->setEntityManager($entityManager)
            ->setEnvironment($environment)
            ->setParameterBag($parameterBag)
        ;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return $this
     */
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @param Environment $environment
     * @return $this
     */
    public function setEnvironment(Environment $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return ParameterBagInterface
     */
    public function getParameterBag(): ParameterBagInterface
    {
        return $this->parameterBag;
    }

    /**
     * @param ParameterBagInterface $parameterBag
     * @return $this
     */
    public function setParameterBag(ParameterBagInterface $parameterBag): self
    {
        $this->parameterBag = $parameterBag;

        return $this;
    }

    /**
     * @param FormInterface $videoForm
     * @return JsonResponse
     */
    public function handleVideoForm(FormInterface $videoForm) : JsonResponse
    {
        if ($videoForm->isSubmitted()) {
           $this->handleValidForm($videoForm);
        } else {
            $this->handleInvalidForm($videoForm);
        }

        return new JsonResponse([]);
    }


    /**
     * @param FormInterface $videoForm
     * @return JsonResponse
     */
    public function handleValidForm(FormInterface $videoForm): JsonResponse
    {
        /** @var Video $video */
        $video = $videoForm->getData();

        /** @var UploadedFile $uploadThumbnail */
        $uploadThumbnail = $videoForm->get('thumbnail')->getData();

        /** @var UploadedFile $videoFile */
        $videoFile = $videoForm->get('videoFile')->getData();

        $thumnailDirectory = $this->getParameterBag()->get('thumbnails.upload_directory');
        $videoDirectory = $this->getParameterBag()->get('videos.upload_directory');

        if (null !== $uploadThumbnail) {
            $newFileName = $this->renameUploadedFile($uploadThumbnail, $thumnailDirectory);
            $video->setThumbnail($newFileName);
        }

        $newFileName = $this->renameUploadedFile($videoFile, $videoDirectory);
        $video->setThumbnail($newFileName);

        $em = $this->getEntityManager();

        $em->persist($video);
        $em->flush();

        return new JsonResponse(
            [
                'code' => Video::VIDEO_ADDED_SUCCESSFULLY,
                'html' => $this->getEnvironment()->render('video/video.html.twig', [
                    'video' => $video,
                ])
            ]
        );
    }

    /**
     * @param FormInterface $videoForm
     * @return JsonResponse
     */
    public function handleInvalidForm(FormInterface $videoForm)
    {
        return new JsonResponse(
            [
                'code' => Video::VIDEO_INVALID_FORM,
                'errors' => $this->getErrorMessages($videoForm),
            ]
        );
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $directory
     * @return string
     */
    public function renameUploadedFile(UploadedFile $uploadedFile, string $directory): string
    {
        $newFileName = uniqid(more_entropy: true) . ".{$uploadedFile->getClientOriginalName()}";
        $uploadedFile->move($directory, $newFileName);

        return $newFileName;
    }

    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
