<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgencyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AgencyRequestController extends Controller
{
    public function index(): View
    {
        $requests = AgencyRequest::query()
            ->latest()
            ->paginate(15);

        return view('admin.agency-requests.index', compact('requests'));
    }

    public function show(AgencyRequest $agencyRequest): View
    {
        return view('admin.agency-requests.show', compact('agencyRequest'));
    }

    public function destroy(AgencyRequest $agencyRequest): RedirectResponse
    {
        $agencyRequest->delete();

        return redirect()
            ->route('admin.agency-requests.index')
            ->with('status', 'تم حذف الطلب بنجاح.');
    }
}
