<?php

namespace Worm\SiteBundle\Image;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ValidatorInterface;
use Worm\SiteBundle\Entity\Submission;
use Worm\SiteBundle\Exception\InvalidImageException;

class ImageManager
{

    protected static $allowedMimeTypes = array('image/png', 'image/jpeg');

    protected $rootPath;
    protected $webPath;
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     * @param $rootPath
     * @param $webPath
     */
    public function __construct(ValidatorInterface $validator, $rootPath, $webPath)
    {
        $this->rootPath = $rootPath;
        $this->webPath = $webPath;
        $this->validator = $validator;
    }

    /**
     * @param UploadedFile $file
     * @param Submission $submission
     * @throws InvalidImageException
     * @throws \Exception
     */
    public function register(UploadedFile $file, Submission $submission)
    {
        $fs = new Filesystem();

        if (!$file->isValid()) {
            throw new InvalidImageException('Uploaded file is invalid. Error no. ' . $file->getError());
        }

        if (!in_array($file->getMimeType(), static::$allowedMimeTypes)) {
            throw new InvalidImageException('Uploaded file is not an image but a ' . $file->getMimeType() . ' file');
        }

        $hash = sha1_file($file->getRealPath());
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();
        $dimensions = getimagesize($file->getRealPath());

        $submission->setExtension($extension);
        $submission->setHash($hash);
        $submission->setSize($size);
        $submission->setWidth($dimensions[0]);
        $submission->setHeight($dimensions[1]);

        $violations = $this->validator->validate($submission);
        if ($violations->count() > 0) {
            throw new InvalidImageException('Image does not respect worm constraints', $violations);
        }

        $name = $submission->getFilename();
        $imageDir = rtrim($this->rootPath, '/') . '/' . $submission->getWorm()->getId();
        if (!$fs->exists($imageDir)) {
            $fs->mkdir($imageDir);
        }

        if ($fs->exists($imageDir . '/' . $name)) {
            throw new \Exception('Uploaded file already exists');
        }

        $file->move($imageDir, $name);
    }

    /**
     * @param Submission $submission
     * @return string
     */
    public function getImageUrl(Submission $submission)
    {
        return implode(
            '/',
            array(
                '',
                trim($this->webPath, '/'),
                $submission->getWorm()->getId(),
                $submission->getFilename()
            )
        );
    }

}