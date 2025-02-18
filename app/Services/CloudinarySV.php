<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use InvalidArgumentException;

class CloudinarySv
{
    /**
     * Upload an image to Cloudinary.
     *
     * @param  mixed $file  A file or an array of files
     * @return string  The secure URL of the uploaded image
     * @throws InvalidArgumentException
     */
    public function uploadImage($file)
    {
        if (is_array($file)) {
            $file = $file[0];
        }

        if (is_object($file) && method_exists($file, 'getRealPath')) {
            try {
                $uploadedFile = Cloudinary::upload($file->getRealPath());
                return $uploadedFile->getSecurePath();  // Return the secure URL
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Cloudinary upload failed: " . $e->getMessage());
            }
        }

        throw new InvalidArgumentException("The provided file is not valid.");
    }

    /**
     * Delete an image from Cloudinary.
     *
     * @param  string $publicId  The public ID of the image to delete
     * @return bool  Returns true if the image was deleted successfully
     */
    public function deleteImage($publicId)
    {
        try {
            // Only delete the image if publicId is valid
            if (!$publicId) {
                throw new InvalidArgumentException("Missing public_id for image deletion.");
            }

            $result = Cloudinary::destroy($publicId);
            return $result;  // Returns true on success, false otherwise
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Cloudinary delete failed: " . $e->getMessage());
        }
    }

    /**
     * Extract the public_id from a Cloudinary URL.
     *
     * @param  string $url  The Cloudinary image URL
     * @return string  The public ID of the image
     */
    public function extractPublicIdFromUrl($url)
    {
        if (!$url) {
            throw new InvalidArgumentException("Image URL is missing.");
        }

        // Extract the public_id from the URL
        $parsedUrl = parse_url($url);
        $path = ltrim($parsedUrl['path'], '/');  // Remove leading slash
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get the URL of an image from Cloudinary.
     *
     * @param  string $publicId  The public ID of the image
     * @return string  The URL of the image
     */
    public function getImageUrl($publicId)
    {
        try {
            $url = Cloudinary::getUrl($publicId);
            return $url;
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Cloudinary URL retrieval failed: " . $e->getMessage());
        }
    }
}


