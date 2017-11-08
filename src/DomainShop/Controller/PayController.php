<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use DomainShop\Entity\OrderRepository;
use DomainShop\Service\PayForOrder;
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
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var PayForOrder
     */
    private $payForOrderService;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $renderer,
        OrderRepository $orderRepository,
        PayForOrder $payForOrderService
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->orderRepository = $orderRepository;
        $this->payForOrderService = $payForOrderService;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $orderId = $request->getAttribute('orderId');

        $order = $this->orderRepository->getById($orderId);

        if ($request->getMethod() === 'POST') {
            $submittedData = $request->getParsedBody();
            if (isset($submittedData['pay'])) {
                $this->payForOrderService->handle($orderId, $submittedData['currency'], (int)$submittedData['amount']);
            }

            return new RedirectResponse(
                $this->router->generateUri('finish', ['orderId' => $orderId])
            );
        }

        $response->getBody()->write($this->renderer->render('pay.html.twig', [
            'orderId' => $orderId,
            'domainName' => $order->getDomainName(),
            'currency' => $order->getPrice()->currency(),
            'amount' => $order->getPrice()->amount()
        ]));

        return $response;
    }
}
