<?php
// app/Repositories/SeoRepository.php

namespace App\Repositories;

use App\Models\Seo;
use App\Helpers\FileHelper;

class SeoRepository
{
    public function create(array $data): Seo
    {
        if (isset($data['og_image'])) {
            $data['og_image'] = FileHelper::uploadImage($data['og_image'], 'uploads/seo');
        }

        return Seo::create($data);
    }

    public function update(Seo $seo, array $data): Seo
    {
        if (isset($data['og_image'])) {
            $data['og_image'] = FileHelper::uploadImage($data['og_image'], 'uploads/seo', $seo->og_image);
        }

        $seo->update($data);
        return $seo;
    }

    public function delete(Seo $seo): void
    {
        if ($seo->og_image) {
            FileHelper::deleteFile($seo->og_image);
        }

        $seo->delete();
    }
}
