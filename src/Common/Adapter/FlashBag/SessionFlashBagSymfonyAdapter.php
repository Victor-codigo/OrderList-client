<?php

declare(strict_types=1);

namespace Common\Adapter\FlashBag;

use Common\Domain\Ports\FlashBag\FlashBagInterface as DomainFlashBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionFlashBagSymfonyAdapter implements FlashBagInterface, DomainFlashBagInterface
{
    private FlashBagInterface $sessionFlashBag;

    /**
     * @throws LogicException
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->sessionFlashBag = $this->getSessionFlashBag($requestStack->getCurrentRequest());
    }

    /**
     * @throws LogicException
     */
    private function getSessionFlashBag(Request $request): FlashBagInterface
    {
        $session = $request->getSession();

        if (!$session instanceof Session) {
            throw new \LogicException('Session is not an instance of [Session]. Can not get FlashBag');
        }

        return $session->getFlashBag();
    }

    /**
     * Adds a flash message for the given type.
     */
    public function add(string $type, mixed $message): void
    {
        $this->sessionFlashBag->add($type, $message);
    }

    /**
     * Registers one or more messages for a given type.
     */
    public function set(string $type, string|array $messages): void
    {
        $this->sessionFlashBag->set($type, $messages);
    }

    /**
     * Gets and clears flash from the stack.
     *
     * @param array $default Default value if $type does not exist
     */
    public function get(string $type, array $default = []): array
    {
        return $this->sessionFlashBag->get($type, $default);
    }

    /**
     * Gets flash messages for a given type.
     *
     * @param string $type    Message category type
     * @param array  $default Default value if $type does not exist
     */
    public function peek(string $type, array $default = []): array
    {
        return $this->sessionFlashBag->peek($type, $default);
    }

    /**
     * Gets all flash messages.
     */
    public function peekAll(): array
    {
        return $this->peekAll();
    }

    /**
     * Gets and clears flashes from the stack.
     */
    public function all(): array
    {
        return $this->all();
    }

    /**
     * Sets all flash messages.
     */
    public function setAll(array $messages): void
    {
        $this->sessionFlashBag->setAll($messages);
    }

    /**
     * Has flash messages for a given type?
     */
    public function has(string $type): bool
    {
        return $this->sessionFlashBag->has($type);
    }

    /**
     * Returns a list of all defined types.
     */
    public function keys(): array
    {
        return $this->sessionFlashBag->keys();
    }

    /**
     * Gets this bag's name.
     */
    public function getName(): string
    {
        return $this->sessionFlashBag->getName();
    }

    /**
     * Initializes the Bag.
     */
    public function initialize(array &$array): void
    {
        $this->sessionFlashBag->initialize($array);
    }

    /**
     * Gets the storage key for this bag.
     */
    public function getStorageKey(): string
    {
        return $this->sessionFlashBag->getStorageKey();
    }

    /**
     * Clears out data from bag.
     *
     * @return mixed Whatever data was contained
     */
    public function clear(): mixed
    {
        return $this->sessionFlashBag->clear();
    }
}
