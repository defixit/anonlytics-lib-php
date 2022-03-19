<?php

declare(strict_types=1);

namespace DeFixIT\Anonlytics;

use DeFixIT\Anonlytics\Exception\AnonlyticsDataNotSendException;
use function curl_init;
use function curl_setopt;
use function curl_exec;
use function file_get_contents;
use function json_decode;
use function json_encode;

class Tracker
{
    private const TRACK_HOST = 'https://api.anonlytics.eu/track';
    private array $server;
    private string $clientToken;
    private string $siteToken;

    public function __construct(array $server, string $clientToken, string $siteToken)
    {
        $this->server = $server;
        $this->clientToken = $clientToken;
        $this->siteToken = $siteToken;
    }

    /**
     * @throws AnonlyticsDataNotSendException
     */
    public function sendRequestData(): bool
    {
        try {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_URL, self::TRACK_HOST);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getPostData());
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            curl_exec($curl);
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
        return json_encode(
            [
                'server_software' => $this->server['SERVER_SOFTWARE'] ?? null,
                'server_protocol' => $this->server['SERVER_PROTOCOL'] ?? null,
                'server_name' => $this->server['SERVER_NAME'] ?? null,
                'uri' => $this->server['REQUEST_URI'] ?? null,
                'method' => $this->server['REQUEST_METHOD'] ?? null,
                'http_user_agent' => $this->server['HTTP_USER_AGENT'] ?? null,
                'http_accept_language' => $this->server['HTTP_ACCEPT_LANGUAGE'] ?? null,
                'http_referer' => $this->server['HTTP_REFERER'] ?? null,
                'country' => $this->getCountry($this->server['REMOTE_ADDR']) ?? null,
            ]
        );
    }

    private function getCountry(string $ip): ?string
    {
        return json_decode(
                file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $ip)
            )->geoplugin_countryCode ?? null;
    }
}