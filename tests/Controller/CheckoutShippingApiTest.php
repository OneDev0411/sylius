<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CheckoutShippingApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_order_shipping_selection_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/checkouts/select-shipping/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/checkouts/select-shipping/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_order_that_is_not_addressed()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $url = sprintf('/api/checkouts/select-shipping/%d', $checkoutData['order1']->getId());
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_invalid_order_state', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_order_without_specifying_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();

        // should be validation error, but due to some bug, it throws NotNullConstraintViolationException
        $this->assertResponseCode($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_unexisting_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": 0
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/shipping_validation_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_select_shipping_method_for_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": {$checkoutData['ups']->getId()}
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $orderId
     */
    private function addressOrder($orderId)
    {
        $this->loadFixturesFromFile('resources/countries.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $data =
<<<EOT
        {
            "shippingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "billingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentBillingAddress": true
        }
EOT;

        $url = sprintf('/api/checkouts/addressing/%d/%d', $orderId, $customers['customer_Oliver']->getId());
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }
}
