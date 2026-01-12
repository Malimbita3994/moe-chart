<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SecureImageService
{
    /**
     * Allowed MIME types for profile pictures
     */
    const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/webp',
    ];

    /**
     * Magic bytes (file signatures) for image validation
     */
    const MAGIC_BYTES = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'image/webp' => ["RIFF", "WEBP"],
    ];

    /**
     * Maximum file size in bytes (2MB)
     */
    const MAX_FILE_SIZE = 2048 * 1024;

    /**
     * Maximum image dimensions
     */
    const MAX_WIDTH = 1024;
    const MAX_HEIGHT = 1024;

    /**
     * Process and store a secure profile picture
     * 
     * @param UploadedFile $file
     * @param int $userId
     * @return string|null Filename of stored image
     * @throws \Exception
     */
    public static function processAndStore(UploadedFile $file, int $userId): ?string
    {
        try {
            // 1. Validate MIME type
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
                Log::warning('Profile picture upload rejected: Invalid MIME type', [
                    'user_id' => $userId,
                    'mime_type' => $mimeType,
                    'ip' => request()->ip(),
                ]);
                throw new \Exception('Invalid file type. Only JPEG, PNG, and WEBP images are allowed.');
            }

            // 2. Validate file size
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                Log::warning('Profile picture upload rejected: File too large', [
                    'user_id' => $userId,
                    'file_size' => $file->getSize(),
                    'ip' => request()->ip(),
                ]);
                throw new \Exception('File size exceeds 2MB limit.');
            }

            // 3. Verify file signature (magic bytes)
            $fileContent = file_get_contents($file->getRealPath());
            if (!self::verifyFileSignature($fileContent, $mimeType)) {
                Log::warning('Profile picture upload rejected: Invalid file signature', [
                    'user_id' => $userId,
                    'mime_type' => $mimeType,
                    'ip' => request()->ip(),
                ]);
                throw new \Exception('Invalid image file. File signature verification failed.');
            }

            // 4. Verify it's actually an image using getimagesize
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                Log::warning('Profile picture upload rejected: Not a valid image', [
                    'user_id' => $userId,
                    'ip' => request()->ip(),
                ]);
                throw new \Exception('Invalid image file.');
            }

            // 5. Re-encode image using Intervention Image (destroys embedded payloads)
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // Strip EXIF metadata and re-encode
            $image->strip();

            // Resize if too large (maintain aspect ratio)
            if ($image->width() > self::MAX_WIDTH || $image->height() > self::MAX_HEIGHT) {
                $image->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);
            }

            // 6. Generate secure UUID filename
            $filename = Str::uuid() . '.jpg'; // Always save as JPG after re-encoding

            // 7. Store the re-encoded image
            $path = storage_path('app/public/profiles/' . $filename);
            $directory = dirname($path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save as JPEG with quality 85 (strips all metadata)
            $image->toJpeg(85)->save($path);

            // Log successful upload
            Log::info('Profile picture uploaded successfully', [
                'user_id' => $userId,
                'filename' => $filename,
                'original_size' => $file->getSize(),
                'final_size' => filesize($path),
                'ip' => request()->ip(),
            ]);

            return $filename;

        } catch (\Exception $e) {
            Log::error('Profile picture upload failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify file signature (magic bytes) to prevent file type spoofing
     * 
     * @param string $fileContent
     * @param string $mimeType
     * @return bool
     */
    protected static function verifyFileSignature(string $fileContent, string $mimeType): bool
    {
        if (!isset(self::MAGIC_BYTES[$mimeType])) {
            return false;
        }

        $signatures = self::MAGIC_BYTES[$mimeType];
        $fileStart = substr($fileContent, 0, 12);

        foreach ($signatures as $signature) {
            if (strpos($fileStart, $signature) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Delete a profile picture file
     * 
     * @param string $filename
     * @return bool
     */
    public static function delete(string $filename): bool
    {
        $path = storage_path('app/public/profiles/' . $filename);
        
        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }
}
