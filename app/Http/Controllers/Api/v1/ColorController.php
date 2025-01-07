<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Api\v1\BaseAPI;
use App\Services\ColorSV;
use App\Http\Requests\StoreColorRequest;  // Import the StoreColorRequest
use App\Http\Requests\UpdateColorRequest; // Import the UpdateColorRequest
use Illuminate\Http\Request;

class ColorController extends BaseAPI
{
    protected $colorSV;

    public function __construct()
    {
        $this->colorSV = new ColorSV();
    }

    // Get all colors
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $colors = $this->colorSV->ColorList($params);
            return $this->sendResponse($colors, 'Colors retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Create a new color
    public function store(request $request)
    {
        try {
            $param['name'] = $request->input('name');
            $param['code'] = $request->input('code');
            $param['created_by'] = $request->input('created_by');
            $color = $this->colorSV->ColorCreate($param);

            return $this->sendResponse($color, 'Color created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update($id, request $request)
    {
        try {
            $param['name'] = $request->input('name');
            $param['code'] = $request->input('code');
            $param['updated_by'] = $request->input('updated_by');

            $color = $this->colorSV->ColorUpdate($id, $param);

            return $this->sendResponse($color, 'Color updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    // Delete a color
    public function destroy($id)
    {
        try {
            $color = $this->colorSV->ColorDelete($id);
            return $this->sendResponse($color, 'Color deleted successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Show details of a single color
    public function show($id)
    {
        try {
            $color = $this->colorSV->ColorDetail($id);
            return $this->sendResponse($color, 'Color details retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
