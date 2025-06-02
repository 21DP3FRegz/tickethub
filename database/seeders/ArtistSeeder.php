<?php

namespace Database\Seeders;

use App\Models\Artist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArtistSeeder extends Seeder
{
    public function run()
    {
        Artist::create([
            'name' => 'Prāta Vētra (Brainstorm)',
            'bio' => 'Leģendārā latviešu roka grupa, dibināta 1989. gadā. Zināmi ar hitiem "My Star", "Maybe" un citiem.',
            'image_url' => 'https://example.com/images/prata-vetra.jpg',
        ]);

        Artist::create([
            'name' => 'Latvijas Radio koris',
            'bio' => 'Profesionāls koris, kas dibināts 1940. gadā. Viens no vadošajiem koriem Latvijā.',
            'image_url' => 'https://example.com/images/lr-koris.jpg',
        ]);

        Artist::create([
            'name' => 'Dons',
            'bio' => 'Populārs latviešu dziedātājs un komponists, pazīstams ar romantiskām dziesmām.',
            'image_url' => 'https://example.com/images/dons.jpg',
        ]);

        Artist::create([
            'name' => 'Instrumenti',
            'bio' => 'Latviešu indie pop grupa, dibināta 2008. gadā Rīgā.',
            'image_url' => 'https://example.com/images/instrumenti.jpg',
        ]);

        Artist::create([
            'name' => 'Liepājas Simfoniskais orķestris',
            'bio' => 'Liepājas pilsētas simfoniskais orķestris, dibināts 1945. gadā.',
            'image_url' => 'https://example.com/images/lso.jpg',
        ]);

        Artist::create([
            'name' => 'Carnival Youth',
            'bio' => 'Latviešu indie pop grupa no Rīgas, pazīstama Eiropā.',
            'image_url' => 'https://example.com/images/carnival-youth.jpg',
        ]);

        Artist::create([
            'name' => 'Aija Andrejeva',
            'bio' => 'Latviešu dziedātāja, Eirovīzijas dalībniece un populāras mūzikas izpildītāja.',
            'image_url' => 'https://example.com/images/aija-andrejeva.jpg',
        ]);
    }
}
