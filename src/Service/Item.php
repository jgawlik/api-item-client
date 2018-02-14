<?php

declare(strict_types=1);

namespace ApiClient\Service;

use ApiClient\Exception\ItemClientException;
use ApiClient\Exception\ItemServerException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Item implements ItemInterface
{
    private $http;
    private const ITEMS_URL = '/v1/items';

    public function __construct(ClientInterface $http)
    {
        $this->http = $http;
    }

    public function get(int $itemId): array
    {
        try {
            $response = $this->http->request('GET', self::ITEMS_URL . "/{$itemId}");
        } catch (ClientException $clientException) {
            $data = json_decode((string)$clientException->getResponse()->getBody(), true);

            throw new ItemClientException($data['errors']['message'], $clientException->getCode());
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function getByParams(array $params): array
    {
        $optionsResolver = $this->optionsResolverForGetByParams();
        $options = $optionsResolver->resolve($params);
        try {
            $response = $this->http->request('GET', self::ITEMS_URL, [
                'query' => $options
            ]);
        } catch (ClientException $clientException) {
            $data = json_decode((string)$clientException->getResponse()->getBody(), true);

            throw new ItemClientException($data['errors']['message'], $clientException->getCode());
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function add(array $postParams): array
    {
        $optionsResolver = $this->optionsResolverForAddUpdateImte();
        $options = $optionsResolver->resolve($postParams);
        try {
            $response = $this->http->request('POST', self::ITEMS_URL, [
                'form_params' => $options,
            ]);
        } catch (ClientException $clientException) {
            throw new ItemClientException($clientException->getMessage(), $clientException->getCode());
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function update(array $patchParams, int $itemId): void
    {
        $optionsResolver = $this->optionsResolverForAddUpdateImte();
        $options = $optionsResolver->resolve($patchParams);
        try {
            $this->http->request('PATCH', self::ITEMS_URL . "/{$itemId}", [
                'form_params' => $options,
            ]);
        } catch (ClientException $clientException) {
            throw new ItemClientException($clientException->getMessage(), $clientException->getCode());
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }
    }

    public function remove(int $itemId): void
    {
        try {
            $this->http->request('DELETE', self::ITEMS_URL . '/' . $itemId);
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }
    }

    private function optionsResolverForGetByParams()
    {
        $optionsResolver = new OptionsResolver();
        $this->defineAmountEquals($optionsResolver);
        $this->defineAmountGreater($optionsResolver);

        return $optionsResolver;
    }

    private function optionsResolverForAddUpdateImte()
    {
        $optionsResolver = new OptionsResolver();
        $this->defineAmount($optionsResolver);
        $this->defineName($optionsResolver);

        return $optionsResolver;
    }

    private function defineAmount(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefined('amount');
        $optionsResolver->setAllowedTypes('amount', 'int');
    }

    private function defineName(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefined('name');
        $optionsResolver->setAllowedTypes('name', 'string');
    }

    private function defineAmountEquals(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefined('amount_equals');
        $optionsResolver->setAllowedTypes('amount_equals', 'int');
    }

    private function defineAmountGreater(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefined('amount_greater');
        $optionsResolver->setAllowedTypes('amount_greater', 'int');
    }
}
