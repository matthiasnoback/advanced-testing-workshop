<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use DomainShop\Entity\Pricing;
use DomainShop\Entity\PricingRepository;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SetPriceController implements MiddlewareInterface
{
    /**
     * @var PricingRepository
     */
    private $pricingRepository;

    public function __construct(PricingRepository $pricingRepository)
    {
        $this->pricingRepository = $pricingRepository;
    }

    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $submittedData = $request->getParsedBody();

        try {
            $pricing = $this->pricingRepository->getById($submittedData['extension']);
        } catch (\RuntimeException $exception) {
            $pricing = new Pricing();
            $pricing->setExtension($submittedData['extension']);
        }

        $pricing->setCurrency($submittedData['currency']);
        $pricing->setAmount((int)$submittedData['amount']);

        $this->pricingRepository->save($pricing);

        return $response;
    }
}
