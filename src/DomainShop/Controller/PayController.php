<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Common\Persistence\Database;
use DomainShop\Clock;
use DomainShop\Entity\Order;
use DomainShop\Entity\Pricing;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Swap\Builder;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

final class PayController implements MiddlewareInterface
{
    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    public function __construct(Clock $clock, RouterInterface $router, TemplateRendererInterface $renderer)
    {
        $this->clock = $clock;
        $this->router = $router;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $orderId = $request->getAttribute('orderId');

        /** @var Order $order */
        $order = Database::retrieve(Order::class, (string)$orderId);

        /** @var Pricing $pricing */
        $pricing = Database::retrieve(Pricing::class, $order->getDomainNameExtension());

        if ($order->getPayInCurrency() !== $pricing->getCurrency()) {
            $swap = (new Builder())
                ->add('fixer')
                ->build();
            $rate = $swap->historical($pricing->getCurrency() . '/' . $order->getPayInCurrency(), $this->clock->now());

            $currency = $order->getPayInCurrency();
            $amount = $pricing->getAmount() * $rate->getValue();
        } else {
            $currency = $pricing->getCurrency();
            $amount = $pricing->getAmount();
        }

        if ($request->getMethod() === 'POST') {
            $submittedData = $request->getParsedBody();
            if (isset($submittedData['pay'])) {
                $order->setWasPaid(true);
                Database::persist($order);
            }

            return new RedirectResponse(
                $this->router->generateUri('finish', ['orderId' => $orderId])
            );
        }

        $response->getBody()->write($this->renderer->render('pay.html.twig', [
            'orderId' => $orderId,
            'domainName' => $order->getDomainName(),
            'currency' => $currency,
            'amount' => $amount
        ]));

        return $response;
    }
}
