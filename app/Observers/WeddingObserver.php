<?php

namespace App\Observers;

use App\Models\Wedding;
use Illuminate\Support\Facades\Storage;

class WeddingObserver
{
    public function updated(Wedding $wedding): void
    {
        $disk = Storage::disk('public');

        // Handle OG Image Replacement
        if ($wedding->wasChanged('og_image_path')) {
            $oldOgImage = $wedding->getOriginal('og_image_path');

            if ($oldOgImage && $disk->exists($oldOgImage)) {
                $disk->delete($oldOgImage);
            }
        }

        // Handle Background Image Replacement
        if ($wedding->wasChanged('bg_image_path')) {
            $oldBgImage = $wedding->getOriginal('bg_image_path');

            if ($oldBgImage && $disk->exists($oldBgImage)) {
                $disk->delete($oldBgImage);
            }
        }
    }


    public function deleted(Wedding $wedding): void
    {
        $disk = Storage::disk('public');

        if ($wedding->og_image_path && $disk->exists($wedding->og_image_path)) {
            $disk->delete($wedding->og_image_path);
        }

        if ($wedding->bg_image_path && $disk->exists($wedding->bg_image_path)) {
            $disk->delete($wedding->bg_image_path);
        }
    }
}
