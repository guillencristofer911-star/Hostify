<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Toalla de baño',    'unit' => 'unidad',  'min_quantity' => 2],
            ['name' => 'Toalla de mano',    'unit' => 'unidad',  'min_quantity' => 2],
            ['name' => 'Sábana cama',       'unit' => 'juego',   'min_quantity' => 1],
            ['name' => 'Funda almohada',    'unit' => 'unidad',  'min_quantity' => 2],
            ['name' => 'Jabón de baño',     'unit' => 'unidad',  'min_quantity' => 2],
            ['name' => 'Shampoo',           'unit' => 'unidad',  'min_quantity' => 1],
            ['name' => 'Papel higiénico',   'unit' => 'rollo',   'min_quantity' => 4],
            ['name' => 'Control remoto TV', 'unit' => 'unidad',  'min_quantity' => 1],
            ['name' => 'Vaso de baño',      'unit' => 'unidad',  'min_quantity' => 2],
            ['name' => 'Bolsa de basura',   'unit' => 'unidad',  'min_quantity' => 2],
        ];

        foreach ($items as $item) {
            InventoryItem::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['is_active' => true])
            );
        }

        $this->command->info(' 10 artículos de inventario creados.');
    }
}
