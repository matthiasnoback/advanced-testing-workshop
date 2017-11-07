<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Common\Persistence\Database;
use DomainShop\Entity\Order;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

final class RegisterController implements MiddlewareInterface
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
        $submittedData = $request->getParsedBody();

        if (isset($submittedData['name'], $submittedData['email_address'])) {
            // the form has been submitted
            // TODO store order
            $orderId = count(Database::retrieveAll(Order::class)) + 1;
            $order = new Order();
            $order->setId($orderId);
            $order->setDomainName($submittedData['domain_name']);
            $order->setOwnerName($submittedData['name']);
            $order->setOwnerEmailAddress($submittedData['email_address']);
            Database::persist($order);

            return new RedirectResponse(
                $this->router->generateUri(
                    'pay',
                    ['orderId' => 1]
                )
            );
        }

        $response->getBody()->write($this->renderer->render('register.html.twig', [
            'domainName' => $submittedData['domain_name']
        ]));

        return $response;
    }
}
