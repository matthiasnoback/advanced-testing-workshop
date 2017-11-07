<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Novutec\WhoisParser\Parser;
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
        $submittedData = $request->getParsedBody();

        if (!isset($submittedData['domain_name'])) {
            throw new \RuntimeException('No domain name provided');
        }
        if (!preg_match('/^\w+\.\w+$/', $submittedData['domain_name'])) {
            throw new \RuntimeException('Invalid domain name provided');
        }

        $domainName = $submittedData['domain_name'];

        $parser = new Parser();
        $parser->throwExceptions(true);
        $result = $parser->lookup($domainName);

        $isAvailable = !$result->registered;

        $response->getBody()->write($this->renderer->render('availability.html.twig', [
            'isAvailable' => $isAvailable,
            'domainName' => $domainName,
            'whoisResult' => $result
        ]));

        return $response;
    }
}
