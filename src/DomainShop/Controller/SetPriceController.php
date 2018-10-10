<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use DomainShop\Application\SetPriceService;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SetPriceController implements MiddlewareInterface
{
    /** @var SetPriceService */
    private $setPriceService;

    public function __construct(SetPriceService $setPriceService)
    {
        $this->setPriceService = $setPriceService;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $submittedData = $request->getParsedBody();
        ($this->setPriceService)($submittedData['extension'], $submittedData['currency'], (int) $submittedData['amount']);

        return $response;
    }
}
