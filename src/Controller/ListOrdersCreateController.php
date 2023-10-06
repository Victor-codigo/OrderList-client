<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ListOrders\ListOrdersCreate\LIST_ORDERS_CREATE_FORM_ERRORS;
use App\Form\ListOrders\ListOrdersCreate\LIST_ORDERS_CREATE_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersCreate\ListOrdersCreateForm;
use App\Twig\Components\ListOrders\ListOrdersCreate\ListOrdersCreateComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/list-orders/create',
    name: 'listo_orders',
    methods: ['POST', 'GET'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ListOrdersCreateController extends AbstractController
{
    private const LIST_ORDERS_CREATE_ENDPOINT = '/api/v1/list-orders';
    private const USER_GROUPS_ENDPOINT = '/api/v1/groups/user-groups';

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
        $userGroups = $this->getUserGroups($tokenSession);
        $form = $this->formFactory->create(new ListOrdersCreateForm($userGroups), $request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formValid($form, $tokenSession, $userGroups);
        }

        return $this->formNotValid($form, $userGroups);
    }

    private function requestListOrdersCreate(array $formData, string $tokenSession): HttpClientResponseInterface
    {
        $dateToBuy = $formData[LIST_ORDERS_CREATE_FORM_FIELDS::DATE_TO_BUY]?->format('Y-m-d H:i:s');

        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::LIST_ORDERS_CREATE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::form([
                    'group_id' => $formData[LIST_ORDERS_CREATE_FORM_FIELDS::USER_GROUP],
                    'name' => $formData[LIST_ORDERS_CREATE_FORM_FIELDS::NAME],
                    'description' => $formData[LIST_ORDERS_CREATE_FORM_FIELDS::DESCRIPTION],
                    'date_to_buy' => $dateToBuy,
                ],
                [],
                $tokenSession
            )
        );
    }

    private function requestUserGroups(string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::USER_GROUPS_ENDPOINT.'?page=1&page_items=100',
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    /**
     * @return array key = group name.
     *               value = group id
     */
    private function getUserGroups(string $tokenSession): array
    {
        try {
            $response = $this->requestUserGroups($tokenSession);
            $userGroups = $response->toArray();
            $userGroupsData = array_column($userGroups['data']['groups'], 'group_id', 'name');
        } catch (Error400Exception|Error500Exception|NetworkException) {
            $userGroupsData = [];
        } finally {
            return $userGroupsData;
        }
    }

    private function formValid(FormInterface $form, string $tokenSession, array $userGroups): Response
    {
        try {
            $formData = $form->getData();
            $response = $this->requestListOrdersCreate($formData, $tokenSession);
            $responseData = $response->toArray();
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorDescription) {
                $form->addError($error, $errorDescription);
            }
        } catch (Error500Exception|NetworkException) {
            $form->addError(LIST_ORDERS_CREATE_FORM_ERRORS::INTERNAL_SERVER->value);
        } finally {
            return new Response($this->renderListOrderCreateComponent($form, true, $userGroups));
        }
    }

    private function formNotValid(FormInterface $form, array $userGroups): Response
    {
        return new Response($this->renderListOrderCreateComponent($form, false, $userGroups));
    }

    private function renderListOrderCreateComponent(FormInterface $form, bool $formSubmitted, array $userGroups): string
    {
        $formData = $form->getData();

        $ListOrdersCreateComponentData = new ListOrdersCreateComponentDto(
            $form->getErrors(),
            $formData[LIST_ORDERS_CREATE_FORM_FIELDS::NAME],
            $formData[LIST_ORDERS_CREATE_FORM_FIELDS::DESCRIPTION],
            $formData[LIST_ORDERS_CREATE_FORM_FIELDS::DATE_TO_BUY],
            $formData[LIST_ORDERS_CREATE_FORM_FIELDS::USER_GROUP],
            $userGroups,
            $form->getCsrfToken(),
            $formSubmitted
        );

        return $this->renderView('list_orders/list_orders_create/index.html.twig', [
            'ListOrdersCreateComponent' => $ListOrdersCreateComponentData,
        ]);
    }
}
