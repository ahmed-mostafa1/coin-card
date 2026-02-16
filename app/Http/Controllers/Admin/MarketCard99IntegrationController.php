<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MarketCard99IntegrationController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.index')
            ->with('error', 'MarketCard99 integration has been disabled and removed.');
    }

    public function syncCatalog(): RedirectResponse
    {
        return $this->index();
    }

    public function syncOrderStatuses(): RedirectResponse
    {
        return $this->index();
    }
}
