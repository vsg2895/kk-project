<?php namespace Api\Http\Controllers;

use Jakten\Repositories\Contracts\GiftCardTypeRepositoryContract;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class GiftCardTypeController
 * @package Api\Http\Controllers
 */
class GiftCardTypeController extends ApiController
{
    /**
     * @var GiftCardTypeRepositoryContract
     */
    private $giftCardTypes;

    public function __construct(GiftCardTypeRepositoryContract $giftCardTypes, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->giftCardTypes = $giftCardTypes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->giftCardTypes->getAll();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->giftCardTypes->getById($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
