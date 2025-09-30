<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Defect;

class DefectSeeder extends Seeder
{
    public function run(): void
    {
        $defects = [
            // CAPS
            ['defect_name' => 'Leak caps', 'category' => 'Caps', 'description' => 'Water leaking into the caps'],
            ['defect_name' => 'Broken caps', 'category' => 'Caps', 'description' => 'Any cracked on the caps'],
            ['defect_name' => 'Loose caps', 'category' => 'Caps', 'description' => 'Caps not properly sealed'],
            ['defect_name' => 'Date code compliance', 'category' => 'Caps', 'description' => 'Missing, incomplete, illegible (pls specify in the remarks)'],
            ['defect_name' => 'Excess flashes on caps', 'category' => 'Caps', 'description' => 'Any excess plastic in the caps'],
            ['defect_name' => 'Caps w/ pin hole', 'category' => 'Caps', 'description' => 'Small hole in the caps'],
            ['defect_name' => 'Dirty caps', 'category' => 'Caps', 'description' => 'Dirt or stain'],
            ['defect_name' => 'Tampered band damage', 'category' => 'Caps', 'description' => '1 tampered band bridge not intact or broken'],
            ['defect_name' => 'Scratch caps', 'category' => 'Caps', 'description' => 'Any scratch present in caps'],
            ['defect_name' => 'Short shot caps', 'category' => 'Caps', 'description' => 'Deformities or uneven cutting'],

            // BOTTLE
            ['defect_name' => 'Low fill', 'category' => 'Bottle', 'description' => 'Fill level below neck bottle'],
            ['defect_name' => 'Empty bottle', 'category' => 'Bottle', 'description' => 'Not filled bottle'],
            ['defect_name' => 'Pearly bottle', 'category' => 'Bottle', 'description' => 'Bottle not clear or is cloudy'],
            ['defect_name' => 'Rocker bottom', 'category' => 'Bottle', 'description' => 'Bottle not stable when standing'],
            ['defect_name' => 'Bottom off centred', 'category' => 'Bottle', 'description' => 'Confirm for leakage'],
            ['defect_name' => 'Dented bottles', 'category' => 'Bottle', 'description' => '≥ 10mm dent'],
            ['defect_name' => 'Damage on neck support ring', 'category' => 'Bottle', 'description' => '≥ 3mm damage'],
            ['defect_name' => 'Bubbles on bottle', 'category' => 'Bottle', 'description' => '≥ 3mm bubbles & confirm for leakage'],
            ['defect_name' => 'Bottle with pin hole', 'category' => 'Bottle', 'description' => 'Any small hole observed'],
            ['defect_name' => 'Scratched on bottle', 'category' => 'Bottle', 'description' => '≥ 10mm scratch'],
            ['defect_name' => 'Water marked on bottle', 'category' => 'Bottle', 'description' => 'Water-like spot observed in the bottle'],
            ['defect_name' => 'Dirty bottle', 'category' => 'Bottle', 'description' => 'Any dirt or stain observed in the bottle'],

            // LABEL
            ['defect_name' => 'Wrong cut label', 'category' => 'Label', 'description' => 'Any out of registration in the label'],
            ['defect_name' => 'Misalign label/label placement', 'category' => 'Label', 'description' => '≥ 3mm misalignment in the label'],
            ['defect_name' => 'Flagged label', 'category' => 'Label', 'description' => '≥ 3mm flag label observed'],
            ['defect_name' => 'Misprint label', 'category' => 'Label', 'description' => 'Any misprint in the label'],
            ['defect_name' => 'Label repeat', 'category' => 'Label', 'description' => '2 labels in a bottle'],
            ['defect_name' => 'Torn label', 'category' => 'Label', 'description' => '≥ 2mm flag label observed'],
            ['defect_name' => 'Label w/ red tape (splice)', 'category' => 'Label', 'description' => 'Splice observed in the label'],
            ['defect_name' => 'Wrinkled label', 'category' => 'Label', 'description' => '≥ wrinkled label'],
            ['defect_name' => 'Visible glue', 'category' => 'Label', 'description' => '≥ 3mm excess glue on the label'],
            ['defect_name' => 'Sticky/messy bottle', 'category' => 'Label', 'description' => 'Excess glue observed anywhere in the bottle'],

            // LDPE Shrinkfilm
            ['defect_name' => 'Out of square', 'category' => 'LDPE Shrinkfilm', 'description' => 'LDPE Shrinkfilm is deformed'],
            ['defect_name' => 'Weak gluing', 'category' => 'LDPE Shrinkfilm', 'description' => 'Side flap pop out'],
            ['defect_name' => 'Wrong print LDPE Shrinkfilm', 'category' => 'LDPE Shrinkfilm', 'description' => 'Wrong design (refer to approved drawing)'],
            ['defect_name' => 'Date code compliance', 'category' => 'LDPE Shrinkfilm', 'description' => 'Missing, incomplete, illegible (pls specify in the remarks)'],
            ['defect_name' => 'Dirty LDPE Shrinkfilm', 'category' => 'LDPE Shrinkfilm', 'description' => 'Any dirt or stain observed on the LDPE Shrinkfilm'],
        ];

        foreach ($defects as $defect) {
            Defect::create($defect);
        }
    }
}
