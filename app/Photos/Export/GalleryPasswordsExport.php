<?php

namespace App\Photos\Export;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;


class GalleryPasswordsExport implements FromCollection
{
    use Exportable;

    protected $subgalleries;

    public function __construct($gallery)
    {
        $this->subgalleries = $gallery->subgalleries;
    }

    public function collection()
    {
        $prepared_data[] = $this->headings();
        foreach ($this->subgalleries as $subgallery){
            $prepared_data[] = [
                $subgallery->id,
                $subgallery->name,
                $subgallery->password,
            ];
        }

        return collect($prepared_data);
    }

    public function headings(): array
    {
        return [
            'sub_id',
            'sub_name',
            'sub_password',
        ];
    }
}
