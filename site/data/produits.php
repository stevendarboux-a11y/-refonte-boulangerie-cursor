<?php

declare(strict_types=1);

return [
    'categories' => [
        [
            'titre' => 'Les pains',
            'produits' => [
                ['nom' => 'Baguette tradition', 'prix' => '1,30 €'],
                ['nom' => 'Baguette classique', 'prix' => '1,10 €'],
                ['nom' => 'Pain de campagne (500g)', 'prix' => '3,20 €'],
                ['nom' => 'Pain au petit épeautre', 'prix' => '4,50 €'],
                ['nom' => 'Pain aux noix', 'prix' => '3,90 €'],
            ],
        ],
        [
            'titre' => 'Viennoiseries',
            'produits' => [
                ['nom' => 'Croissant', 'prix' => '1,20 €'],
                ['nom' => 'Pain au chocolat', 'prix' => '1,35 €'],
                ['nom' => 'Chausson aux pommes', 'prix' => '1,90 €'],
                ['nom' => 'Brioche aux pralines', 'prix' => '2,80 €'],
            ],
        ],
        [
            'titre' => 'Pâtisseries',
            'produits' => [
                ['nom' => 'Éclair au chocolat', 'prix' => '3,20 €'],
                ['nom' => 'Tarte aux pralines (part)', 'prix' => '3,50 €'],
                ['nom' => 'Flan pâtissier (part)', 'prix' => '2,90 €'],
                ['nom' => 'Paris-Brest', 'prix' => '4,20 €'],
            ],
        ],
    ],
    'commande' => 'Gâteaux sur commande : nous appeler.',
];
