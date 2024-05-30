<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use App\Kernel;
use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class OnKernelExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['__invoke']];
    }

    public function __construct(
        private Environment $twig,
        private RequestStack $request,
        private Kernel $kernel
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if ($this->kernel->isDebug()) {
            // return;
        }

        $exception = $event->getThrowable();

        $response = $this->error401($exception);
        $response ??= $this->error403($exception);
        $response ??= $this->error404($exception);
        $response ??= $this->error500();

        $event->setResponse($response);
    }

    private function error401(\Throwable $exception): ?Response
    {
        if (!$exception instanceof AccessDeniedException) {
            return null;
        }

        $request = $this->request->getMainRequest();
        $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
        $locale = $this->getLocale($request->getPathInfo());

        return new Response(
            $this->twig->render('page_errors/error401.html.twig', [
                '_locale' => $locale,
                'domainName' => Config::CLIENT_DOMAIN_NAME,
                'error' => null === $tokenSession ? 'forbidden' : 'session.expired',
            ])
        );
    }

    private function error403(\Throwable $exception): ?Response
    {
        if (!$exception instanceof RequestUnauthorizedException) {
            return null;
        }

        $request = $this->request->getMainRequest();
        $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
        $locale = $this->getLocale($request->getPathInfo());

        return new Response(
            $this->twig->render('page_errors/error403.html.twig', [
                '_locale' => $locale,
                'domainName' => Config::CLIENT_DOMAIN_NAME,
                'error' => null === $tokenSession ? 'forbidden' : 'session.expired',
            ])
        );
    }

    private function error404(\Throwable $exception): ?Response
    {
        if (!$exception instanceof NotFoundHttpException) {
            return null;
        }

        $locale = $this->getLocale($this->request->getMainRequest()->getPathInfo());

        return new Response(
            $this->twig->render('page_errors/error404.html.twig', [
                '_locale' => $locale,
                'domainName' => Config::CLIENT_DOMAIN_NAME,
            ])
        );
    }

    private function error500(): ?Response
    {
        $locale = $this->getLocale($this->request->getMainRequest()->getPathInfo());

        return new Response(
            $this->twig->render('page_errors/error.html.twig', [
                '_locale' => $locale,
                'domainName' => Config::CLIENT_DOMAIN_NAME,
            ])
        );
    }

    private function getLocale(string $url): string
    {
        $urlArray = explode('/', $url);

        if (!isset($urlArray[1])) {
            return 'en';
        }

        return $urlArray[1];
    }
}
