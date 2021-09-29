<?php

namespace App\Model\Service\Gitlab;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

abstract class GitlabServiceAbstract
{

    const BOOLEAN_TRUE = 'true';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    protected $perPageDefault;

    public function __construct(ClientInterface $client, string $token, int $perPageDefault = 100)
    {
        $this->client = $client;
        $this->token = $token;
        $this->perPageDefault = $perPageDefault;
    }

    abstract protected function getListUrl(): string;

    abstract protected function getItemUrl(): string;

    /**
     * @param  string[]  $urlParameters      Key-value
     * @param  string[]  $requestParameters  Key-value
     *
     * @return Collection
     */
    public function getList(array $urlParameters = [], array $requestParameters = []): Collection
    {
        $data = new Collection();

        $baseUrl = $this->getListUrl();
        foreach ($urlParameters as $key => $value) {
            $baseUrl = str_replace($key, $value, $baseUrl);
        }

        $page = $requestParameters['page'] ?? null;
        $currentPage = $page ?: 1;

        do {
            $requestParameters['page'] = $currentPage;
            $requestParameters['perPage'] = $requestParameters['perPage'] ?? $this->perPageDefault;
            $parts = $this->prepareRequestParameters($requestParameters);
            $url = $baseUrl . '?' . $parts;
            try {
                $response = $this->client->get($url);
            } catch (ClientException $e) {
                // #12 Try to get Data from Group of project without group
                if ($e->getCode() == Response::HTTP_NOT_FOUND) {
                    return $data;
                }
                throw $e;
            }
            $content = $response->getBody()->getContents();

            $items = json_decode($content, true);
            $data = $data->merge($items);

            if (!empty($items)) {
                if (is_array($items)) {
                    if (count($items) < $requestParameters['perPage']){
                        break;
                    }
                } elseif ($items->count() < $requestParameters['perPage']) {
                    break;
                }
            }

            $currentPage++;
        } while (!empty($items) && !$page);

        return $data;
    }

    public function getItem(array $urlParameters = [], array $parameters = [])
    {
        // @todo
    }

    /**
     * @param  array  $requestParameters
     *
     * @return string
     */
    protected function prepareRequestParameters(array $requestParameters): string
    {
        $requestParameters['private_token'] = $requestParameters['private_token'] ?? $this->token;
        $requestParameters['per_page'] = $requestParameters['per_page'] ?? $this->perPageDefault;
        $requestParameters['order_by'] = $requestParameters['order_by'] ?? 'updated_at';
        return http_build_query($requestParameters);
    }

}