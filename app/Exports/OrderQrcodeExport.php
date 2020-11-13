<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Font;

class OrderQrcodeExport implements FromView,ShouldAutoSize
{
    private $exportData;

    public function __construct($data)
    {
        $this->exportData = $data;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('exports.order_qrcode', [
            'exportData' => $this->exportData
        ]);
    }
}
