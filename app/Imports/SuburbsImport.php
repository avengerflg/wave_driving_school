<?php

namespace App\Imports;

use App\Models\Suburb;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SuburbsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Suburb([
            'name'     => $row['name'],
            'state'    => $row['state'],
            'postcode' => $row['postcode'],
            'active'   => $row['active'] ?? true,
        ]);
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'active' => 'nullable|boolean',
        ];
    }
}
