<?php

namespace App\Photos;

use App\Photos\People\PersonRepo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GalleryClientsImport implements ToCollection
{
    /**
     * @param Collection $collection
     *
     * @throws \Exception
     */
    public function collection(Collection $collection)
    {
        $clientRepo = new PersonRepo();

        foreach ($collection as $client_data){
            // Check the count of columns
            if(count($client_data) == 10){
                if($client = $clientRepo->getByID((int)$client_data[0])){

                    // Update person data
                    $new_data['first_name'] = $client_data[2];
                    $new_data['last_name'] = $client_data[3];
                    $new_data['classroom'] = $client_data[4] ?? 'without classroom';
                    $new_data['school_name'] = $client_data[5];
                    $new_data['graduate'] = $client_data[6] === 'yes' ? true : false;
                    $new_data['teacher'] = $client_data[7] === 'yes' ? true : false;
                    $new_data['title'] = $client_data[8];

                    $clientRepo->update($client->id, $new_data);

                    // Update sub gallery password
                    $subGallery = $client->subgallery;
                    $subGallery['password'] = $client_data[9];
                    $subGallery->save();
                }
            } else {
                throw new \Exception('You have no 10 columns in your CSV');
            }
        }
    }
}
