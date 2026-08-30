<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deterministic Search Synonyms & Term Expansion Map
    |--------------------------------------------------------------------------
    | Maps search terms to canonical product photography keywords across
    | Products, Creative Effects, Environments, Compositions, and Ad Intent.
    |
    */

    // 1. Products & Beverages
    'soda can' => ['soft drink can', 'canned beverage', 'carbonated beverage', 'sparkling drink', 'soda'],
    'soft drink' => ['soda can', 'carbonated drink', 'beverage can', 'canned drink'],
    'soft drink can' => ['soda can', 'canned beverage', 'carbonated beverage'],
    'carbonated beverage' => ['soda can', 'soft drink', 'sparkling drink', 'fizzy drink'],
    'sparkling drink' => ['soda can', 'sparkling water', 'fizzy drink'],
    'canned beverage' => ['soda can', 'canned drink', 'soft drink can'],
    'beverage' => ['drink', 'soda', 'beverage can', 'liquid'],
    'drink' => ['beverage', 'soda', 'drink packshot'],
    'supplement' => ['protein powder', 'whey protein', 'nutrition', 'vitamin'],
    'protein powder' => ['supplement', 'whey protein', 'nutrition jar'],
    'skincare' => ['cleanser', 'cosmetic', 'facial cleanser', 'serum', 'beauty product'],
    'cleanser' => ['facial cleanser', 'skincare', 'cosmetic bottle'],

    // 2. Creative Effects
    'splash' => ['water splash', 'liquid splash', 'splash effect', 'burst'],
    'water splash' => ['splash', 'liquid splash', 'splash effect'],
    'liquid splash' => ['splash', 'water splash', 'fluid splash'],
    'ice' => ['ice cubes', 'crushed ice', 'chilled ice', 'frozen'],
    'crushed ice' => ['ice', 'ice cubes', 'chilled ice'],
    'condensation' => ['water droplets', 'moisture', 'dew', 'sweating'],
    'bubbles' => ['effervescent', 'fizz', 'carbonated bubbles'],
    'floating' => ['levitating', 'suspended', 'airborne'],
    'pouring' => ['liquid pour', 'pouring shot'],

    // 3. Environment & Setting
    'beach' => ['tropical beach', 'ocean', 'sand', 'seashore'],
    'tropical' => ['beach', 'palm trees', 'botanical'],
    'gym' => ['fitness studio', 'workout setting', 'fitness'],
    'kitchen' => ['kitchen counter', 'dining table'],
    'urban' => ['city street', 'streetwear setting', 'downtown'],
    'marble' => ['marble surface', 'white marble', 'stone podium'],
    'concrete' => ['concrete surface', 'podium', 'studio background'],

    // 4. Composition & Shot Types
    'hero shot' => ['hero packshot', 'packshot', 'product showcase'],
    'packshot' => ['hero shot', 'product packshot', 'clean packshot'],
    'close-up' => ['close up', 'closeup', 'macro', 'detail shot'],
    'close up' => ['close-up', 'macro'],
    'macro' => ['close-up', 'extreme close-up', 'detail shot'],
    'top-down' => ['top down', 'flatlay', 'overhead view'],
    'flatlay' => ['top-down', 'overhead shot'],
    '45 degree' => ['45 degree angle', 'perspective view'],

    // 5. Advertising Intent & Campaigns
    'instagram ad' => ['social media ad', 'ad template', 'commercial ad'],
    'social media ad' => ['instagram ad', 'facebook ad', 'digital ad'],
    'billboard' => ['outdoor ad', 'billboard mockup', 'large format ad'],
    'product campaign' => ['brand campaign', 'commercial campaign'],
    'summer campaign' => ['summer ad', 'seasonal campaign', 'sunlit ad'],
    'advertisement' => ['ad', 'commercial', 'promotional visual'],
    'ad' => ['advertisement', 'commercial', 'promo visual'],
];
