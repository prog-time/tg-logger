<?php

namespace ProgTime\TgLogger;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;

class TgLoggerMethods
{
    /**
     * Bot Token
     * @var string
     */
    private string $botToken;

    /**
     * Chat ID for logs
     * @var int
     */
    private int $chatId;

    public function __construct()
    {
        try {
            if (empty(config('tg-logger.token'))) {
                throw new \Exception('Bot token is missing');
            }
            $this->botToken = config('tg-logger.token');

            if (empty(config('tg-logger.chat_id'))) {
                throw new \Exception('ID chat is missing');
            }
            $this->chatId = (int)config('tg-logger.chat_id');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Sending POST requests
     * @param string $urlQuery
     * @param array $queryParams
     * @return array|null
     */
    protected function postQuery(string $urlQuery, array $queryParams = []): ?array
    {
        try {
            $ch = curl_init($urlQuery);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryParams);
            $resultQuery = curl_exec($ch);
            curl_close($ch);

            if (empty($resultQuery)) {
                throw new \Exception('Запрос вызвал ошибку');
            }

            return json_decode($resultQuery, true) ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Sending requests to Telegram
     * @param string $method - request method
     * @param array $queryParams - request parameters
     * @return array|null
     */
    public function telegramQuery(string $method, array $queryParams = []): ?array
    {
        try {
            $urlQuery = "https://api.telegram.org/bot{$this->botToken}/{$method}";

            $queryParams['chat_id'] = $this->chatId;
            $resultQuery = $this->postQuery($urlQuery, $queryParams);

            if (empty($resultQuery)) {
                throw new \Exception('Запрос вызвал ошибку');
            }

            return $resultQuery;
        } catch (\Exception $e) {
            return null;
        }
    }
}
