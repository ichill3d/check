<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ChecklistController;


Route::get('/', function () {
    return redirect()->route('checklists.index');
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/team/invitations/accept/{token}', [TeamInvitationController::class, 'accept'])->name('team.invitations.accept');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
    Route::patch('/checklists/{id}', [ChecklistController::class, 'update'])->name('checklists.update');
    Route::get('/checklists', [ChecklistController::class, 'index'])->name('checklists.index');
    Route::get('/checklist/{id}', [ChecklistController::class, 'show'])->name('checklists.show');
    Route::delete('/checklist/{id}', [ChecklistController::class, 'removeChecklist'])->name('checklists.destroy');

    Route::post('/checklists/{checklist}/items', [ChecklistController::class, 'addItem'])->name('checklists.items.store');
    Route::post('/checklistitem/{item}/toggle', [ChecklistController::class, 'toggleItem'])->name('checklists.items.toggle');
    Route::delete('/checklistitem/{item}', [ChecklistController::class, 'removeItem'])->name('checklists.items.destroy');


    Route::get('/checklists/create', function () {
        return Inertia::render('Checklists/Create');
    })->name('checklists.create');

    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/create',[TeamController::class, 'create'])->name('teams.create');
        Route::post('/create',[TeamController::class, 'store'])->name('teams.store');
        Route::get('/{id}',[TeamController::class, 'show'])->name('teams.show');
        Route::delete('/{teamId}/member/{memberId}',[TeamController::class, 'removeMember'])->name('teams.members.destroy');

        Route::post('/{id}/invitation', [TeamController::class, 'sendInvitation'])->name('teams.invitations.store');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
        Route::patch('/settings/{id}/toggle', [NotificationController::class, 'settingsToggle'])->name('notifications.settings.toggle');
    });


});

require __DIR__.'/auth.php';
