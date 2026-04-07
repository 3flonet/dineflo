<?php

namespace App\Filament\Admin\Pages\OrderResource\Pages;

use Filament\Pages\Page;

class ViewOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.order-resource.pages.view-order';
}
