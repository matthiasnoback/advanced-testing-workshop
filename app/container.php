<?php

use DomainShop\Controller\CheckAvailabilityController;
use DomainShop\Controller\FinishController;
use DomainShop\Controller\HomepageController;
use DomainShop\Controller\PayController;
use DomainShop\Controller\RegisterController;
use DomainShop\Controller\SetPriceController;
use DomainShop\Resources\Views\TwigTemplates;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Xtreamwayz\Pimple\Container;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Twig\TwigRendererFactory;

Debug::enable();
ErrorHandler::register();

$container = new Container();

$container['config'] = [
    'debug' => true,
    'templates' => [
        'extension' => 'html.twig',
        'paths' => [
            TwigTemplates::getPath()
        ]
    ],
    'twig' => [
        'extensions' => [

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
$container['Zend\Expressive\FinalHandler'] = function () {
    return function (RequestInterface $request, ResponseInterface $response, $err = null) {
        if ($err instanceof \Throwable) {
            throw $err;
        }
    };
};
$container[RouterInterface::class] = function () {
    return new FastRouteRouter();
};
$container[Application::class] = new ApplicationFactory();

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

/**
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
        $container->get(TemplateRendererInterface::class)
    );
};
$container[PayController::class] = function (ContainerInterface $container) {
    return new PayController(
        $container->get(RouterInterface::class),
        $container->get(TemplateRendererInterface::class)
    );
};
$container[FinishController::class] = function (ContainerInterface $container) {
    return new FinishController(
        $container->get(RouterInterface::class),
        $container->get(TemplateRendererInterface::class)
    );
};
$container[SetPriceController::class] = function (ContainerInterface $container) {
    return new SetPriceController();
};

return $container;
