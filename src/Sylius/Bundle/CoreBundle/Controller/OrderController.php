<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\OrderBundle\Controller\OrderController as BaseOrderController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

class OrderController extends BaseOrderController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function thankYouAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $orderId = $request->getSession()->get('sylius_order_id', null);

        if (null === $orderId) {
            $options = $configuration->getParameters()->get('after_failure');

            return $this->redirectHandler->redirectToRoute(
                $configuration,
                isset($options['route']) ? $options['route'] : 'sylius_shop_homepage',
                isset($options['parameters']) ? $options['parameters'] : []
            );
        }

        $request->getSession()->remove('sylius_order_id');
        $order = $this->repository->find($orderId);
        Assert::notNull($order);

        $view = View::create()
            ->setData([
                'order' => $order
            ])
            ->setTemplate($configuration->getParameters()->get('template'))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAvailableShippingMethodsAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var OrderInterface $cart */
        $cart = $this->getCartOr404($request->attributes->get('orderId'));

        if (!$this->isCheckoutTransitionPossible($cart, 'select_shipping')) {
            throw new BadRequestHttpException('The shipment methods cannot be resolved in the current state of cart!');
        }

        $shipments = [];

        foreach ($cart->getShipments() as $shipment) {
            $shipments['shipments'][] = [
                'methods' => $this->getShippingMethodsResolver()->getSupportedMethods($shipment),
            ];
        }

        return $this->viewHandler->handle($configuration, View::create($shipments));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAvailablePaymentMethodsAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var OrderInterface $cart */
        $cart = $this->getCartOr404($request->attributes->get('orderId'));

        if (!$this->isCheckoutTransitionPossible($cart, 'select_payment')) {
            throw new BadRequestHttpException('The payment methods cannot be resolved in the current state of cart!');
        }

        $payments = [];

        foreach ($cart->getPayments() as $payment) {
            $payments['payments'][] = [
                'methods' => $this->getPaymentMethodsResolver()->getSupportedMethods($payment),
            ];
        }

        return $this->viewHandler->handle($configuration, View::create($payments));
    }

    /**
     * @return ShippingMethodsResolverInterface
     */
    protected function getShippingMethodsResolver()
    {
        return $this->get('sylius.shipping_methods_resolver');
    }

    /**
     * @return PaymentMethodsResolverInterface
     */
    protected function getPaymentMethodsResolver()
    {
        return $this->get('sylius.payment_methods_resolver');
    }

    /**
     * @param mixed $cartId
     *
     * @return OrderInterface
     */
    protected function getCartOr404($cartId)
    {
        $cart = $this->get('sylius.repository.order')->findCartById($cartId);

        if (null === $cart) {
            throw new NotFoundHttpException();
        }

        return $cart;
    }

    /**
     * @param OrderInterface $cart
     * @param string $transition
     *
     * @return bool
     */
    private function isCheckoutTransitionPossible(OrderInterface $cart, $transition)
    {
        return $this->get('sm.factory')->get($cart, 'sylius_order_checkout')->can($transition);
    }
}
