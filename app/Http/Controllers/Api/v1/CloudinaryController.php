<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CloudinarySv;
use App\Http\Controllers\Api\v1\BaseAPI;

class CloudinaryController extends BaseAPI
{
    protected $cloudinary;

    public function __construct(CloudinarySv $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // Upload a file to Cloudinary
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $file = $request->file('image');
            $imageUrl = $this->cloudinary->uploadImage($file);

            return $this->successResponse(['url' => $imageUrl], 'Image uploaded successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Delete an image from Cloudinary
    public function deleteImage(Request $request)
    {
        try {
            $request->validate([
                'public_id' => 'required|string',
            ]);

            $publicId = $request->input('public_id');
            $this->cloudinary->deleteImage($publicId);

            return $this->successResponse(null, 'Image deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Get an image URL from Cloudinary
    public function getImageUrl(Request $request)
    {
        try {
            $request->validate([
                'public_id' => 'required|string',
            ]);
            $publicId = $request->input('public_id');
            $imageUrl = $this->cloudinary->getImageUrl($publicId);
            return $this->successResponse(['url' => $imageUrl], 'Image URL retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
