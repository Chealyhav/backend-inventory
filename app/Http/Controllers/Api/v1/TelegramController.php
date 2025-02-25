<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\TelegramBotSV;
use Illuminate\Http\Request;

class TelegramController extends BaseAPI
{
    protected $telegramBot;

    public function __construct()
    {
        $this->telegramBot = new TelegramBotSV();
    }

    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $response = $this->telegramBot->sendTrackerProduct($params);
            return $this->successResponse($response, 'Message sent successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
