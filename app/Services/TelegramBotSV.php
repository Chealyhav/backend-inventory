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

    /**
     * Sends a product action notification to Telegram.
     *
     * @param array $params Parameters containing action, product_name, quantity, and optional description.
     * @return array The response from the Telegram API.
     * @throws Exception If the notification fails to send.
     */
    public function sendTrackerProduct($params = [])
    {
        try {
            // Validate required parameters
            if (!isset($params['action'], $params['product_name'], $params['quantity'])) {
                throw new Exception('Missing required parameters: action, product_name, or quantity.');
            }

            // Extract parameters
            $action = $params['action'];
            $productName = $params['product_name'];
            $quantity = $params['quantity'];
            $description = $params['description'] ?? null;

            // Format the message based on the action
            $message = $this->formatMessage($action, $productName, $quantity, $description);

            // Get Telegram bot token and chat ID from environment variables
            $token = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID_DEV');

            if (!$token || !$chatId) {
                throw new Exception('Telegram bot token or chat ID is missing in the environment configuration.');
            }

            // Build the Telegram API URL
            $url = "https://api.telegram.org/bot{$token}/sendMessage";

            // Send the message to Telegram
            $response = $this->client->post($url, [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ],
                'verify' => false, // Disable SSL verification (not recommended for production)
            ]);

            // Log the response for debugging
            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Telegram Response:', $result);

            return $result;
        } catch (Exception $e) {
            // Log the error and rethrow it
            Log::error('Failed to send Telegram notification:', ['error' => $e->getMessage()]);
            throw new Exception('Failed to send tracker product notification: ' . $e->getMessage());
        }
    }

    /**
     * Formats the Telegram message based on the action.
     *
     * @param string $action The action type (e.g., sell, add_stock, unstock).
     * @param string $productName The name of the product.
     * @param int $quantity The quantity involved in the action.
     * @param string|null $description Optional description for additional context.
     * @return string The formatted message.
     */
    protected function formatMessage($action, $productName, $quantity, $description = null)
    {
        // Common message structure
        $message = "<b>Product Action Notification</b>\n";
        $message.= "  \n";
        $message = "<b>{$this->getActionEmoji($action)}  {$this->getActionLabel($action)}</b>\n";
        $message.= "  \n";
        $message .= "📦  Product: {$productName}\n";
        $message.= "  \n";
        $message .= "🔢  Quantity: {$quantity}\n";
        $message.= "  \n";
        // Add description if provided
        if ($description) {
            $message .= "📝 Description: {$description}\n";
        }

        // Add status or additional notes
        switch ($action) {
            case 'sell':
                $message .= "✅ Status: Sold successfully!";

                break;
            case 'add_stock':
                $message .= "✅ Status: Stock updated successfully!";
                break;
            case 'unstock':
                $message .= "⚠️ Status: Stock removed!";
                break;
            case 'checkStock':
                $message .= "ℹ️ Note: Please pre-order more stock to avoid shortages.";
                break;
            case 'adjust_stock':
                $message .= "✅ Status: Stock updated successfully!";
                break;
            default:
                $message .= "⚙️ Action: {$action}";
        }

        return $message;
    }

    /**
     * Returns the emoji associated with the given action.
     *
     * @param string $action The action type.
     * @return string The corresponding emoji.
     */
    private function getActionEmoji($action)
    {
        switch ($action) {
            case 'sell': return '🛒';
            case 'add_stock': return '➕';
            case 'unstock': return '➖';
            case 'checkStock': return '⚠️';
            case 'adjust_stock': return '🔄';
            default: return '❓';
        }
    }

    /**
     * Returns the label associated with the given action.
     *
     * @param string $action The action type.
     * @return string The corresponding label.
     */
    private function getActionLabel($action)
    {
        switch ($action) {
            case 'sell': return 'Product Sold';
            case 'add_stock': return 'Stock Added';
            case 'unstock': return 'Stock Removed';
            case 'checkStock': return 'Low Stock Alert';
            case 'adjust_stock': return 'Stock Adjusted';
            default: return 'Unknown Action';
        }
    }
}
