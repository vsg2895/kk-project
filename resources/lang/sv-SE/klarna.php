<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Klarna Language Lines
    |--------------------------------------------------------------------------
    */

    'status' => [
        \Jakten\Helpers\KlarnaSignup::STATUS_NOT_INITIATED => 'Inte påbörjad.',
        \Jakten\Helpers\KlarnaSignup::STATUS_WAITING => 'Förfrågan skickad, väntar på svar från Klarna.',
        \Jakten\Helpers\KlarnaSignup::STATUS_SUCCESS => 'Förfrågan accepterad, Klarna väntar på ifyllning av riskbedömingsformulär.',
        \Jakten\Helpers\KlarnaSignup::STATUS_REJECTED => 'Förfrågan nekad: ',
        \Jakten\Helpers\KlarnaSignup::STATUS_ACCESSED => 'Förfrågan accepterad, Klarna väntar på ifyllning av riskbedömingsformulär. Kund har öppnat formuläret.',
        \Jakten\Helpers\KlarnaSignup::STATUS_CANCELLED => 'Förfrågan accepterad, Klarna väntar på ifyllning av riskbedömingsformulär. Kund har öppnat formuläret men avbrutit.',
        \Jakten\Helpers\KlarnaSignup::STATUS_SUBMITTED => 'Förfrågan accepterad, riskbedömingsformulär i fyllt. Väntar på svar från Klarna.',
        \Jakten\Helpers\KlarnaSignup::STATUS_COMPLETED => 'Allt är klart.',
    ]

];
