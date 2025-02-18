<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramBotSV extends BaseService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    // Existing method for sending product action notifications
    public function sendTrackerProduct($params = array())
    {
        try {
            $token = env('TELEGRAM_BOT_TOKEN');  // Get the bot token from .env
            $chatId = env('TELEGRAM_CHAT_ID_DEV');  // Get the group chat ID from .env

            $url = "https://api.telegram.org/bot{$token}/sendMessage";

            // Ensure all necessary parameters exist in the array
            if (!isset($params['action'], $params['product_name'], $params['quantity'])) {
                throw new Exception('Missing required parameters: action, product_name, or quantity.');
            }

            // Extract the parameters from the input
            $action = $params['action'];
            $productName = $params['product_name'];
            $quantity = $params['quantity'];

            // Format the message based on the action
            $message = $this->formatMessage($action, $productName, $quantity);

            // Send the message to Telegram
            $response = $this->client->post($url, [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ],
                'verify' => false
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Telegram Response: ', $result);
            return $result;

        } catch (Exception $e) {
            // Handle any errors by throwing an exception
            throw new Exception('Failed to send tracker product notification: ' . $e->getMessage());
        }
    }

    // Existing method to format the Telegram message
    protected function formatMessage($action, $productName, $quantity)
    {
        switch ($action) {
            case 'sell':
                return "<b>Product Sold</b>\nProduct: {$productName}\nQuantity: {$quantity}\nStatus: Sold successfully!";
            case 'add_stock':
                return "<b>Stock Added</b>\nProduct: {$productName}\nQuantity: {$quantity}\nStatus: Stock updated!";
            case 'unstock':
                return "<b>Stock Removed</b>\nProduct: {$productName}\nQuantity: {$quantity}\nStatus: Stock removed!";
            case "checkStock":
                // You may want to return a custom message for stock checks (e.g., low stock)
                return "⚠️ Low stock alert for SubProduct: {$productName}.\nCurrent stock: {$quantity}.\nPlease pre-order more stock.";
            default:
                return "<b>Unknown Action</b>\nAction: {$action}\nProduct: {$productName}\nQuantity: {$quantity}";
        }
    }
}
