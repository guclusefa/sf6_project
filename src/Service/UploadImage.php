<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadImage
{
    // test log in console
    public function logTest($message): void
    {
        $log = fopen('log.txt', 'a+');
        fwrite($log, $message . PHP_EOL);
        fclose($log);
    }

    // upload image personne
    public function uploadImagePersonne($image, $slugger, $targetDirectory): string
    {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
        try {
            $image->move(
                $targetDirectory,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $newFilename;
    }

    // delete existing image personne if new image is uploaded
    public function deleteImagePersonne($image, $targetDirectory): void
    {
        if ($image) {
            unlink($targetDirectory . '/' . $image);
        }
    }
}