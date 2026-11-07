<?php

namespace Database\Seeders;

use App\Models\Maker;
use App\Models\Model;
use Illuminate\Database\Seeder;

class ModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Land Cruiser'],
            'Lexus' => ['RX 350', 'ES 350', 'LX 570', 'NX 300'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot'],
            'Ford' => ['F-150', 'Mustang', 'Explorer', 'Escape'],
            'BMW' => ['3 Series', '5 Series', 'X5', 'X3'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'GLC', 'GLE'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Patrol'],
        ];

        foreach ($models as $makerName => $names) {
            $maker = Maker::where('name', $makerName)->firstOrFail();

            foreach ($names as $name) {
                Model::firstOrCreate([
                    'maker_id' => $maker->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
