<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notifications Language Lines
    |
    | Important remarks:
    |
    | These phrases can be combine like: "Notification: New Order".
    | All words started with ":" (like ":date" or ":name") should not be translated,
    | because it's a placeholders for variables which will be taken from database.
    |
    |--------------------------------------------------------------------------
    */

    // General phrases
    'new_order' => 'New Order',
    'new_register' => 'New registration',
    'order_cancelled' => 'Order cancelled',
    'order_failed' => 'Order failed',
    'order_id' => 'OrderId: :id',
    'notification' => 'Notification: ',
    'created_at' => 'Created at: :date',
    'registered_at' => 'Registered at :date',

    // Dashboard messages
    'dash_new_order' => 'New order has been created at :date. OrderId: :id',
    'dash_new_user' => 'New user :name has been registered at :date',
    'dash_order_failed' => 'Order failed for user :name. PaymentId: :id'
];