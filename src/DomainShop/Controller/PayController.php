<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Common\Persistence\Database;
use DomainShop\Entity\Order;
use DomainShop\Service\PayForOrder;
use DomainShop\Service\PricingService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

final class PayController implements MiddlewareInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * @var PricingService
     */
    private $pricingService;

    /**
     * @var PayForOrder
     */
    private $payForOrderService;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $renderer,
        PricingService $pricingService,
        PayForOrder $payForOrderService
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->pricingService = $pricingService;
        $this->payForOrderService = $payForOrderService;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $orderId = $request->getAttribute('orderId');

        /** @var Order $order */
        $order = Database::retrieve(Order::class, $orderId);

        $price = $this->pricingService->getPriceForDomainNameExtension($order->getDomainNameExtension(), $order->getPayInCurrency());

        if ($request->getMethod() === 'POST') {
            $submittedData = $request->getParsedBody();
            if (isset($submittedData['pay'])) {
                $this->payForOrderService->handle($orderId);
            }

            return new RedirectResponse(
                $this->router->generateUri('finish', ['orderId' => $orderId])
            );
        }

        $response->getBody()->write($this->renderer->render('pay.html.twig', [
            'orderId' => $orderId,
            'domainName' => $order->getDomainName(),
            'currency' => $price->currency(),
            'amount' => $price->amount()
        ]));

        return $response;
    }
}
