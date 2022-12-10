<?php

declare(strict_types=1);

namespace App\Twig\Components;

use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class TwigComponent
{
    abstract protected static function getComponentName(): string;

    protected TranslatorInterface $translator;
    protected RequestStack $request;
    public TwigComponentDtoInterface $data;

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->request = $request;
    }

    protected function translate(string $id, array $params = []): string
    {
        return $this->translator->trans($id, $params, $this->getComponentName());
    }

    /**
     * @throws LogicException
     */
    protected function loadFromSession(): void
    {
        $data = $this->request->getSession()->get($this->getComponentName());

        if ($data===null) {
            throw new LogicException('Could not find ['.$this->getComponentName().'] in session variables');
        }

        if (!$data instanceof TwigComponentDtoInterface) {
            throw new LogicException('Session var ['.$this->getComponentName().'], is not an instance of ['.TwigComponentDtoInterface::class.']');
        }

        $this->data = $data;
    }
}
