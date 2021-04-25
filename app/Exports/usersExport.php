<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\withHeadings;

class usersExport implements withHeadings, FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // $usersData = User::where('status', 1)->orderBy('id', 'Desc')->get();
        // return $usersData;

        ////////////// Return with Headings after importinh use use Maatwebsite\Excel\Concerns\withHeadings;
        $usersData = User::select('name', 'email', 'city', 'address')->where('status', 1)->orderBy('id', 'Desc')->get();
        return $usersData;
    }

    public function headings(): array{
        return ['Name', 'Email', 'City', 'State'];
    }
}
