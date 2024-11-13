<?php

namespace App\Service;

use App\Entity\Video;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoService
{
    public function __construct()
    {
    }

    public function handleVideoForm(FormInterface $videoForm)
    {
        if ($videoForm->isSubmitted()) {
           $this->handleValidForm($videoForm);
        } else {
            $this->handleInvalidForm($videoForm);
        }
    }

    public function handleValidForm(FormInterface $videoForm)
    {
        /** @var Video $video */
        $video = $videoForm->getData();

        /** @var UploadedFile $uploadThumbnail */
        $uploadThumbnail = $videoForm->get('thumbnail')->getData();

        /** @var UploadedFile $videoFile */
        $videoFile = $videoForm->get('videoFile')->getData();
    }

    public function handleInvalidForm(FormInterface $videoForm)
    {

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
}