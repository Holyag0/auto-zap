<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelSenhaController;
use App\Http\Controllers\AssociadoSenhaController;
use App\Http\Controllers\OperadorSenhaController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas Públicas - Painel de Senhas
Route::prefix('painel')->name('painel.')->group(function () {
    Route::get('/', [PainelSenhaController::class, 'index'])->name('index');
    Route::get('/{setor}', [PainelSenhaController::class, 'setor'])->name('setor');
    Route::get('/{setor}/stream', [PainelSenhaController::class, 'stream'])->name('stream');
    Route::get('/{setor}/dados', [PainelSenhaController::class, 'dados'])->name('dados');
});

// Rotas Públicas - Associado (Autoatendimento)
Route::prefix('senha')->name('senha.')->group(function () {
    Route::get('/{setor}/qrcode', [AssociadoSenhaController::class, 'qrcode'])->name('qrcode');
    Route::get('/{setor}/criar', [AssociadoSenhaController::class, 'formulario'])->name('create');
    Route::get('/{setor}', [AssociadoSenhaController::class, 'formulario'])->name('formulario');
    Route::post('/{setor}', [AssociadoSenhaController::class, 'store'])->name('store');
    Route::get('/comprovante/{senha}', [AssociadoSenhaController::class, 'comprovante'])->name('comprovante');
});

// Rotas Protegidas - Operador (Controle de Atendimento) - REQUER AUTENTICAÇÃO
Route::prefix('operador')->name('operador.')->middleware(['auth'])->group(function () {
    Route::get('/', [OperadorSenhaController::class, 'index'])->name('index');
    Route::get('/{setor}', [OperadorSenhaController::class, 'painel'])->name('painel');
    Route::get('/{setor}/dados', [OperadorSenhaController::class, 'dados'])->name('dados');
    Route::post('/{setor}/chamar-proxima', [OperadorSenhaController::class, 'chamarProxima'])->name('chamar-proxima');
    Route::post('/{setor}/atender-atual', [OperadorSenhaController::class, 'atenderAtual'])->name('atender-atual');
    Route::post('/senha/{senha}/cancelar', [OperadorSenhaController::class, 'cancelar'])->name('cancelar');
});
