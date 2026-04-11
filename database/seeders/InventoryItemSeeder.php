<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Ropa blanca
            ['name' => 'Toalla de baño',     'category' => 'ropa_blanca',  'unit' => 'unidad', 'min_quantity' => 2],
            ['name' => 'Toalla de mano',     'category' => 'ropa_blanca',  'unit' => 'unidad', 'min_quantity' => 2],
            ['name' => 'Sábana cama',        'category' => 'ropa_blanca',  'unit' => 'juego',  'min_quantity' => 1],
            ['name' => 'Funda almohada',     'category' => 'ropa_blanca',  'unit' => 'unidad', 'min_quantity' => 2],

            // Amenidades
            ['name' => 'Jabón de baño',      'category' => 'amenidades',   'unit' => 'unidad', 'min_quantity' => 2],
            ['name' => 'Shampoo',            'category' => 'amenidades',   'unit' => 'unidad', 'min_quantity' => 1],
            ['name' => 'Acondicionador',     'category' => 'amenidades',   'unit' => 'unidad', 'min_quantity' => 1],
            ['name' => 'Papel higiénico',    'category' => 'amenidades',   'unit' => 'rollo',  'min_quantity' => 4],
            ['name' => 'Vaso de baño',       'category' => 'amenidades',   'unit' => 'unidad', 'min_quantity' => 2],
            ['name' => 'Bolsa de basura',    'category' => 'amenidades',   'unit' => 'unidad', 'min_quantity' => 2],

            // Electrónico
            ['name' => 'Control remoto TV',  'category' => 'electronico',  'unit' => 'unidad', 'min_quantity' => 1],
            ['name' => 'Control A/C',        'category' => 'electronico',  'unit' => 'unidad', 'min_quantity' => 1],

            // Mobiliario / dotación
            ['name' => 'Almohada',           'category' => 'dotacion',     'unit' => 'unidad', 'min_quantity' => 2],
            ['name' => 'Cobija',             'category' => 'dotacion',     'unit' => 'unidad', 'min_quantity' => 1],
            ['name' => 'Percha ropa',        'category' => 'dotacion',     'unit' => 'unidad', 'min_quantity' => 4],
        ];

        foreach ($items as $item) {
            InventoryItem::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['is_active' => true])
            );
        }

        $this->command->info('✔ 15 artículos de inventario creados con categoría.');
    }
}