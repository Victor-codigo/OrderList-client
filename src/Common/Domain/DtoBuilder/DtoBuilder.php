<?php

declare(strict_types=1);

namespace Common\Domain\DtoBuilder;

class DtoBuilder
{
    private array $builderMethods = [];

    /**
     * @param string[] $builderMethods
     */
    public function __construct(array $builderMethods)
    {
        $this->setBuilderMethods($builderMethods);
    }

    private function setBuilderMethods(array $builderMethods): void
    {
        $this->builderMethods = array_combine(
            $builderMethods,
            array_fill(0, count($builderMethods), false)
        );
    }

    public function setMethodStatus(string $methodName, bool $status): void
    {
        if (!array_key_exists($methodName, $this->builderMethods)) {
            throw new \LogicException("No such method exists [{$methodName}]");
        }

        $this->builderMethods[$methodName] = $status;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if (count(array_filter($this->builderMethods)) < count($this->builderMethods)) {
            $methodsMandatory = implode(', ', array_keys($this->builderMethods));
            throw new \InvalidArgumentException("Constructors: {$methodsMandatory}. Are mandatory");
        }
    }
}
