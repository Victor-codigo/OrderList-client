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
        $builderMethodsExecuted = array_filter($this->builderMethods);

        if (count($builderMethodsExecuted) < count($this->builderMethods)) {
            $builderMethodsNotExecuted = array_diff_key($this->builderMethods, $builderMethodsExecuted);
            $methodsMandatory = implode(', ', array_keys($builderMethodsNotExecuted));

            throw new \InvalidArgumentException("Constructors: {$methodsMandatory}. Are mandatory");
        }
    }
}
