<?php

namespace App\Services;

use App\Models\SystemSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;

class MailbuxService
{
    const ENDPOINT_AUTH = 'api/v1/auth/';
    const ENDPOINT_USERS = 'api/v1/accounts/';
    const ENDPOINT_SINGLE_USER = 'api/v1/accounts/%s/';

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(
        string $username,
        string $password,
        string $baseUrl
    ) {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
        $this->httpClient = new Client();
    }

    /**
     * @param bool   $withAuth
     * @param string $method
     * @param string $uri
     * @param array  $parameters
     * @param bool   $catchExceptions
     *
     * @throws GuzzleException
     * @throws \Exception
     *
     * @return array
     */
    private function sendRequest(
        bool $withAuth,
        string $method,
        string $uri,
        array $parameters = [],
        bool $catchExceptions = true
    ): array {
        $body = null;
        $url = sprintf('%s/%s', $this->baseUrl, $uri);

        if (!empty($parameters)) {
            if ($method == self::METHOD_GET) {
                $url = sprintf('%s/%s?%s', $this->baseUrl, $uri, http_build_query($parameters));
            } else {
                $body = json_encode($parameters);
            }
        }

        try {
            $response = $this->httpClient
                ->send(
                    new Request(
                        $method,
                        $url,
                        $this->headers($withAuth),
                        $body
                    )
                );

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode($response->getBody()->getContents(), true) ?? [];
            }
        } catch (GuzzleException  $exception) {
            if ($withAuth && $exception->getCode() == 401) {
                $reAuth = $this->reAttemptAuth();
                if ($reAuth) {
                    return $this->sendRequest(...func_get_args());
                }
            }

            if (!$catchExceptions) {
                if (method_exists($exception, 'getResponse')) {
                    $response = $exception->getResponse();

                    if ($response instanceof Response) {
                        $responseBody = json_decode($response->getBody()->getContents(), true);
                        if (!empty($responseBody['message'])) {
                            throw new \Exception($responseBody['message']);
                        }
                    }
                }
                throw $exception;
            }
        }

        return [];
    }

    /**
     * @throws GuzzleException
     *
     * @return array
     */
    public function getUsers(): array
    {
        $users = [];
        $start = 0;

        do {
            $result = $this->sendRequest(
                true,
                self::METHOD_GET,
                self::ENDPOINT_USERS,
                [
                    'start' => $start,
                ]
            );

            $start += 50;

            $users = array_merge($users, $result['accounts'] ?? []);
        } while (!empty($result));

        return $users;
    }

    /**
     * @param bool $withAuth
     *
     * @return string[]
     */
    private function headers(bool $withAuth): array
    {
        $headers = [
            'cache-control' => 'no-cache',
            'content-type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($withAuth) {
            $headers['Authorization'] = sprintf(
                'Basic %s',
                base64_encode(
                    sprintf('%s:%s', $this->username, SystemSetting::getSetting('mailbux_auth_token'))
                )
            );
        }

        return $headers;
    }

    /**
     * @throws GuzzleException
     *
     * @return null|string
     */
    public function obtainAuth(): ?string
    {
        $responseBody = $this->sendRequest(
            false,
            self::METHOD_POST,
            self::ENDPOINT_AUTH, [
                'username' => $this->username,
                'password' => $this->password,
            ]);

        $authToken = Arr::get($responseBody, 'auth.auth_token');

        if ($authToken) {
            SystemSetting::setSetting('mailbux_auth_token', $authToken);
            SystemSetting::setSetting('mailbux_auth_token_last_update', time());

            return $authToken;
        }

        return null;
    }

    /**
     * @param string $username
     * @param null   $error
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function deleteUser(string $username, &$error = null): bool
    {
        try {
            $this->sendRequest(
                true,
                self::METHOD_DELETE,
                sprintf(self::ENDPOINT_SINGLE_USER, $username),
                [],
                false
            );

            return true;
        } catch (GuzzleException $exception) {
            return false;
        }
    }

    /**
     * @throws GuzzleException
     *
     * @return bool
     */
    private function reAttemptAuth(): bool
    {
        $lastAttempt = (int) SystemSetting::getSetting('mailbux_reauth_last_attempt');
        $now = time();

        if (($now - $lastAttempt) < 30) {
            return false;
        }

        $newToken = $this->obtainAuth();

        if ($newToken) {
            SystemSetting::setSetting('mailbux_reauth_last_attempt', $now);

            return true;
        }

        return false;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $recovery_email
     * @param string $perm_level
     * @param bool   $api_access
     * @param bool   $enabled
     * @param array  $additionalInfo
     *
     * @throws GuzzleException
     *
     * @return array
     */
    public function storeUser(
        string $email,
        string $password,
        string $recovery_email,
        string $perm_level,
        bool $api_access,
        bool $enabled,
        array $additionalInfo = []
    ): array {
        return $this->sendRequest(
            true,
            self::METHOD_POST,
            self::ENDPOINT_USERS,
            array_merge([
                'username' => $email,
                'password' => $password,
                'perm_level' => $perm_level,
                'api_access' => (int) $api_access,
                'enabled' => (int) $enabled,
                'recovery_email' => $recovery_email,
                'language' => 'en',
            ], $additionalInfo),
            false
        );
    }

    /**
     * @param string      $username
     * @param null|string $password
     * @param bool        $apiAccess
     * @param bool        $enabled
     * @param string      $recoveryEmail
     * @param string      $language
     * @param int         $storageQuotaTotal
     *
     * @throws GuzzleException
     *
     * @return array
     */
    public function updateMailUser(
        string $username,
        ?string $password,
        bool $apiAccess,
        bool $enabled,
        string $recoveryEmail,
        string $language,
        int $storageQuotaTotal
    ): array {
        $data = [
            'api_access' => (int) $apiAccess,
            'enabled' => (int) $enabled,
            'recovery_email' => $recoveryEmail,
            'language' => $language,
            'storagequota_total' => $storageQuotaTotal,
        ];

        if (!is_null($password)) {
            $data['password'] = $password;
        }

        return $this->sendRequest(
            true,
            self::METHOD_PATCH,
            sprintf(self::ENDPOINT_SINGLE_USER, $username),
            $data,
            false
        );
    }

    /**
     * @return string
     */
    public function getMailHost(): string
    {
        return parse_url($this->baseUrl, PHP_URL_HOST);
    }
}
