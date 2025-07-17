<?php

namespace App\Http\Livewire\Samu;

use Livewire\Component;
use App\Models\Samu\Event;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class RemStatistics extends Component implements FromView
{
    protected $listeners = ['updateDate' => 'changePeriod'];

    public $month;

    public $year;

    protected $chartData;

    public const LABELS = [
        'SCA' => 'Sindrome Coronario Agudo',
        'PCR' => 'Paro Cardiaco Respiratorio',
        'PT' => 'Politraumatismo',
        'OTHERS' => 'Otros',
    ];

    protected const SCA = [5];

    protected const PCR = [2];

    protected const PT = [63, 64, 65, 66, 67, 68, 69]; //201*, 401d, 402b, 701i 

    protected const CRITICAL = [2, 13, 35, 36, 37, 46, 66, 78, 86, 93, 96, 97, 127]; //['101a', '102a', '105a', '105b', '105c', '107a', '201c', '401b', '402b', '501a', '501b', '403d', '701i']

    protected const MOBILES = [
        'BASIC' => [1, 5],
        'ADVANCED' => [2, 3, 6],
        'HYBRID' => [4]
    ];

    protected $ages = [
        ['0', '4'],
        ['5', '9'],
        ['10', '14'],
        ['15', '19'],
        ['20', '24'],
        ['25', '29'],
        ['30', '34'],
        ['35', '39'],
        ['40', '44'],
        ['45', '49'],
        ['50', '54'],
        ['55', '59'],
        ['60', '64'],
        ['65', '69'],
        ['70', '74'],
        ['75', '79'],
        ['80', '+'],
    ];

    public $showExportModal = false;

    public $exportSection = 'N';

    public function mount()
    {
        $this->changePeriod();
    }

    public function render()
    {
        $stats = $this->getStats();

        return view('livewire.samu.rem-statistics', [
            'K' => $stats['section_k'],
            'N' => $stats['section_n'],
            'L' => $stats['section_l'],
        ]);
    }

    public function getEvents()
    {
        $events = Event::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->with('key', 'call')
            ->has('call');
        return $events;
    }

    public function getStats()
    {
        $events = $this->getEvents();
        return [
            'section_k' => $this->getSectionK($events),
            'section_n' => $this->getSectionN($events),
            'section_l' => $this->getSectionL($events),
        ];
    }

    public function getSectionK($events)
    {
        $events = $events->get();
        // Basic
        $basicEvents = $events->whereIn('mobile_id', self::MOBILES['BASIC']);
        $basicCriticalEvents = $basicEvents->whereIn('key_id', self::CRITICAL);
        $basicNonCriticalEvents = $basicEvents->whereNotIn('key_id', self::CRITICAL);
        $basicCriticalArrivalTimes = $this->getTimeRanges($basicCriticalEvents->whereNotNull('mobile_departure_at')->whereNotNull('mobile_arrival_at'));
        $basicNonCriticalArrivalTimes = $this->getTimeRanges($basicNonCriticalEvents->whereNotNull('mobile_departure_at')->whereNotNull('mobile_arrival_at'));

        // Advanced
        $advancedEvents = $events->whereIn('mobile_id', self::MOBILES['ADVANCED']);
        $advancedCriticalEvents = $advancedEvents->whereIn('key_id', self::CRITICAL);
        $advancedNonCriticalEvents = $advancedEvents->whereNotIn('key_id', self::CRITICAL);
        $advancedCriticalArrivalTimes = $this->getTimeRanges($advancedCriticalEvents->whereNotNull('mobile_departure_at')->whereNotNull('mobile_arrival_at'));
        $advancedNonCriticalArrivalTimes = $this->getTimeRanges($advancedNonCriticalEvents->whereNotNull('mobile_departure_at')->whereNotNull('mobile_arrival_at'));

        return [
            'BASIC' => [
                'COUNT' => [
                    'TOTAL' => $basicEvents->count(),
                    'CRITICAL' => $basicCriticalEvents->count(),
                    'UNCRITICAL' => $basicNonCriticalEvents->count(),
                ],
                'RECIPIENTS' => $basicEvents->unique('patient_identification')->count(),
                'CRITICAL' => $basicCriticalArrivalTimes,
                'UNCRITICAL' => $basicNonCriticalArrivalTimes,
            ],
            'ADVANCED' => [
                'COUNT' => [
                    'TOTAL' => $advancedEvents->count(),
                    'CRITICAL' => $advancedCriticalEvents->count(),
                    'UNCRITICAL' => $advancedNonCriticalEvents->count(),
                ],
                'RECIPIENTS' => $advancedEvents->unique('patient_identification')->count(),
                'CRITICAL' => $advancedCriticalArrivalTimes,
                'UNCRITICAL' => $advancedNonCriticalArrivalTimes,
            ],
        ];
    }

    public function getSectionN($events)
    {
        $events = $events->get();
        $SCA = $this->ageRangeByGender($events, self::SCA);
        $PCR = $this->ageRangeByGender($events, self::PCR);
        $PT = $this->ageRangeByGender($events, self::PT);
        $OTHERS = $this->ageRangeByGender($events->whereNotIn('key_id', array_merge(self::SCA, self::PCR, self::PT)));

        $criticalEvents = $events->whereIn('key_id', self::CRITICAL);
        $nonCriticalEvents = $events->whereNotIn('key_id', self::CRITICAL);
        $CRITICAL = $this->ageRangeByGender($criticalEvents, self::CRITICAL);
        $UNCRITICAL = $this->ageRangeByGender($nonCriticalEvents, null);

        return [
            'SCA' => $SCA,
            'PCR' => $PCR,
            'PT' => $PT,
            'OTHERS' => $OTHERS,
            'CRITICAL' => $CRITICAL,
            'UNCRITICAL' => $UNCRITICAL,
        ];
    }

    public function getSectionL($events)
    {
        $filteredEvents = $events->whereHas('call', function ($query) {
            $query->where('classification', 'T1');
        })->get();
        $basicEvents = $filteredEvents->where('mobile_id', 1);
        $advancedEvents = $filteredEvents->whereIn('mobile_id', [2, 3]);
        $basicTotal = $basicEvents->count();
        $basicUniques = $basicEvents->unique('patient_identification')->count();
        $advancedTotal = $advancedEvents->count();
        $advancedUniques = $advancedEvents->unique('patient_identification')->count();
        return [
            'BASIC' => [
                'TOTAL' => $basicTotal,
                'UNIQUE' => $basicUniques,
            ],
            'ADVANCED' => [
                'TOTAL' => $advancedTotal,
                'UNIQUE' => $advancedUniques,
            ]
        ];
    }

    public function ageRangeByGender($events, array $key = null)
    {
        $genderByAge = [];

        // Events Variables
        $maleEvents = $events->filter(fn($e) => $e->gender_id == 1);
        $maleEvents = $key ? $maleEvents->whereIn('key_id', $key) : $maleEvents;
        $femaleEvents = $events->filter(fn($e) => $e->gender_id == 2);
        $femaleEvents = $key ? $femaleEvents->whereIn('key_id', $key) : $femaleEvents;

        // Total Count
        $genderByAge['total']['both'] = $key ? $events->whereIn('key_id', $key)->count() : $events->count();
        $genderByAge['total']['male'] = $maleEvents->count();
        $genderByAge['total']['female'] = $femaleEvents->count();

        foreach ($this->ages as $range) {
            $genderByAge[$range[0] . '-' . $range[1]]['male'] = $maleEvents->filter($ageFilter = function ($event) use ($range) {
                return ($range[1] === '+') ? ($event->age_year >= $range[0]) : ($event->age_year >= $range[0] && $event->age_year <= $range[1]);
            })->count();
            $genderByAge[$range[0] . '-' . $range[1]]['female'] = $femaleEvents->filter($ageFilter)->count();
        }
        return $genderByAge;
    }

    public function getTransportTime($event)
    {
        return $event->mobile_arrival_at->diffInMinutes($event->mobile_departure_at);
    }

    public function getTimeRanges($events)
    {
        $times = $events->map(function ($event) {
            if ($event->mobile_departure_at && $event->mobile_arrival_at) {
                return $this->getTransportTime($event);
            }
            return null;
        })->filter(function ($time) {
            return $time !== null; // Filter out null values
        })->toArray();
        $arrivalTimes = [];
        $arrivalTimes['0 - 20 min'] = count(array_filter($times, function ($time) {
            return $time <= 20;
        }));

        $arrivalTimes['20 - 40 min'] = count(array_filter($times, function ($time) {
            return $time > 20 && $time <= 40;
        }));

        $arrivalTimes['More than 40 min'] = count(array_filter($times, function ($time) {
            return $time > 40;
        }));
        return $arrivalTimes;
    }

    public function showExportOptions($show = false)
    {
        // $this->showExportModal = $show;
        $this->showExportModal = false;
    }

    public function setExportSection($section)
    {
        $this->exportSection = $section;
        $this->showExportOptions(false);
        if ($section === 'K') {
            $this->exportSection = 'K';
        } else if ($section === 'N') {
            $this->exportSection = 'N';
        } else {
            $this->exportSection = 'L';
        }
        return Excel::download(new RemStatistics, 'Seccion' . $this->exportSection . '.xlsx');
    }

    public function view(): View
    {
        $stats = $this->getStats();
        $view = view('samu.rem');
        switch ($this->exportSection) {
            case 'K':
                $view = view('samu.rem.export-k', ['stats' => $stats]);
                break;
            case 'N':
                $view = view(
                    'samu.rem.export-n',
                    [
                        'stats' => $stats,
                        'ages' => $this->ages,
                        'labels' => self::LABELS
                    ]
                );
                break;
            case 'L':
                $view = view('samu.rem.export-l', ['stats' => $stats]);
                break;
        }
        return $view;
    }

    public function changePeriod($month = null, $year = null)
    {
        $this->month = $month ?? now()->month;
        $this->year = $year ?? now()->year;
    }
}
