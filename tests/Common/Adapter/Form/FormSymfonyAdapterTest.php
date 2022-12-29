<?php

declare(strict_types=1);

namespace App\Tests\Common\Adapter\Form;

use App\Tests\Common\Adapter\Form\Fixtures\FORM_ERRORS;
use App\Tests\Common\Adapter\Form\Fixtures\FormForTesting;
use Common\Adapter\Form\FormSymfonyAdapter;
use Common\Adapter\Form\FormTypeSymfony;
use Common\Domain\Form\FormTypeInterface;
use Common\Domain\Validation\ValidationInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface as SymfonyFormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class FormSymfonyAdapterTest extends TestCase
{
    private FormSymfonyAdapter $object;
    private MockObject|CsrfTokenManager $csrfManager;
    private MockObject|FormInterface $form;
    private MockObject|FormConfigInterface $formConfig;
    private MockObject|ResolvedFormTypeInterface $resolvedForm;
    private MockObject|FormTypeInterface $formType;
    private MockObject|SymfonyFormTypeInterface  $formTypeSymfony;
    private MockObject|ValidationInterface $validator;
    private string $tokenId = 'tokenId';

    protected function setUp(): void
    {
        parent::setUp();

        $this->csrfManager = $this->createMock(CsrfTokenManager::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->formConfig = $this->createMock(FormConfigInterface::class);
        $this->resolvedForm = $this->createMock(ResolvedFormTypeInterface::class);
        $this->formTypeSymfony = $this
            ->getMockBuilder(SymfonyFormTypeInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getFormType'])
            ->getMockForAbstractClass();
        $this->validator = $this->createMock(ValidationInterface::class);
        $this->formType = $this->createMock(FormTypeInterface::class);
        $this->object = new FormSymfonyAdapter($this->form, $this->csrfManager, $this->validator, $this->tokenId);
    }

    /** @test */
    public function ItSouldFailNotReturnCsrfTokenValueCsrfManagerNotInitialized(): void
    {
        $tokenValue = 'token value';
        $this->csrfManager
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(new CsrfToken('token', $tokenValue));

        $return = $this->object->getCsrfToken();

        $this->assertSame($tokenValue, $return);
    }

    /** @test */
    public function itShouldReturnTheCsrfTokenValue(): void
    {
        $tokenValue = 'token value';

        $this->csrfManager
            ->expects($this->once())
            ->method('refreshToken')
            ->willReturn(new CsrfToken('token', $tokenValue));

        $this->object->csrfTokenRefresh();
        $return = $this->object->getCsrfToken();

        $this->assertSame($tokenValue, $return);
    }

    /** @test */
    public function itShouldBeSubmittedTheForm(): void
    {
        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $return = $this->object->isSubmitted();

        $this->assertTrue($return);
    }

    /** @test */
    public function itShouldNotBeSubmittedTheForm(): void
    {
        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false);

        $return = $this->object->isSubmitted();

        $this->assertFalse($return);
    }

    private function createStubsForMethodIsValid(bool $formSymfonyValid, array $formTypeErrors): void
    {
        $this->createStubsForGetFormType();

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        $this->formType
            ->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf(ValidationInterface::class), [])
            ->willReturn($formTypeErrors);
    }

    private function createStubsForGetFormType(): void
    {
        $this->form
            ->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->formConfig);

        $this->formConfig
            ->expects($this->any())
            ->method('getType')
            ->willReturn($this->resolvedForm);

        $this->resolvedForm
            ->expects($this->any())
            ->method('getInnerType')
            ->willReturn($this->formTypeSymfony);

        $this->formTypeSymfony
            ->expects($this->any())
            ->method('getFormType')
            ->willReturn($this->formType);
    }



    /** @test */
    public function itShouldBeAValidFormNotCsrfProtectetionChecked(): void
    {
        $this->createStubsForMethodIsValid(true, []);

        $return = $this->object->isValid(false);

        $this->assertTrue($return);
    }

    /** @test */
    public function itShouldBeAValidFormCsrfProtectetionChecked(): void
    {
        /** @var MockObject|FormSymfonyAdapter $object */
        $object = $this
            ->getMockBuilder(FormSymfonyAdapter::class)
            ->setConstructorArgs([$this->form, $this->csrfManager, $this->validator, $this->tokenId])
            ->onlyMethods(['isCsrfValid'])
            ->getMock();

        $object
            ->expects($this->once())
            ->method('isCsrfValid')
            ->willReturn(true);

        $this->createStubsForMethodIsValid(true, []);

        $return = $object->isValid(true);

        $this->assertTrue($return);
    }

    /** @test */
    public function itShouldNotBeAValidFormBecauseOfFormValidation(): void
    {
        $this->createStubsForMethodIsValid(true, ['email' => [1,2]]);

        $return = $this->object->isValid(false);

        $this->assertFalse($return);
    }

    /** @test */
    public function itShouldNotBeAValidFormBecauseOfSymfonyFormAndFormValidation(): void
    {
        $this->createStubsForMethodIsValid(false, ['email' => [1, 2]]);

        $return = $this->object->isValid(false);

        $this->assertFalse($return);
    }

    /** @test */
    public function itShouldRefreshTheCsrfToken(): void
    {
        $tokenRefreshed = 'token refreshed';

        $this->csrfManager
            ->expects($this->once())
            ->method('refreshToken')
            ->with($this->tokenId)
            ->willReturn(new CsrfToken($this->tokenId, $tokenRefreshed));

        $return = $this->object->csrfTokenRefresh();

        $this->assertInstanceOf(FormSymfonyAdapter::class, $return);
        $this->assertNotSame('', $tokenRefreshed);
    }

    /** @test */
    public function itShouldBeAValidCsrfToken(): void
    {
        $this->formType = new FormForTesting();
        $tokenFieldName = $this->formType::getCsrfTokenFieldName();
        $dataReturn = [
            $tokenFieldName => 'this is a token'
        ];

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($dataReturn);

        $this->createStubsForGetFormType();

        $this->csrfManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($this->equalTo(new CsrfToken($this->tokenId, $dataReturn[$tokenFieldName])))
            ->willReturn(true);

        $return = $this->object->isCsrfValid();

        $this->assertTrue($return);
    }

    /** @test */
    public function itShouldBeAValidCsrfTokenInitiallyTokenValueIsNotSet(): void
    {
        $this->formType = new FormForTesting();
        $tokenFieldName = $this->formType::getCsrfTokenFieldName();
        $dataReturn = [
            $tokenFieldName => 'this is a token'
        ];

        $this->createStubsForGetFormType();

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($dataReturn);

        $this->csrfManager
            ->expects($this->once())
            ->method('getToken')
            ->with($this->tokenId)
            ->willReturn(new CsrfToken($this->tokenId, $dataReturn[$tokenFieldName]));

        $this->csrfManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($this->equalTo(new CsrfToken($this->tokenId, $dataReturn[$tokenFieldName])))
            ->willReturn(true);


        $return = $this->object->isCsrfValid();

        $this->assertTrue($return);
    }


    /** @test */
    public function itShouldNotBeAValidCsrfTokenItDoesNotExistInFormData(): void
    {
        $this->formType = new FormForTesting();

        $this->createStubsForGetFormType();

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn([]);

        $return = $this->object->isCsrfValid();

        $this->assertFalse($return);
    }

    /** @test */
    public function itShouldNotBeAValidCsrfTokenInitiallyTokenIdDoesNotExist(): void
    {
        $this->formType = new FormForTesting();
        $tokenFieldName = $this->formType::getCsrfTokenFieldName();
        $dataReturn = [
            $tokenFieldName => 'this is a token'
        ];

        $this->object = new FormSymfonyAdapter($this->form, $this->csrfManager, $this->validator, null);

        $this->createStubsForGetFormType();

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($dataReturn);

        $return = $this->object->isCsrfValid();

        $this->assertFalse($return);
    }

    /** @test */
    public function itShouldNotBeAValidCsrfToken(): void
    {
        $this->formType = new FormForTesting();
        $tokenFieldName = $this->formType::getCsrfTokenFieldName();
        $dataReturn = [
            $tokenFieldName => 'this is a token'
        ];

        $this->createStubsForGetFormType();

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($dataReturn);

        $this->csrfManager
            ->expects($this->once())
            ->method('getToken')
            ->with($this->tokenId)
            ->willReturn(new CsrfToken($this->tokenId, $dataReturn[$tokenFieldName]));

        $this->csrfManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($this->equalTo(new CsrfToken($this->tokenId, $dataReturn[$tokenFieldName])))
            ->willReturn(false);

        $return = $this->object->isCsrfValid();

        $this->assertFalse($return);
    }

    /** @test */
    public function itShouldReturnFormDataWhithoutTheFormType(): void
    {
        $data = [
            FormTypeSymfony::OPTION_FORM_TYPE => 'this should be removed',
            'formField1' => 'value1',
            'formField2' => 'value2',
            'formField3' => 'value3',
        ];

        $dataExpected = [
            'formField1' => 'value1',
            'formField2' => 'value2',
            'formField3' => 'value3',
        ];

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->createStubsForGetFormType();

        $return = $this->object->getData();

        $this->assertSame($dataExpected, $return);
    }

    /** @test */
    public function itShouldReturnFormFieldData(): void
    {
        $data = [
            FormTypeSymfony::OPTION_FORM_TYPE => 'this should be removed',
            'formField1' => 'value1',
            'formField2' => 'value2',
            'formField3' => 'value3',
        ];

        $dataExpected = [
            'formField1' => 'value1',
            'formField2' => 'value2',
            'formField3' => 'value3',
        ];

        $this->form
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data['formField2']);

        $this->createStubsForGetFormType();

        $return = $this->object->getFieldData('formField2');

        $this->assertSame($dataExpected['formField2'], $return);
    }

    /** @test */
    public function itShouldFailReturningFormFieldDataFieldDoesNotExists(): void
    {
        $data = [
            FormTypeSymfony::OPTION_FORM_TYPE => 'this should be removed',
            'formField1' => 'value1',
            'formField2' => 'value2',
            'formField3' => 'value3',
        ];

        $dataExpected = [
            'formField1' => 'value1',
            'formField2' => 'value2',
            'formField3' => 'value3',
        ];

        $this->form
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new InvalidArgumentException());

        $this->createStubsForGetFormType();
        $this->expectException(InvalidArgumentException::class);

        $this->object->getFieldData('formField2');
    }

    /** @test */
    public function itShouldAddAnErrorToTheForm()
    {
        $errorValue = 'error value';
        $return = $this->object->addError(FORM_ERRORS::FORM_ERROR_1->value, $errorValue);

        $this->assertSame($this->object, $return);
        $this->assertArrayHasKey(FORM_ERRORS::FORM_ERROR_1->value, $this->object->getErrors());
        $this->assertContains($errorValue, $this->object->getErrors());
    }

    /** @test */
    public function itShouldReturnAnEmptyArrayNoErrorsFound()
    {
        $return = $this->object->getErrors();

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldReturnAnErrorOfTheForm()
    {
        $errorValue1 = 'error value 1';
        $errorValue2 = 'error value 2';
        $this->object->addError(FORM_ERRORS::FORM_ERROR_1->value, $errorValue1);
        $this->object->addError(FORM_ERRORS::FORM_ERROR_2->value, $errorValue2);
        $return = $this->object->getErrors();

        $this->assertArrayHasKey(FORM_ERRORS::FORM_ERROR_1->value, $return);
        $this->assertArrayHasKey(FORM_ERRORS::FORM_ERROR_2->value, $return);
        $this->assertContains($errorValue1, $this->object->getErrors());
        $this->assertContains($errorValue2, $this->object->getErrors());
    }
}
