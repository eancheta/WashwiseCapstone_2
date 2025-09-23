<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TestCloudinaryUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:test-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test uploading a file to Cloudinary';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = public_path('apple-touch-icon.png'); // a real test image in public folder

        try {
            $result = Cloudinary::upload($filePath, [
                'folder' => 'test_uploads',
            ]);

            $this->info('Upload successful!');
            $this->info('URL: ' . $result->getSecurePath());
        } catch (\Throwable $e) {
            $this->error('Upload failed: ' . $e->getMessage());
        }

        return 0;
    }
}
