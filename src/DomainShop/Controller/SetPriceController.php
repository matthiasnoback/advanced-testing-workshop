<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Common\Persistence\Database;
use DomainShop\Entity\Pricing;
use Novutec\WhoisParser\Templates\Pr;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SetPriceController implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $submittedData = $request->getParsedBody();

        try {
            $pricing = Database::retrieve(Pricing::class, $submittedData['currency']);
        } catch (\RuntimeException $exception) {
            $pricing = new Pricing();
            $pricing->setExtension($submittedData['extension']);
        }

        $pricing->setCurrency($submittedData['currency']);
        $pricing->setAmount((int)$submittedData['amount']);

        Database::persist($pricing);

        return $response;
    }
}
