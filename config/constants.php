<?php

return [
    'course_types' => [
        'undergraduate' => 'Undergraduate',
        'postgraduate' => 'Postgraduate',
        'diploma' => 'Diploma',
        'certificate' => 'Certificate',
        'phd' => 'PhD',

    ],
    'statuses' => [
        'active' => 1,
        'inactive' => 0,
    ],
    'currency_symbols' => [
        'INR' => '₹',
        'USD' => '$',
        'EUR' => '€',
    ],
    'accademic_entry_requirement' => [
        '40+' => '40% and Above',
        '45+' => '45% and Above',
        '50+' => '50% and Above',
        '55+' => '55% and Above',
        '60+' => '60% and Above',
        '65+' => '65% and Above',
        '70+' => '70% and Above',
        '75+' => '75% and Above',
        '80+' => '80% and Above',
        '55-59' => '55% - 59%',
        '60-65' => '60% - 65%',
        '66-69' => '66% - 69%',
    ],
    'english_waiver' => [
        'CBSE 80+' => 'CBSE 80% and Above',
        'Any Board 60+' => 'Any Board 60% and Above',
        'Any Board 65+' => 'Any Board 65% and Above',
        'Any Board 70+' => 'Any Board 70% and Above',
        'Any Board 75+' => 'Any Board 75% and Above',
        'Any Board 80+' => 'Any Board 80% and Above',
    ],


    'product_units' => [
        'kg' => 'Kg',
        'liter' => 'Liter',
        'meter' => 'Meter',
        // Add more options as needed
    ],
    //credit Means User Get Money From Other From like client and vender
    //Debit User Send money to client and vendor
    'transaction_types' => [
        'debit' => 'Money Sending',
        'credit' => 'Money Received',
        'Material Send' => 'Material Send',
        'Material Receive' => 'Material Receive',
    ],
];
