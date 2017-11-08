<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use DomainShop\Service\DomainAvailability;
use DomainShop\Service\DomainAvailabilityService;
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

    /**
     * @var DomainAvailabilityService
     */
    private $domainAvailabilityService;

    public function __construct(TemplateRendererInterface $renderer, DomainAvailabilityService $domainAvailabilityService)
    {
        $this->renderer = $renderer;
        $this->domainAvailabilityService = $domainAvailabilityService;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $submittedData = $request->getParsedBody();

        if (!isset($submittedData['domain_name'])) {
            throw new \RuntimeException('No domain name provided');
        }

        $domainName = $submittedData['domain_name'];

        $domainAvailability = $this->domainAvailabilityService->lookup($domainName);

        $response->getBody()->write($this->renderer->render('availability.html.twig', [
            'isAvailable' => $domainAvailability->isAvailable(),
            'domainName' => $domainName,
            'whoisInformation' => $domainAvailability->whoisInformation()
        ]));

        return $response;
    }
}
