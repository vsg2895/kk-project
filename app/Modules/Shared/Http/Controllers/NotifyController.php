<?php namespace Shared\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Helpers\NotifyChannels;
use Illuminate\Support\Facades\Auth;
use Jakten\Repositories\NotifyEventsRepository;
use Jakten\Repositories\NotifySettingsRepository;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class NotifyController
 * @package Admin\Http\Controllers
 */
class NotifyController extends Controller
{
    /**
     * @var NotifyEventsRepository
     */
    private $notifyEvents;

    /**
     * @var NotifySettingsRepository
     */
    private $notifySettings;

    /**
     * NotifyController constructor.
     *
     * @param NotifyEventsRepository $notifyEvents
     * @param NotifySettingsRepository $notifySettings
     * @param KKJTelegramBotService $botService
     */
    public function __construct(NotifyEventsRepository $notifyEvents, NotifySettingsRepository $notifySettings, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->notifyEvents = $notifyEvents;
        $this->notifySettings = $notifySettings;
    }

    /**
     * Notification settings page
     */
    public function index()
    {
        $notifyEvents = $this->notifyEvents->getNotifyEvents();

        $disabledTypes = [
            NotifyChannels::$types[NotifyChannels::TYPE_SMS]
        ];

        return view('shared::notify.settings', compact('notifyEvents', 'disabledTypes'));
    }

    /**
     * Update notifications settings
     *
     * @param Request $request
     * @return string
     */
    public function update(Request $request)
    {
        $formEvents = $request->input('events');
        $notifyEvents = $this->notifyEvents->getNotifyEvents();

        $this->notifySettings->updateSettings($formEvents, $notifyEvents);

        $request->session()->flash('message', 'Notiser uppdaterad!');

        return redirect()->back();
    }

    /**
     * Show list of messages
     */
    public function messages()
    {
        $user = Auth::user();

        return view('shared::notify.messages', compact('user'));
    }

    /**
     * Mark as read message
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mark(Request $request)
    {
        $notification = Auth::user()->unreadNotifications->first(function ($notification) use ($request) {
            return $notification->id == $request->notify_id;
        });

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }
}
