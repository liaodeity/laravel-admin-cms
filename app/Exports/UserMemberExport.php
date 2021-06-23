<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/
namespace App\Exports;

use App\Models\Log;
use App\Models\User\UserMember;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UserMemberExport extends DefaultValueBinder implements FromView, ShouldAutoSize, WithColumnWidths, WithColumnFormatting, WithCustomValueBinder
{
    private $resultData;

    public function __construct ($result)
    {
        $this->resultData = $result;
    }

    public function bindValue (Cell $cell, $value)
    {
        if (is_numeric ($value) && strlen ($value) >= 10) {
            $cell->setValueExplicit ($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue ($cell, $value);
    }

    public function columnWidths (): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 25,
        ];
    }

    public function columnFormats (): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }

    /**
     * @return View
     */
    public function view (): View
    {
        $details = $this->resultData;
        foreach ($details as $key => $item) {
            //
        }

        Log::createLog (Log::INFO_TYPE, '进行会员记录', '', 0, UserMember::class);

        return view ('admin.user_member.list_export', compact ('details'));
    }
}
