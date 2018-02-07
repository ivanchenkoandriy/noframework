<?php

namespace app\models;

use app\Response;
use claviska\SimpleImage;
use FileUpload\FileSystem\Simple as FileSystemSimple;
use FileUpload\FileUpload;
use FileUpload\PathResolver\Simple as PathResolverSimple;
use FileUpload\Validator\Simple as ValidatorSimple;

/**
 * Image
 *
 * @author Andri Ivanchenko
 */
class Image {

    /**
     * Relative path to image
     *
     * @var string
     */
    private $relativePath = '';

    /**
     * Upload image
     *
     * @return $this
     */
    public function upload() {
        // Simple validation (max file size 2MB and only two allowed mime types)
        $validator = new ValidatorSimple('2M', ['image/png', 'image/jpg', 'image/jpeg',
            'image/gif']);

        // Simple path resolver, where uploads will be put
        $pathresolver = new PathResolverSimple(ROOT_PATH . 'public/uploads');

        // The machine's filesystem
        $filesystem = new FileSystemSimple();

        // FileUploader itself
        $fileupload = new FileUpload($_FILES['image'], $_SERVER);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        $fileupload->addValidator($validator);

        $fileupload->processAll();
        $files = $fileupload->getFiles();

        $file = null;
        if (count($files) > 0) {
            $file = reset($files);
        }

        if (NO_FILE_WAS_UPLOADED === $file->errorCode) {
            return Response::createSuccess('');
        }

        /* @var $file \FileUpload\File */
        if ($file && empty($file->error)) {
            $imagePathname = $file->getPathname();
            (new SimpleImage($imagePathname))
                    ->bestFit(getenv('PICTURE_WIDTH'), getenv('PICTURE_HEIGHT'))
                    ->toFile($imagePathname);

            $this->relativePath = 'uploads/' . $file->getFilename();

            $result = Response::createSuccess('Success.');
        } else {
            $result = Response::createFail('Could not load file: ' . $file->error . '.');
        }

        return $this;
    }

    /**
     * Set relative path from string
     *
     * @param string $relativePath
     */
    public function setRelativePath(string $relativePath) {
        $this->relativePath = $relativePath;
    }

    /**
     * Get relative path
     *
     * @return string
     */
    public function getRelativePath(): string {
        return $this->relativePath;
    }

    /**
     * Is image exists
     *
     * @return bool
     */
    public function exists(): bool {
        return '' !== $this->relativePath && file_exists(ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . $this->relativePath);
    }

    /**
     * Get URL
     *
     * @param string $url
     * @return string
     */
    public function getUrl(string $url = '/'): string {
        return $url . $this->relativePath;
    }

    /**
     * Remove image
     *
     * @return Response
     */
    public function remove(): Response {
        if ($this->exists()) {
            $fs = new FileSystemSimple();
            try {
                $fs->unlink(ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . $this->relativePath);
            } catch (\Exception $ex) {
                return Response::createFail('Error: ' . $ex->getMessage() . '!');
            }
        }

        return Response::createSuccess('Success!');
    }

}
