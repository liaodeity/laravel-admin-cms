<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Font;

class MemberExport implements FromView,ShouldAutoSize
{
    private $exportData;
    private $view_blade;

    public function __construct($data,$view_blade = 'member')
    {
        $this->exportData = $data;
        $this->view_blade = $view_blade;
    }

    /**
     * @return View
     */
    public function view (): View
    {

        if($this->view_blade == 'report_agent_memberBill'){
            $this->memberBillFormat();
        }

        return view ('exports.' . $this->view_blade, [
            'exportData' => $this->exportData
        ]);
    }

    protected function memberBillFormat ()
    {
        $_data = $this->exportData;
        foreach ($this->exportData as $key => $item) {
            $row = 0;
            foreach ($_data as $key2 => $item2) {
                if($item->count_type == $item2->count_type) {
                    $row++;
                    unset($_data{$key2});
                }
            }
            $this->exportData{$key}->row = $row;
        }
    }
}
