<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Client;

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ApiPlatformClient implements ApiClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var array */
    private $request = ['url' => null, 'body' => []];

    /** @var array */
    private $filters;

    public function __construct(AbstractBrowser $client, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    public function setResource(string $resource): void
    {
        $this->request['url'] = '/new-api/'.$resource;
    }

    public function index(): void
    {
        $this->request('GET', $this->request['url'], ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function showRelated(string $resource): void
    {
        $this->request('GET', $this->getResponseContentValue($resource), ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function showByIri(string $iri): void
    {
        $this->request('GET', $iri, ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function show(string $id): void
    {
        $this->request('GET', sprintf('%s/%s', $this->request['url'], $id), ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function subResourceIndex(string $subResource, string $id): void
    {
        $this->request('GET', sprintf('%s/%s/%s', $this->request['url'], $id, $subResource), ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function buildCreateRequest(): void
    {
        $this->request['method'] = 'POST';
    }

    public function buildUpdateRequest(string $id): void
    {
        $this->show($id);

        $this->request['method'] = 'PUT';
        $this->request['url'] = sprintf('%s/%s', $this->request['url'], $id);
        $this->request['body'] = json_decode($this->client->getResponse()->getContent(), true);
    }

    public function buildFilter(array $filters): void
    {
        $this->filters = $filters;
    }

    /** @param string|int $value */
    public function addRequestData(string $key, $value): void
    {
        $this->request['body'][$key] = $value;
    }

    public function addCompoundRequestData(array $data): void
    {
        $this->request['body'] = array_merge_recursive($this->request['body'], $data);
    }

    public function updateRequestData(array $data): void
    {
        $this->request['body'] = $this->mergeArraysUniquely($this->request['body'], $data);
    }

    public function create(): void
    {
        $content = json_encode($this->request['body']);

        $this->request($this->request['method'], $this->request['url'], ['CONTENT_TYPE' => 'application/json'], $content);
    }

    public function update(): void
    {
        $content = json_encode($this->request['body']);

        $this->request($this->request['method'], $this->request['url'], ['CONTENT_TYPE' => 'application/ld+json'], $content);
    }

    public function delete(string $id): void
    {
        $this->request('DELETE', sprintf('%s/%s', $this->request['url'], $id), []);
    }

    public function filter(string $resource): void
    {
        $query = http_build_query($this->filters, '', '&', PHP_QUERY_RFC3986);
        $path = sprintf('/new-api/%s?%s', $resource, $query);

        $this->request('GET', $path, ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function applyTransition(string $id, string $transition): void
    {
        $this->request(
            'PATCH',
            sprintf('%s/%s/%s', $this->request['url'], $id, $transition),
            ['CONTENT_TYPE' => 'application/merge-patch+json'],
            '{}'
        );
    }

    public function countCollectionItems(): int
    {
        return (int) $this->getResponseContentValue('hydra:totalItems');
    }

    public function getCollectionItems(): array
    {
        return $this->getResponseContentValue('hydra:member');
    }

    public function getCollectionItemsWithValue(string $key, string $value): array
    {
        $items = array_filter($this->getCollectionItems(), function (array $item) use ($key, $value): bool {
            return $item[$key] === $value;
        });

        return $items;
    }

    public function getError(): string
    {
        return $this->getResponseContentValue('hydra:description');
    }

    public function isCreationSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_CREATED;
    }

    public function isUpdateSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_OK;
    }

    public function isDeletionSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    /** @param string|int $value */
    public function responseHasValue(string $key, $value): bool
    {
        return $this->getResponseContentValue($key) === $value;
    }

    /** @param string|int $value */
    public function relatedResourceHasValue(string $resource, string $key, $value): bool
    {
        $this->showRelated($resource);

        return $this->getResponseContentValue($key) === $value;
    }

    /** @param string|float $value */
    public function hasItemWithValue(string $key, $value): bool
    {
        foreach ($this->getCollectionItems() as $resource) {
            if ($resource[$key] === $value) {
                return true;
            }
        }

        return false;
    }

    public function hasItemOnPositionWithValue(int $position, string $key, string $value): bool
    {
        return $this->getCollectionItems()[$position][$key] === $value;
    }

    public function hasItemWithTranslation(string $locale, string $key, string $translation): bool
    {
        foreach ($this->getCollectionItems() as $resource) {
            if (
                isset($resource['translations']) &&
                isset($resource['translations'][$locale]) &&
                $resource['translations'][$locale][$key] === $translation
            ) {
                return true;
            }
        }

        return false;
    }

    private function request(string $method, string $url, array $headers, string $content = null): void
    {
        $defaultHeaders = ['HTTP_ACCEPT' => 'application/ld+json'];
        if ($this->sharedStorage->has('token')) {
            $defaultHeaders['HTTP_Authorization'] = 'Bearer ' . $this->sharedStorage->get('token');
        }

        $this->client->request($method, $url, [], [], array_merge($defaultHeaders, $headers), $content);
    }

    private function getResponseContentValue(string $key)
    {
        $content = json_decode($this->client->getResponse()->getContent(), true);

        Assert::keyExists($content, $key);

        return $content[$key];
    }

    private function mergeArraysUniquely(array $firstArray, array $secondArray): array
    {
        foreach ($secondArray as $key => $value) {
            if (is_array($value) && is_array(@$firstArray[$key])) {
                $value = $this->mergeArraysUniquely($firstArray[$key], $value);
            }
            $firstArray[$key] = $value;
        }
        return $firstArray;
    }
}
