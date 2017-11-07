<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Common\Persistence\Database;
use DomainShop\Entity\Order;
use DomainShop\Entity\Pricing;
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

    public function __construct(RouterInterface $router, TemplateRendererInterface $renderer)
    {
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
            'orderAmount' => $pricing->getAmount()
        ]));

        return $response;
    }
}
