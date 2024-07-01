<?php

declare(strict_types=1);

namespace Common\Adapter\Form;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormTypeInterface;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\UlidType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\Extension\Core\Type\WeekType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;

class FormTypeSymfony extends AbstractType
{
    public const OPTION_FORM_TYPE = 'formType';

    protected FormTypeInterface $formType;
    protected array $validationErrors = [];

    public function getFormType(): FormTypeInterface
    {
        return $this->formType;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function getBlockPrefix(): string
    {
        if (!isset($this->formType)) {
            return parent::getBlockPrefix();
        }

        return $this->formType::getName();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->formType = $options['data'][static::OPTION_FORM_TYPE];
        $this->formType->formBuild();

        foreach ($this->formType->getFields() as $field) {
            $builder->add(
                $field->name,
                $this->toSymfonyFormField($field->type),
                $this->getFormFieldOptions($field->type, $field->options)
            );
        }
    }

    private function toSymfonyFormField(FIELD_TYPE $type): string
    {
        return match ($type) {
            FIELD_TYPE::BIRTH_DAY => BirthdayType::class,
            FIELD_TYPE::BUTTON => ButtonType::class,
            FIELD_TYPE::CHECKBOX => CheckboxType::class,
            FIELD_TYPE::CHOICE => ChoiceType::class,
            FIELD_TYPE::COLLECTION => CollectionType::class,
            FIELD_TYPE::COLOR => ColorType::class,
            FIELD_TYPE::COUNTRY => CountryType::class,
            FIELD_TYPE::CURRENCY => CurrencyType::class,
            FIELD_TYPE::DATEINTERVAL => DateIntervalType::class,
            FIELD_TYPE::DATETIME => DateTimeType::class,
            FIELD_TYPE::DATE => DateType::class,
            FIELD_TYPE::EMAIL => EmailType::class,
            FIELD_TYPE::ENUM => EnumType::class,
            FIELD_TYPE::FILE => FileType::class,
            FIELD_TYPE::FORM => FormType::class,
            FIELD_TYPE::HIDDEN => HiddenType::class,
            FIELD_TYPE::INTEGER => IntegerType::class,
            FIELD_TYPE::LANGUAGE => LanguageType::class,
            FIELD_TYPE::LOCALE => LocaleType::class,
            FIELD_TYPE::MONEY => MoneyType::class,
            FIELD_TYPE::NUMBER => NumberType::class,
            FIELD_TYPE::PASSWORD => PasswordType::class,
            FIELD_TYPE::PERCENT => PercentType::class,
            FIELD_TYPE::RADIO => RadioType::class,
            FIELD_TYPE::RANGE => RangeType::class,
            FIELD_TYPE::REPEATED => RepeatedType::class,
            FIELD_TYPE::RESET => ResetType::class,
            FIELD_TYPE::SEARCH => SearchType::class,
            FIELD_TYPE::SUBMIT => SubmitType::class,
            FIELD_TYPE::TEL => TelType::class,
            FIELD_TYPE::TEXTAREA => TextareaType::class,
            FIELD_TYPE::TEXT => TextType::class,
            FIELD_TYPE::TIME => TimeType::class,
            FIELD_TYPE::TIMEZONE => TimezoneType::class,
            FIELD_TYPE::ULID => UlidType::class,
            FIELD_TYPE::URL => UrlType::class,
            FIELD_TYPE::UUID => UuidType::class,
            FIELD_TYPE::WEEK => WeekType::class,
            FIELD_TYPE::DROPDOWN => DropzoneType::class,
            FIELD_TYPE::CAPTCHA => Recaptcha3Type::class
        };
    }

    private function getFormFieldOptions(FIELD_TYPE $type, array $options): array
    {
        return match ($type) {
            FIELD_TYPE::DATE => [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ],
            FIELD_TYPE::DATETIME => [
                'widget' => 'single_text',
            ],
            FIELD_TYPE::CHOICE => [
                'choices' => $options,
            ],
            FIELD_TYPE::COLLECTION => [
                'allow_add' => true,
            ],
            FIELD_TYPE::CAPTCHA => [
                'constraints' => new Recaptcha3(),
            ],
            default => []
        };
    }
}
