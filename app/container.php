<?php

use DomainShop\Application\ExchangeRateProvider;
use DomainShop\Application\PayForOrderService;
use DomainShop\Application\RegisterDomainName;
use DomainShop\Application\SetPriceService;
use DomainShop\Controller\CheckAvailabilityController;
use DomainShop\Controller\FinishController;
use DomainShop\Controller\HomepageController;
use DomainShop\Controller\PayController;
use DomainShop\Controller\RegisterController;
use DomainShop\Controller\SetPriceController;
use DomainShop\Domain\Clock;
use DomainShop\Domain\OrderRepository;
use DomainShop\Domain\PricingRepository;
use DomainShop\Infrastructure\FileSystemOrderRepository;
use DomainShop\Infrastructure\FileSystemPricingRepository;
use DomainShop\Infrastructure\FixedClock;
use DomainShop\Infrastructure\FixedExchangeRate;
use DomainShop\Infrastructure\InMemoryOrderRepository;
use DomainShop\Infrastructure\SwapFixer;
use DomainShop\Infrastructure\SystemClock;
use DomainShop\Resources\Views\TwigTemplates;
use Interop\Container\ContainerInterface;
use Symfony\Component\Debug\Debug;
use Xtreamwayz\Pimple\Container;
use Zend\Diactoros\Response;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Twig\TwigRendererFactory;
use Zend\Stratigility\Middleware\NotFoundHandler;

Debug::enable();

$container = new Container();

$applicationEnv = getenv('APPLICATION_ENV') ?: 'development';
$container['config'] = [
    'middleware_pipeline' => [
        'routing' => [
            'middleware' => array(
                ApplicationFactory::ROUTING_MIDDLEWARE,
                ApplicationFactory::DISPATCH_MIDDLEWARE,
            ),
            'priority' => 1,
        ],
        [
            'middleware' => NotFoundHandler::class,
            'priority' => -1,
        ],
    ],
    'debug' => $applicationEnv !== 'production',
    'final_handler' => [
        'options' => [
            'env' => $applicationEnv,
            'onerror' => function(\Throwable $throwable) {
                error_log((string)$throwable);
            }
        ]
    ],
    'templates' => [
        'extension' => 'html.twig',
        'paths' => [
            TwigTemplates::getPath()
        ]
    ],
    'twig' => [
        'globals' => [
            'applicationEnv' => $applicationEnv
        ]
    ],
    'routes' => [
        [
            'name' => 'homepage',
            'path' => '/',
            'middleware' => HomepageController::class,
            'allowed_methods' => ['GET']
        ],
        [
            'name' => 'check_availability',
            'path' => '/check-availability',
            'middleware' => CheckAvailabilityController::class,
            'allowed_methods' => ['POST']
        ],
        [
            'name' => 'register',
            'path' => '/register',
            'middleware' => RegisterController::class,
            'allowed_methods' => ['POST']
        ],
        [
            'name' => 'pay',
            'path' => '/pay/{orderId}',
            'middleware' => PayController::class,
            'allowed_methods' => ['GET', 'POST']
        ],
        [
            'name' => 'finish',
            'path' => '/finish/{orderId}',
            'middleware' => FinishController::class,
            'allowed_methods' => ['GET']
        ],
        [
            'name' => 'set_price',
            'path' => '/set-price',
            'middleware' => SetPriceController::class,
            'allowed_methods' => ['POST']
        ],
    ]
];

/*
 * Zend Expressive Application
 */
$container[RouterInterface::class] = function () {
    return new FastRouteRouter();
};
$container[Application::class] = new ApplicationFactory();
$container[NotFoundHandler::class] = function() {
    return new NotFoundHandler(new Response());
};

/*
 * Templating
 */
$container[TemplateRendererInterface::class] = new TwigRendererFactory();
$container[ServerUrlHelper::class] = function () {
    return new ServerUrlHelper();
};
$container[UrlHelper::class] = function (ContainerInterface $container) {
    return new UrlHelper($container[RouterInterface::class]);
};

/*
 * Controllers
 */
$container[HomepageController::class] = function (ContainerInterface $container) {
    return new HomepageController($container->get(TemplateRendererInterface::class));
};
$container[CheckAvailabilityController::class] = function (ContainerInterface $container) {
    return new CheckAvailabilityController($container->get(TemplateRendererInterface::class));
};
$container[RegisterController::class] = function (ContainerInterface $container) {
    return new RegisterController(
        $container->get(RouterInterface::class),
        $container->get(TemplateRendererInterface::class),
        $container->get(RegisterDomainName::class)
    );
};
$container[PayController::class] = function (ContainerInterface $container) {
    return new PayController(
        $container->get(RouterInterface::class),
        $container->get(TemplateRendererInterface::class),
        $container->get(PayForOrderService::class)
    );
};
$container[FinishController::class] = function (ContainerInterface $container) {
    return new FinishController(
        $container->get(TemplateRendererInterface::class)
    );
};
$container[SetPriceController::class] = function (ContainerInterface $container) {
    return new SetPriceController($container->get(SetPriceService::class));
};

$container[Clock::class] = function () use ($applicationEnv) {
    if ('testing' === $applicationEnv || 'system' === $applicationEnv) {
        return new FixedClock();
    }

    return new SystemClock();
};

$container[ExchangeRateProvider::class] = function (ContainerInterface $container) use ($applicationEnv) {
    if ('testing' === $applicationEnv) {
        return new FixedExchangeRate();
    }
    return new SwapFixer($container->get(Clock::class));
};

$container[RegisterDomainName::class] = function (ContainerInterface $container) {
    return new RegisterDomainName(
        $container->get(ExchangeRateProvider::class),
        $container->get(OrderRepository::class),
        $container->get(PricingRepository::class)
    );
};

$container[PricingRepository::class] = function () {
    return new FileSystemPricingRepository();
};

$container[PayForOrderService::class] = function () {
    return new PayForOrderService();
};

$container[SetPriceService::class] = function (ContainerInterface $container) {
    return new SetPriceService(
        $container->get(PricingRepository::class)
    );
};

$container[OrderRepository::class] = function () use ($applicationEnv) {
    if ('testing' === $applicationEnv) {
        return new InMemoryOrderRepository();
    }
    return new FileSystemOrderRepository();
};

return $container;
