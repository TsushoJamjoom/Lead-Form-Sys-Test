<?php

use App\Livewire\AppointmentManagement\Calendar;
use App\Livewire\Auth\Login;
use App\Livewire\Company\CompanyAdd;
use App\Livewire\Company\CompanyCreate;
use App\Livewire\Company\CompanyEdit;
use App\Livewire\Company\CompanyList;
use App\Livewire\SalesLead\SalesLeadList;
use App\Livewire\Company\CompanyView;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\History\HistoryList;
use App\Livewire\History\HistoryView;
use App\Livewire\Map\Map;
use App\Livewire\SalesLead\SalesLeadHistory;
use App\Livewire\Ticket\TicketList;
use App\Livewire\UserManagement\UserAdd;
use App\Livewire\UserManagement\UserEdit;
use App\Livewire\UserManagement\UserList;
use App\Livewire\UserManagement\UserProfile;
use App\Livewire\UserManagement\UserView;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class)->name('login');

Route::group(['middleware' => ['auth', 'auth.session']], function () {

    // Dashboard
    Route::get('dashboard/', Dashboard::class)->name('dashboard');

    // User Management
    Route::get('/user/add/', UserAdd::class)->name('user-add')->middleware('isPermission:user/create');
    Route::get('/user/edit/{id}/', UserEdit::class)->name('user-edit')->middleware('isPermission:user/edit');
    Route::get('/user/view/{id}/', UserView::class)->name('user-view')->middleware('isPermission:user/view');
    Route::get('/user/list/', UserList::class)->name('user-list')->middleware('isPermission:user/list');

    // Company Management
    // Route::get('/company/add/{id?}', CompanyAdd::class)->name('company-add');
    Route::get('/company/edit/{id}/{eventid?}', CompanyAdd::class)->name('company-edit')->middleware('isPermission:customer/edit');
    Route::get('/company/view/{id}/', CompanyView::class)->name('company-view')->middleware('isPermission:customer/view');
    Route::get('/company/list/', CompanyList::class)->name('company-list')->middleware('isPermission:customer/list');
    Route::get('/company/create/{id?}', CompanyCreate::class)->name('company-create')->middleware('isPermission:customer/create');

    //Sales Lead
    Route::get('/sales-lead/list', SalesLeadList::class)->name('sales-lead-list')->middleware('isPermission:sales_lead/list');

    // Appointment Management
    Route::get('calendar/', Calendar::class)->name('calendar')->middleware('isPermission:calendar/list');

    // Ticket
    Route::get('/ticket/list/{status?}', TicketList::class)->name('ticket-list')->middleware('isPermission:ticket/list');

    // History
    Route::get('/history/list', HistoryList::class)->name('history-list')->middleware('isPermission:history/list');
    Route::get('/history/view/{id}', HistoryView::class)->name('history-view')->middleware('isPermission:history/view');

    // Map
    Route::get('map', Map::class)->name('map')->middleware('isPermission:map/list');

    //Profile
    Route::get('/profile', UserProfile::class)->name('profile');

    //Sales History
    Route::get('/sales-lead-history', SalesLeadHistory::class)->name('sales-lead-history');
});
