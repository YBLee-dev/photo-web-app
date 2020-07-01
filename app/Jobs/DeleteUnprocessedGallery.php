<?php

namespace App\Jobs;

use App\Core\StorageManager;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteUnprocessedGallery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $galleryPath;

    /**
     * Create a new job instance.
     *
     * @param string $galleryPath
     */
    public function __construct(string $galleryPath)
    {
        $this->galleryPath = $galleryPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $storageManager =  new StorageManager();
        $storageManager->deleteFtpUploadsDir($this->galleryPath);
    }
}
