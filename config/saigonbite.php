<?php

return [
    // A-02 is pending confirmation; 15000 preserves the current local checkout setting.
    'delivery_fee' => (float) env('SAIGONBITE_DELIVERY_FEE', 15000),
    'surcharge' => (float) env('SAIGONBITE_SURCHARGE', 0),
];
