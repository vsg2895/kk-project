<?php namespace Api\Http\Controllers;

use Api\Http\Requests\StoreContactRequest;
use Jakten\Services\ContactService;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class ContactController
 * @package Api\Http\Controllers
 */
class ContactController extends ApiController
{
    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * ContactController constructor.
     *
     * @param ContactService $contactService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(ContactService $contactService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->contactService = $contactService;
    }

    /**
     * @param StoreContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeContact(StoreContactRequest $request)
    {
        $this->contactService->contactStore($request);

        return $this->success();
    }
}
