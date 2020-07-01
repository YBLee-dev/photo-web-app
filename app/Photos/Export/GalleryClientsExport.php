<?php

namespace App\Photos\Export;

use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;


class GalleryClientsExport implements FromCollection
{
    use Exportable;

    /** @var Person [] */
    protected $people;

    /**
     * GalleryClientsExport constructor.
     *
     * @param $gallery
     */
    public function __construct(Gallery $gallery)
    {
        $this->people = $gallery->people;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $prepared_data[] = $this->headings();

        foreach ($this->people as $person){
            $prepared_data[] = [
                $person->id,
                $person->subgallery->name,
                $person->first_name,
                $person->last_name,
                $person->classroom,
                $person->school_name,
                $person->graduate ? 'yes' : 'no',
                $person->teacher ? 'yes' : 'no',
                $person->title,
                $person->subgallery->password
            ];
        }

        return collect($prepared_data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'SubGallery Name',
            'First Name',
            'Last Name',
            'Classroom',
            'School Name',
            'Graduate',
            'Teacher',
            'Title',
            'Password'
        ];
    }
}
