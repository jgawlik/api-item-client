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

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(ClientInterface $http)
    {
        $this->http = $http;
    }

    public function getByParams(array $params): array
    {
        $this->optionsResolverForGetByParams();
        $options = $this->optionsResolver->resolve($params);
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
        $this->optionsResolverForAddUpdateImte();
        $options = $this->optionsResolver->resolve($postParams);
        try {
            $response = $this->http->request('POST', self::ITEMS_URL, [
                'form_params' => $options,
            ]);
        } catch (ClientException $clientException) {
            $data = json_decode((string)$clientException->getResponse()->getBody(), true);
            throw new ItemClientException($data['errors']['message'], $clientException->getCode());
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function update(array $postParams): array
    {
        $this->optionsResolverForAddUpdateImte();
        $options = $this->optionsResolver->resolve($postParams);
        try {
            $response = $this->http->request('PATCH', self::ITEMS_URL, [
                'form_params' => $options,
            ]);
        } catch (ClientException $clientException) {
            $data = json_decode((string)$clientException->getResponse()->getBody(), true);
            throw new ItemClientException($data['errors']['message'], $clientException->getCode());
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function remove(int $itemId): array
    {
        try {
            $response = $this->http->request('DELETE', self::ITEMS_URL . '/' . $itemId);
        } catch (ServerException $serverException) {
            throw new ItemServerException($serverException->getMessage(), $serverException->getCode(), $serverException);
        }

        return json_decode((string)$response->getBody(), true);
    }


    private function optionsResolverForGetByParams()
    {
        $this->optionsResolver = new OptionsResolver();
        $this->defineAmountEquals();
        $this->defineAmountGreater();
    }

    private function optionsResolverForAddUpdateImte()
    {
        $this->optionsResolver = new OptionsResolver();
        $this->defineAmount();
        $this->defineName();
    }

    private function defineAmount()
    {
        $this->optionsResolver->setDefined('amount');
        $this->optionsResolver->setAllowedTypes('amount', 'int');
    }

    private function defineName()
    {
        $this->optionsResolver->setDefined('name');
        $this->optionsResolver->setAllowedTypes('name', 'string');
    }

    private function defineAmountEquals()
    {
        $this->optionsResolver->setDefined('amount_equals');
        $this->optionsResolver->setAllowedTypes('amount_equals', 'int');
    }

    private function defineAmountGreater()
    {
        $this->optionsResolver->setDefined('amount_greater');
        $this->optionsResolver->setAllowedTypes('amount_greater', 'int');
    }
}
