<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $companies = Company::with('owner')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Companies/Create');
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $company = Company::create([
            ...$request->validated(),
            'owner_id' => $request->user()->id,
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Company created.');
    }

    public function show(Company $company): Response
    {
        $company->load([
            'owner',
            'contacts' => fn ($q) => $q->orderByDesc('created_at')->limit(10),
            'deals' => fn ($q) => $q->orderByDesc('created_at')->limit(10),
            'activities' => fn ($q) => $q->orderByDesc('created_at')->limit(10),
            'tags',
        ]);

        return Inertia::render('Companies/Show', [
            'company' => $company,
        ]);
    }

    public function edit(Company $company): Response
    {
        return Inertia::render('Companies/Edit', [
            'company' => $company,
        ]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return redirect()->route('companies.show', $company)
            ->with('success', 'Company updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted.');
    }
}