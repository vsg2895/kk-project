<?php

namespace Jakten\Exports;

use Illuminate\Support\Collection;
use Jakten\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchoolsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return Collection
     */
    public function collection()
    {
        $schools = new Collection();

        School::all()
            ->each(function (School $school) use (&$schools) {
                $schools->push([
                    $school->id,
                    $school->name,
                    $school->full_address
                ]);
            });

        return $schools;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID', 'Name', 'Address'
        ];
    }
}
