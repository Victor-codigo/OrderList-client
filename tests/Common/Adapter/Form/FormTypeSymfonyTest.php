<?php

declare(strict_types=1);

namespace App\Tests\Common\Adapter\Form;

use App\Tests\Common\Adapter\Form\Fixtures\FormForTesting;
use Common\Adapter\Form\FormTypeSymfony;
use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormField;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FormTypeSymfonyTest extends TestCase
{
    private const OPTION_FORM_TYPE = 'formType';

    private FormTypeSymfony $object;
    private MockObject|FormBuilderInterface $formBuilder;
    private MockObject|FormForTesting $formType;
    private array $options;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new FormTypeSymfony();
        $this->formBuilder = $this->createMock(FormBuilderInterface::class);
        $this->formType = $this->createMock(FormForTesting::class);
        $this->options['data'] = [self::OPTION_FORM_TYPE => $this->formType];
    }

    /** @test */
    public function itShouldBuildTheForm(): void
    {
        $formFields = $this->createFormFields();

        $this->formType
            ->expects($this->once())
            ->method('formBuild');

        $this->formType
            ->expects($this->once())
            ->method('getFields')
            ->willReturn(array_map(fn (array $field) => $field['field'], $formFields));

        $this->formBuilder
            ->expects($this->exactly(count($formFields)))
            ->method('add')
            ->withConsecutive(
                [$formFields[0]['field']->name, $formFields[0]['symfonyType']],
                [$formFields[1]['field']->name, $formFields[1]['symfonyType']],
                [$formFields[2]['field']->name, $formFields[2]['symfonyType']],
                [$formFields[3]['field']->name, $formFields[3]['symfonyType']],
            )
            ->willReturnCallback(function (string $name, string $type, array $options) {
                $this->assertFieldOptionsAreOk($type, $options);

                return $this->formBuilder;
            });

        $this->object->buildForm($this->formBuilder, $this->options);
    }

    private function assertFieldOptionsAreOk(string $type, array $options): void
    {
        $expectedOptions = match ($type) {
            DateType::class => [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ],
            DateTimeType::class => [
                'widget' => 'single_text',
            ],
            default => []
        };

        $this->assertEquals($expectedOptions, $options);
    }

    /**
     * @return FormField[]
     */
    private function createFormFields(): array
    {
        return [
            ['field' => new FormField('name', FIELD_TYPE::TEXT, null), 'symfonyType' => TextType::class],
            ['field' => new FormField('age', FIELD_TYPE::INTEGER, null), 'symfonyType' => IntegerType::class],
            ['field' => new FormField('country', FIELD_TYPE::COUNTRY, null), 'symfonyType' => CountryType::class],
            ['field' => new FormField('date', FIELD_TYPE::DATE, null), 'symfonyType' => DateType::class],
            ['field' => new FormField('date', FIELD_TYPE::DATETIME, null), 'symfonyType' => DateTimeType::class],
        ];
    }
}
