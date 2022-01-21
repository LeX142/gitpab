<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class SpentByProjectExports implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private Collection $data;

    public function __construct(LazyCollection $data)
    {
        $d = collect($data);
        $d = $d->push(['','','','','']);
        $d = $d->push(['','','','Total',$data->sum('hours')]);
        $this->data = $d;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Date', 'Project', 'Description (issue name)', 'Closed at (issue)','Hours'];
    }
}
