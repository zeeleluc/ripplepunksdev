<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Badge Tiers
    |--------------------------------------------------------------------------
    |
    | These tiers define the minimum NFT count thresholds required to earn
    | each badge for any, OG, and Other punk collections.
    |
    */

    'tiers' => [
        500 => ['Ledger Legend', 'Chain Immortal', 'Cyber Monarch'],
        350  => ['Meta Mogul', 'OG Tycoon', 'Neo-Punk Magnate'],
        200  => ['Digital Don', 'Original Boss', 'Uprising Leader'],
        100  => ['Ripple Overlord', 'Ledger Lord', 'Punk Syndicate'],
        50  => ['Punk King', 'Ripple Monarch', 'Chain King'],
        25   => ['Vault Dweller', 'Time-Locked', 'Deep Vault'],
        10   => ['Street Raider', 'Genesis Raider', 'Colony Climber'],
        1    => ['Punk', 'OG Initiate', 'Other Punk'],
    ],

];
