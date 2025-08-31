<?php

declare(strict_types=1);

namespace DeFixIT\Anonlytics;

use DeFixIT\Anonlytics\Exception\AnonlyticsDataNotSendException;

readonly class Tracker
{
    private const TRACK_HOST = 'https://api.anonlytics.eu/track';

    public function __construct(
        private array $server,
        private string $clientToken,
        private string $siteToken,
    ) {
    }

    /**
     * @throws AnonlyticsDataNotSendException
     */
    public function sendRequestData(): bool
    {
        try {
            $curl = curl_init();
            if ($curl === false) {
                throw new AnonlyticsDataNotSendException('Failed to initialize cURL');
            }

            curl_setopt_array($curl, [
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_URL => self::TRACK_HOST,
                CURLOPT_HTTPHEADER => $this->getHeaders(),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $this->getPostData(),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);

            $result = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if ($result === false) {
                $error = curl_error($curl);
                curl_close($curl);
                throw new AnonlyticsDataNotSendException("cURL error: {$error}");
            }
            
            curl_close($curl);
            
            if ($httpCode >= 400) {
                throw new AnonlyticsDataNotSendException("HTTP error: {$httpCode}");
            }
        } catch (AnonlyticsDataNotSendException $e) {
            throw $e;
        } catch (\Throwable $exception) {
            throw new AnonlyticsDataNotSendException(
                'Error while sending data to host: ' . self::TRACK_HOST,
                0,
                $exception
            );
        }

        return true;
    }

    private function getHeaders(): array
    {
        return [
            'Content-type: application/json',
            'anonlytics-client-token: ' . $this->clientToken,
            'anonlytics-site-token: ' . $this->siteToken,
        ];
    }

    private function getPostData(): string
    {
        return json_encode([
            'server_software' => $this->server['SERVER_SOFTWARE'] ?? null,
            'server_protocol' => $this->server['SERVER_PROTOCOL'] ?? null,
            'server_name' => $this->server['SERVER_NAME'] ?? null,
            'uri' => $this->server['REQUEST_URI'] ?? null,
            'method' => $this->server['REQUEST_METHOD'] ?? null,
            'http_user_agent' => $this->server['HTTP_USER_AGENT'] ?? null,
            'http_accept_language' => $this->server['HTTP_ACCEPT_LANGUAGE'] ?? null,
            'http_referer' => $this->server['HTTP_REFERER'] ?? null,
            'country' => $this->getCountry($this->server['REMOTE_ADDR'] ?? ''),
        ], JSON_THROW_ON_ERROR);
    }

    private function getCountry(string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Anonlytics-PHP-Library/1.0',
                ],
            ]);
            
            $response = file_get_contents(
                "https://www.geoplugin.net/json.gp?ip={$ip}",
                false,
                $context
            );
            
            if ($response === false) {
                return null;
            }
            
            $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            return $data['geoplugin_countryCode'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }
}