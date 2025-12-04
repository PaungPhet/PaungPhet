<?php

namespace App\Filament\Resources\PreweddingImageResource\Pages;

use App\Filament\Resources\PreweddingImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPreweddingImages extends ListRecords
{
    protected static string $resource = PreweddingImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data) {
                    $data['wedding_id'] = auth()->user()->wedding->id;
                    return $data;
                }),
        ];
    }
}
