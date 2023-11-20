<?php

declare(strict_types=1);

namespace Common\Domain\Ports\FlashBag;

interface FlashBagInterface
{
    /**
     * Adds a flash message for the given type.
     */
    public function add(string $type, mixed $message): void;

    /**
     * Registers one or more messages for a given type.
     */
    public function set(string $type, string|array $messages): void;

    /**
     * Gets and clears flash from the stack.
     *
     * @param array $default Default value if $type does not exist
     */
    public function get(string $type, array $default = []): array;

    /**
     * Gets flash messages for a given type.
     *
     * @param string $type    Message category type
     * @param array  $default Default value if $type does not exist
     */
    public function peek(string $type, array $default = []): array;

    /**
     * Gets all flash messages.
     */
    public function peekAll(): array;

    /**
     * Gets and clears flashes from the stack.
     */
    public function all(): array;

    /**
     * Sets all flash messages.
     */
    public function setAll(array $messages): void;

    /**
     * Has flash messages for a given type?
     */
    public function has(string $type): bool;

    /**
     * Returns a list of all defined types.
     */
    public function keys(): array;

    /**
     * Gets this bag's name.
     */
    public function getName(): string;

    /**
     * Initializes the Bag.
     */
    public function initialize(array &$array): void;

    /**
     * Gets the storage key for this bag.
     */
    public function getStorageKey(): string;

    /**
     * Clears out data from bag.
     *
     * @return mixed Whatever data was contained
     */
    public function clear(): mixed;
}
