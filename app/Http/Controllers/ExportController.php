<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\ExportService;

class ExportController extends Controller
{
    public function __construct(protected ExportService $exportService)
    {
        // Optionally protect exports (uncomment if you use auth/roles)
        // $this->middleware(['auth']);
        // $this->middleware('can:export-data');
    }

    /** /exports/people */
    public function people(Request $request): StreamedResponse
    {
        return $this->exportService->exportPeople($request->all());
    }

    /** /exports/stakeholders */
    public function stakeholders(Request $request): StreamedResponse
    {
        return $this->exportService->exportStakeholders($request->all());
    }

    /** /exports/opportunities */
    public function opportunities(Request $request): StreamedResponse
    {
        return $this->exportService->exportOpportunities($request->all());
    }

    /** /exports/initiatives */
    public function initiatives(Request $request): StreamedResponse
    {
        return $this->exportService->exportInitiatives($request->all());
    }

    /** /exports/organizations */
    public function organizations(Request $request): StreamedResponse
    {
        return $this->exportService->exportOrganizations($request->all());
    }

    /** /exports/executives */
    public function executives(Request $request): StreamedResponse
    {
        return $this->exportService->exportExecutives($request->all());
    }
}
