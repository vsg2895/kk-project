<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute måste accepteras.',
    'active_url'           => ':attribute är inte en giltig adress.',
    'after'                => ':attribute måste vara ett datum efter :date.',
    'after_or_equal'       => ':attribute måste vara ett datum efter eller lika med :date.',
    'alpha'                => ':attribute får endast innehålla bokstäver.',
    'alpha_dash'           => ':attribute får endast innehålla bokstäver, siffror och bindestreck.',
    'alpha_num'            => ':attribute får endast innehålla bokstäver och siffror.',
    'array'                => ':attribute måste vara en array.',
    'before'               => ':attribute måste vara ett datum före :date.',
    'before_or_equal'      => ':attribute måste vara ett datum före eller lika med :date.',
    'between'              => [
        'numeric' => ':attribute måste vara mellan :min och :max.',
        'file'    => ':attribute måste ha en storlek mellan :min och :max kilobytes.',
        'string'  => ':attribute måste vara mellan :min och :max tecken långt.',
        'array'   => ':attribute måste ha mellan :min och :max antal objekt.',
    ],
    'boolean'              => ':attribute måste vara sant eller falskt.',
    'confirmed'            => ':attribute överrensstämmer ej.',
    'date'                 => ':attribute är inte ett giltigt datum.',
    'date_format'          => ':attribute matchar inte formatet :format.',
    'different'            => ':attribute och :other måste skiljas åt.',
    'digits'               => ':attribute måste vara :digits siffror.',
    'digits_between'       => ':attribute måste vara mellan :min och :max siffror.',
    'dimensions'           => ':attribute har en ogiltig upplösning.',
    'distinct'             => ':attribute innehåller duplicerade värden.',
    'email'                => ':attribute måste vara en giltig E-postadress.',
    'exists'               => 'Vald :attribute är ogiltig.',
    'file'                 => ':attribute måste vara en fil.',
    'filled'               => ':attribute är obligatoriskt.',
    'image'                => ':attribute måste vara en bild.',
    'in'                   => 'Vald :attribute är ogiltig.',
    'in_array'             => ':attribute existerar inte i :other.',
    'integer'              => ':attribute måste vara ett heltal.',
    'ip'                   => ':attribute måste vara en giltig IP-adress.',
    'json'                 => ':attribute måste vara en giltig JSON sträng.',
    'max'                  => [
        'numeric' => ':attribute får inte vara större än :max.',
        'file'    => ':attribute får inte vara större än :max kilobytes.',
        'string'  => ':attribute får inte vara längre än :max tecken.',
        'array'   => ':attribute får inte innehålla mer än :max objekt.',
    ],
    'mimes'                => ':attribute måste vara en fil av typen: :values.',
    'mimetypes'            => ':attribute måste vara en fil av typen: :values.',
    'min'                  => [
        'numeric' => ':attribute måste vara minst :min.',
        'file'    => ':attribute måste minst vara :min kilobytes stor.',
        'string'  => ':attribute måste innehålla minst :min tecken.',
        'array'   => ':attribute måste minst innehålla :min objekt.',
    ],
    'not_in'               => 'Vald :attribute är ogiltig.',
    'numeric'              => ':attribute måste vara ett nummer.',
    'present'              => ':attribute måste vara närvarande.',
    'regex'                => ':attribute format är ogiltigt.',
    'required'             => ':attribute är obligatoriskt.',
    'required_if'          => ':attribute är obligatoriskt när :other är :value.',
    'required_unless'      => ':attribute är obligatoriskt om inte :other existerar i :values.',
    'required_with'        => ':attribute är obligatoriskt när :values är angett.',
    'required_with_all'    => ':attribute är obligatoriskt när :values är angett.',
    'required_without'     => ':attribute är obligatoriskt när :values inte är angett.',
    'required_without_all' => ':attribute är obligatoriskt när ingen av :values är angett.',
    'same'                 => ':attribute och :other måste matcha.',
    'size'                 => [
        'numeric' => ':attribute måste vara :size.',
        'file'    => ':attribute måste vara :size kilobytes.',
        'string'  => ':attribute måste vara :size tecken långt.',
        'array'   => ':attribute måste innehålla :size objekt.',
    ],
    'string'               => ':attribute måste vara en sträng.',
    'timezone'             => ':attribute måste vara en giltig tidszon.',
    'unique'               => ':attribute används redan.',
    'uploaded'             => ':attribute misslyckades med uppladdningen.',
    'url'                  => ':attribute har ett ogiltigt format.',

    //Custom validation rule
    'password_match' => 'Fel lösenord.',
    'phone_number' => 'Ogiltigt telefonnummer',
    'org_number' => 'Ogiltigt organisationsnummer',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'latitude' => [
            'required' => 'Kunde inte hämta latitud, ange adressen på nytt.',
        ],
        'longitude' => [
            'required' => 'Kunde inte hämta longitud, ange adressen på nytt.',
        ],
        'addons.*.price' => [
            'required_with' => 'Du måste ange pris på aktiva tillval.'
        ],
        'prices.*.amount' => [
            'required_with' => 'Du måste ange pris.'
        ],
        'prices.*.quantity' => [
            'required_with' => 'Du måste ange längd i minuter.'
        ],
        'start_time' => [
            'after' => 'Startdatum måste vara en tid i framtiden.'
        ],
        'org_number'=> [
            'unique' => 'En trafikskola med detta organisationsnummer finns redan.'
        ],
        'vehicle_segment_id' => [
            'exists' => 'Vald kurstyp är ogiltig.'
        ],
        'message' => [
            'required' => 'Kommentaren får inte vara tom.'
        ],
        'vehicles' => [
            'array' => 'Minst en av fordonstyperna nedan måste väljas'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'vehicle_segment_id' => 'Fordonstyp',
        'rating' => 'Omdömet',
        'given_name' => 'Förnamn',
        'family_name' => 'Efternamn',
        'phone_number' => 'Telefonnummer',
        'password' => 'Lösenordet',
        'name' => 'Namn',
        'org_number' => 'Organisationsnummer',
        'email' => 'Email',
        'start_time' => 'Kursstart',
        'length_minutes' => 'Längd',
        'price' => 'Pris',
        'address' => 'Adress',
        'coaddress' => 'C/O Adress',
        'description' => 'Beskrivning',
        'address_description' => 'Addressbeskrivning',
        'confirmation_text' => 'Bekräftelsemeddelande',
        'seats' => 'Platser',
        'city_id' => 'Stad',
        'school_id' => 'Trafikskola',
        'zip' => 'Postnummer',
        'postal_city' => 'Postort',
        'contact_email' => 'Kontaktemail',
        'addons.*.price' => 'Pris',
        'addons.*.id' => 'Tillval',
        'prices.*.quantity' => 'Längd i minuter',
        'prices.*.amount' => 'Pris',
        'website' => 'Hemsida',
        'new_user.email' => 'Email',
        'new_user.given_name' => 'Förnamn',
        'new_user.family_name' => 'Efternamn',
        'new_user.phone_number' => 'Telefonnummer',
        'image' => 'Bilden',
        'file_name' => 'Bildnamn',
        'alt_text' => 'Textbeskrivning'
    ],

];
