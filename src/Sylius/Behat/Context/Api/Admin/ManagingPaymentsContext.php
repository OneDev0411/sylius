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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Webmozart\Assert\Assert;

final class ManagingPaymentsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @Given I am browsing payments
     * @When I browse payments
     */
    public function iAmBrowsingPayments(): void
    {
        $this->client->index('payments');
    }

    /**
     * @When I complete the payment of order :order
     */
    public function iCompleteThePaymentOfOrder(OrderInterface $order): void
    {
        $payment = $order->getLastPayment();
        Assert::notNull($payment);

        $this->client->applyTransition(
            'payments',
            (string) $payment->getId(),
            PaymentTransitions::TRANSITION_COMPLETE
        );
    }

    /**
     * @When I choose :state as a payment state
     */
    public function iChooseAsAPaymentState(string $state): void
    {
        $this->client->buildFilter(['state' => $state]);
    }

    /**
     * @When I choose :channel as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(ChannelInterface $channel): void
    {
        $this->client->buildFilter(['order.channel.code' => $channel->getCode()]);
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter('payments');
    }

    /**
     * @Then I should see a single payment in the list
     * @Then I should see :count payments in the list
     */
    public function iShouldSeePaymentsInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getResponse()), $count);
    }

    /**
     * @Then the payment of the :orderNumber order should be :paymentState for :customer
     */
    public function thePaymentOfTheOrderShouldBeFor(
        string $orderNumber,
        string $paymentState,
        CustomerInterface $customer
    ): void {
        $payments = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->getResponse(),
            'state',
            StringInflector::nameToLowercaseCode($paymentState)
        );

        foreach ($payments as $payment) {
            $this->client->showByIri($payment['order']);
            $response = $this->client->getResponse();

            if (!$this->responseChecker->hasValue($response, 'number', $orderNumber)) {
                continue;
            }

            if (
                $this->responseChecker->relatedResourceHasValue($response, 'customer', 'email', $customer->getEmail())
            ) {
                return;
            }
        }

        throw new \InvalidArgumentException('There is no payment with given data.');
    }

    /**
     * @Then /^I should see payment for (the "[^"]+" order) as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldSeePaymentForTheOrderInTheList(string $orderNumber, int $position): void
    {
        Assert::true($this->responseChecker->hasItemOnPositionWithValue(
            $this->client->getResponse(), $position - 1, 'order', sprintf('/new-api/orders/%s', $orderNumber)
        ));
    }

    /**
     * @Then I should be notified that the payment has been completed
     */
    public function iShouldBeNotifiedThatThePaymentHasBeenCompleted(): void
    {
        Assert::true($this->client->isUpdateSuccessful());
    }

    /**
     * @Then I should see the payment of order :order as :paymentState
     */
    public function iShouldSeeThePaymentOfOrderAs(OrderInterface $order, string $paymentState): void
    {
        $payment = $order->getLastPayment();
        Assert::notNull($payment);

        $this->client->show('payments', (string) $payment->getId());
        Assert::true($this->client->responseHasValue('state', StringInflector::nameToLowercaseCode($paymentState)));
    }

    /**
     * @Then I should see (also) the payment of the :order order
     */
    public function iShouldSeeThePaymentOfTheOrder(OrderInterface $order): void
    {
        Assert::true($this->client->hasItemWithValue('order', $this->iriConverter->getIriFromItem($order)));
    }

    /**
     * @Then I should not see the payment of the :order order
     */
    public function iShouldNotSeeThePaymentOfTheOrder(OrderInterface $order): void
    {
        Assert::false($this->client->hasItemWithValue('order', $this->iriConverter->getIriFromItem($order)));
    }
}
