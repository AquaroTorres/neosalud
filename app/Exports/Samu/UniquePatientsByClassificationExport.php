<?php

namespace App\Exports\Samu;

use App\Http\Livewire\Samu\MinsalStatistics;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class UniquePatientsByClassificationExport implements FromView, WithTitle
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function view(): View
    {
        $component = new MinsalStatistics();
        $component->year = $this->year;
        $data = $component->getUniquePatientsAttendedByClassification();

        return view('samu.minsal.unique-patients-by-classification', [
            'data' => $data,
            'year' => $this->year
        ]);
    }

    public function title(): string
    {
        return 'Pacientes por Clasificación ' . $this->year;
    }
}
