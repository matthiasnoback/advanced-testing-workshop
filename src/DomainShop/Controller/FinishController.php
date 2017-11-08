<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use DomainShop\Entity\OrderRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

final class FinishController implements MiddlewareInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(TemplateRendererInterface $renderer, OrderRepository $orderRepository)
    {
        $this->renderer = $renderer;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $orderId = $request->getAttribute('orderId');

        $order = $this->orderRepository->getById($orderId);

        $response->getBody()->write($this->renderer->render('finish.html.twig', [
            'domainName' => $order->getDomainName(),
            'wasPaid' => $order->wasPaid()
        ]));
    }
}
