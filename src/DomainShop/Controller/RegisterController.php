<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use DomainShop\Service\RegisterDomainName;
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

    /**
     * @var RegisterDomainName
     */
    private $service;

    public function __construct(RouterInterface $router, TemplateRendererInterface $renderer, RegisterDomainName $service)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $submittedData = $request->getParsedBody();
        $formErrors = [];

        if (!isset($submittedData['domain_name'])) {
            throw new \RuntimeException('No domain name provided');
        }
        if (!preg_match('/^\w+\.\w+$/', $submittedData['domain_name'])) {
            throw new \RuntimeException('Invalid domain name provided');
        }

        if (isset($submittedData['register'])) {
            if (empty($submittedData['name'])) {
                $formErrors['name'][] = 'Please fill in your name';
            }
            if (!filter_var($submittedData['email_address'], FILTER_VALIDATE_EMAIL)) {
                $formErrors['email_address'][] = 'Please fill in a valid email address';
            }

            if (empty($formErrors)) {
                $order = $this->service->handle(
                    $submittedData['domain_name'],
                    $submittedData['name'],
                    $submittedData['email_address'],
                    $submittedData['currency']
                );

                return new RedirectResponse(
                    $this->router->generateUri(
                        'pay',
                        ['orderId' => $order->id()]
                    )
                );
            }
        }

        $response->getBody()->write($this->renderer->render('register.html.twig', [
            'domainName' => $submittedData['domain_name'],
            'formErrors' => $formErrors,
            'submittedData' => $submittedData
        ]));

        return $response;
    }
}
