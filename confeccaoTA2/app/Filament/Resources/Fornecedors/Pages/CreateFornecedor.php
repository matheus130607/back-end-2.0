<?php

namespace App\Filament\Resources\Fornecedors\Pages;

use App\Filament\Resources\Fornecedors\FornecedorResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Post;
use Filament\Actions\Action;



class CreateFornecedor extends CreateRecord
{
    protected static string $resource = FornecedorResource::class;

    
}
