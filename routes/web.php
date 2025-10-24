<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

// Livewire pages
use App\Livewire\Dashboard\Index as DashboardIndex;

use App\Livewire\People\Index as PeopleIndex;
use App\Livewire\People\Form  as PeopleForm;

use App\Livewire\Members\Index as MembersIndex;
use App\Livewire\Members\Form  as MembersForm;

use App\Livewire\Executives\Index as ExecutivesIndex;
use App\Livewire\Executives\Form  as ExecutivesForm;

use App\Livewire\Stakeholders\Index as StakeholdersIndex;
use App\Livewire\Stakeholders\Form  as StakeholdersForm;

use App\Livewire\Initiatives\Index as InitiativesIndex;
use App\Livewire\Initiatives\Form  as InitiativesForm;

use App\Livewire\Opportunities\Index as OpportunitiesIndex;
use App\Livewire\Opportunities\Form  as OpportunitiesForm;

use App\Livewire\Organizations\Index as OrganizationsIndex;
use App\Livewire\Organizations\Form  as OrganizationsForm;

use App\Livewire\Imports\PeopleImport        as ImportPeople;
use App\Livewire\Imports\MembersImport       as ImportMembers;
use App\Livewire\Imports\StakeholdersImport  as ImportStakeholders;
use App\Livewire\Imports\OpportunitiesImport as ImportOpportunities;

// Public (or guest) landing
Route::get('/', function () {
    return view('welcome');
});
// 🚧 TEMP: move people routes outside middleware to remove auth/verified from the equation
Route::get('/people', \App\Livewire\People\Index::class)->name('people.index');
Route::get('/people/create', \App\Livewire\People\Form::class)->name('people.create');
Route::get('/people/{person}/edit', \App\Livewire\People\Form::class)->whereNumber('person')->name('people.edit');

// Authenticated area
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /* ----------------------------- Dashboard ----------------------------- */
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    /* -------------------------------- People ----------------------------- */
   /* -------------------------------- People ----------------------------- */
Route::get('/people', PeopleIndex::class)->name('people.index');
Route::get('/people/create', PeopleForm::class)->name('people.create');           // <-- fixed
Route::get('/people/{person}/edit', PeopleForm::class)
    ->whereNumber('person')                                                       // safer
    ->name('people.edit');


    /* --------------------------- Member Registrations -------------------- */
    // Scoped under person
    //Route::get('/people/{person}/members', MembersIndex::class)->name('members.index');
    //Route::get('/people/{person}/members/create', MembersForm::class)->name('members.create');
   // Route::get('/people/{person}/members/{memberRegistration}/edit', MembersForm::class)->name('members.edit');

    /* ------------------------------ Executives --------------------------- */
    Route::get('/executives', ExecutivesIndex::class)->name('executives.index');
    Route::get('/executives/create', ExecutivesForm::class)->name('executives.create');
    Route::get('/executives/{executiveAssignment}/edit', ExecutivesForm::class)->name('executives.edit');

    /* ------------------------------ Stakeholders ------------------------- */
    Route::get('/stakeholders', StakeholdersIndex::class)->name('stakeholders.index');
    Route::get('/stakeholders/create', StakeholdersForm::class)->name('stakeholders.create');
    Route::get('/stakeholders/{stakeholder}/edit', StakeholdersForm::class)->name('stakeholders.edit');

    /* ------------------------------ Initiatives -------------------------- */
    Route::get('/initiatives', InitiativesIndex::class)->name('initiatives.index');
    Route::get('/initiatives/create', InitiativesForm::class)->name('initiatives.create');
    Route::get('/initiatives/{initiative}/edit', InitiativesForm::class)->name('initiatives.edit');

    /* ------------------------------ Opportunities ------------------------ */
    Route::get('/opportunities', OpportunitiesIndex::class)->name('opportunities.index');
    Route::get('/opportunities/create', OpportunitiesForm::class)->name('opportunities.create');
    Route::get('/opportunities/{opportunity}/edit', OpportunitiesForm::class)->name('opportunities.edit');

    /* ------------------------------ Organizations ------------------------ */
    Route::get('/organizations', OrganizationsIndex::class)->name('organizations.index');
    Route::get('/organizations/create', OrganizationsForm::class)->name('organizations.create');
    Route::get('/organizations/{organization}/edit', OrganizationsForm::class)->name('organizations.edit');

    /* -------------------------------- Imports ---------------------------- */
    Route::get('/imports/people',        ImportPeople::class)->name('imports.people');
    Route::get('/imports/members',       ImportMembers::class)->name('imports.members');
    Route::get('/imports/stakeholders',  ImportStakeholders::class)->name('imports.stakeholders');
    Route::get('/imports/opportunities', ImportOpportunities::class)->name('imports.opportunities');

    /* -------------------------------- Exports ---------------------------- */
    Route::get('/exports/people',        [ExportController::class, 'people'])->name('export.people');
    Route::get('/exports/stakeholders',  [ExportController::class, 'stakeholders'])->name('export.stakeholders');
    Route::get('/exports/opportunities', [ExportController::class, 'opportunities'])->name('export.opportunities');
    Route::get('/exports/initiatives',   [ExportController::class, 'initiatives'])->name('export.initiatives');
    Route::get('/exports/organizations', [ExportController::class, 'organizations'])->name('export.organizations');
    Route::get('/exports/executives',    [ExportController::class, 'executives'])->name('export.executives');
});
