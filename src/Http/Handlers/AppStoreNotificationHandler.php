<?php

namespace Imdhemy\Purchases\Http\Handlers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Imdhemy\AppStore\ServerNotifications\ServerNotification;
use Imdhemy\Purchases\Contracts\NotificationHandlerContract;
use Imdhemy\Purchases\Events\AppStore\EventFactory as AppStoreEventFactory;
use Imdhemy\Purchases\Http\Requests\AppStoreServerNotificationRequest;
use Imdhemy\Purchases\ServerNotifications\AppStoreServerNotification;

/**
 * App Store server notification handler
 *
 * Handles notifications sent by App store.
 * Dispatches the App store subscription event related to the notification type.
 */
class AppStoreNotificationHandler implements NotificationHandlerContract
{
    /**
     * @var AppStoreServerNotificationRequest
     */
    private $request;

    /**
     * @param AppStoreServerNotificationRequest $request
     */
    public function __construct(AppStoreServerNotificationRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @throws AuthorizationException
     */
    public function execute()
    {
        if (!$this->request->authorize()) {
            throw new AuthorizationException();
        }

        $this->request->validate($this->request->rules());

        $attributes = $this->request->all();
        $serverNotification = ServerNotification::fromArray($attributes);
        $appStoreNotification = new AppStoreServerNotification($serverNotification);

        if ($appStoreNotification->isTest()) {
            Log::info('AppStore Test Notification');
        }

        $event = AppStoreEventFactory::create($appStoreNotification);
        event($event);
    }
}