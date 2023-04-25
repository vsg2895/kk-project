<?php namespace Jakten\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StudentsExport implements FromView
{
    /**
     * @var
     */
    private $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function view() : View
    {
        return view('admin::reports.export', [
            'students' => $this->students->get()
        ]);
    }
}
