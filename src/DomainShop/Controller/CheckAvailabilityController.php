<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

final class CheckAvailabilityController implements MiddlewareInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $formData = $request->getParsedBody();
        $domainName = $formData['domain_name'];

        $response->getBody()->write($this->renderer->render('availability.html.twig', [
            'isAvailable' => true,
            'domainName' => $domainName
        ]));

        return $response;
    }
}
