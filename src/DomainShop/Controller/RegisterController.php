<?php
declare(strict_types=1);

namespace DomainShop\Controller;

use Common\Persistence\Database;
use DomainShop\Application\ExchangeRateProvider;
use DomainShop\Domain\Clock;
use DomainShop\Entity\Order;
use DomainShop\Entity\Pricing;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Swap\Builder;
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
    /** @var ExchangeRateProvider */
    private $exchangeRateProvider;

    public function __construct(
        RouterInterface $router,
        TemplateRendererInterface $renderer,
        ExchangeRateProvider $exchangeRateProvider
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->exchangeRateProvider = $exchangeRateProvider;
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
                $orderId = count(Database::retrieveAll(Order::class)) + 1;

                $order = new Order();
                $order->setId($orderId);
                $order->setDomainName($submittedData['domain_name']);
                $order->setOwnerName($submittedData['name']);
                $order->setOwnerEmailAddress($submittedData['email_address']);
                $order->setPayInCurrency($submittedData['currency']);

                /** @var Pricing $pricing */
                $pricing = Database::retrieve(Pricing::class, $order->getDomainNameExtension());

                if ($order->getPayInCurrency() !== $pricing->getCurrency()) {
                    $rate = $this->exchangeRateProvider->getExchangeRate(
                        $pricing->getCurrency(),
                        $order->getPayInCurrency()
                    );
                    $amount = (int)round($pricing->getAmount() * $rate);
                } else {
                    $amount = $pricing->getAmount();
                }

                $order->setAmount($amount);

                Database::persist($order);

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
