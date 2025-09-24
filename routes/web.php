<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



// Front Office Routes
Route::get('/', function () {
    return view('FrontOffice.Accueil');
})->name('accueil');
Route::get('/livres', function () {
    return view('FrontOffice.Livres.LivrePage');
})->name('livres');

Route::get('/articles', function () {
    return view('FrontOffice.Articles.ArticlePage');
})->name('articles');

// ========================
// 🔒 Routes du Back Office
// ========================
Route::middleware(['auth'])->group(function () {

    // ========================
    // 🔒 Routes réservées ADMIN uniquement
    // ========================
    Route::middleware(['role:admin'])->group(function () {
        // DashboardAdmin
        Route::get('/dashboardAdmin', fn() => view('BackOffice.dashboardAdmin'))->name('dashboardAdmin');

        // Blog Management
        Route::get('/AjouterBlog', fn() => view('BackOffice.blog.ajouterBlog'))->name('AjouterBlog');
        Route::get('/listeBlog', fn() => view('BackOffice.blog.listeBlog'))->name('listeBlog');

        // Magasin Management
        Route::get('/AjouterMagasin', fn() => view('BackOffice.magasin.ajouterMagasin'))->name('AjouterMagasin');
        Route::get('/listeMagasin', fn() => view('BackOffice.magasin.listeMagasin'))->name('listeMagasin');

        // Utilisateur Management
        Route::get('/AjouterUtilisateur', fn() => view('BackOffice.utilisateur.ajouterUtilisateur'))->name('AjouterUtilisateur');
        Route::get('/listeUtilisateur', fn() => view('BackOffice.utilisateur.listeUtilisateur'))->name('listeUtilisateur');
    });

    // ========================
    // 🔒 Routes réservées AUTEUR uniquement
    // ========================
    Route::middleware(['role:auteur'])->group(function () {
        // Dashboard Auteur
        Route::get('/dashboardAuteur', fn() => view('BackOffice.dashboardAuteur'))->name('dashboardAuteur');
    });

    // ========================
    // 🔒 Routes accessibles ADMIN + AUTEUR
    // ========================
    Route::middleware(['role:admin,auteur'])->group(function () {
        // Livre Management
        Route::get('/AjouterLivre', fn() => view('BackOffice.livre.ajouterLivre'))->name('AjouterLivre');
        Route::get('/listeLivre', fn() => view('BackOffice.livre.listeLivre'))->name('listeLivre');

        // Categorie Management
        Route::get('/AjouterCategorie', fn() => view('BackOffice.categorieLivre.ajouterCategorie'))->name('AjouterCategorie');
        Route::get('/listeCategorie', fn() => view('BackOffice.categorieLivre.listeCategorie'))->name('listeCategorie');
    });
});

Route::get('/admin', function () {
    return view('dashboard');
})->middleware(['auth', 'role:admin']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
