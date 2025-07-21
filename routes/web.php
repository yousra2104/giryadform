<?php 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [FormController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-middleware', function () {
    return 'Middleware works';
})->middleware('testadmin');

Route::get('/test-admin-middleware', function () {
    return 'Admin middleware works';
})->middleware('admin');

Route::middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->group(function () {
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/edit/{slug}', [FormController::class, 'edit'])->name('forms.edit');
    Route::put('/forms/{slug}', [FormController::class, 'update'])->name('forms.update');
    Route::get('/forms/{slug}/responses', [FormController::class, 'responses'])->name('forms.responses');
    Route::get('/forms/{slug}/responses/{responseId}', [FormController::class, 'response'])->name('forms.response');
    Route::get('/forms/{slug}/export', [FormController::class, 'exportResponses'])->name('forms.export');
});

Route::get('/forms/{slug}', [FormController::class, 'show'])->name('forms.show');
Route::post('/forms/{slug}/responses', [FormController::class, 'storeResponse'])->name('responses.store');
Route::get('/forms/{slug}/respond', [ResponseController::class, 'create'])->name('responses.create');

require __DIR__.'/auth.php';