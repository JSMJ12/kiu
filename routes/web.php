<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardDocenteController;
use App\Http\Controllers\DashboardSecretarioController;
use App\Http\Controllers\DashboardSecretarioEpsuController;
use App\Http\Controllers\DashboardAlumnoController;
use App\Http\Controllers\DashboardPostulanteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\ParaleloController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\SecretarioController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MaestriaController;
use App\Http\Controllers\AsignaturaDocenteController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\PeriodoAcademicoController;
use App\Http\Controllers\CohorteController;
use App\Http\Controllers\CohorteDocenteController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\NotasAsignaturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PerfilAlumnoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

route::get('/', function () {    return redirect()->route('login');})->middleware('guest');

//Redireccionador
Route::get('/inicio', [InicioController::class, 'redireccionarDashboard'])->name('inicio');

//DASHBOARD
Route::get('/dashboard/admin', [DashboardAdminController::class, 'index'])->middleware('can:dashboard_admin')->name('dashboard_admin');
Route::get('/dashboard/docente', [DashboardDocenteController::class, 'index'])->middleware('can:dashboard_docente')->name('dashboard_docente');
Route::get('/dashboard/secretario', [DashboardSecretarioController::class, 'index'])->middleware('can:dashboard_secretario')->name('dashboard_secretario');
Route::get('/dashboard/secretario/epsu', [DashboardSecretarioEpsuController::class, 'index'])->middleware('can:epsu_dashboard')->name('dashboard_secretario_epsu');
Route::get('/dashboard/alumno', [DashboardAlumnoController::class, 'index'])->middleware('can:dashboard_alumno')->name('dashboard_alumno');
Route::get('/dashboard/postulante', [DashboardPostulanteController::class, 'index'])->middleware('can:dashboard_postulante')->name('dashboard_postulante');

//USUARIOS
Route::resource('usuarios', UsuarioController::class)->middleware(['can:dashboard_admin']);
Route::put('/usuarios/{usuario}/disable', [UsuarioController::class, 'disable'])->name('usuarios.disable')->middleware('can:dashboard_admin');
Route::put('/usuarios/{usuario}/enable', [UsuarioController::class, 'enable'])->name('usuarios.enable')->middleware('can:dashboard_admin');

//CRUD DOCENTES
Route::resource('docentes', DocenteController::class)->middleware(['can:dashboard_secretario']);

//CRUD SECRETARIOS
Route::resource('secretarios', SecretarioController::class)->middleware(['can:dashboard_admin']);

//CRUD SECCIONES
Route::resource('secciones', SeccionController::class)->middleware(['can:dashboard_admin']);

//DOCNTES ASIGNATURAS
Route::delete('/docentes/{docente_dni}/asignaturas/{asignatura_id}', [AsignaturaDocenteController::class, 'destroy'])->name('eliminar_asignatura');
Route::get('/asignaturas_docentes/create/{docente_id}', [AsignaturaDocenteController::class, 'create'])->name('asignaturas_docentes.create1')->middleware(['can:dashboard_secretario']);

//MAESTRIAS
Route::resource('maestrias', MaestriaController::class)->middleware(['can:dashboard_admin']);
Route::put('maestrias/{maestria}/disable', [MaestriaController::class, 'disable'])->name('maestrias.disable')->middleware(['can:dashboard_admin']);
Route::put('maestrias/{maestria}/enable', [MaestriaController::class, 'enable'])->name('maestrias.enable')->middleware(['can:dashboard_admin']);

//ASIGNATURAS Y SILABO
Route::resource('asignaturas', AsignaturaController::class)->middleware(['can:dashboard_admin']);
Route::post('/dashboard/docente/update-silabo', [DashboardDocenteController::class, 'updateSilabo'])->name('updateSilabo');

//ALUMNOS
Route::resource('alumnos', AlumnoController::class)->middleware(['can:dashboard_secretario']);

//ASIGNATURA DOCENTES
Route::resource('asignaturas_docentes', AsignaturaDocenteController::class)->middleware(['can:dashboard_secretario']);

//AULAS
Route::resource('aulas', AulaController::class)->middleware(['can:dashboard_secretario']);

//PERIDOS ACADEMICOS
Route::resource('periodos_academicos', PeriodoAcademicoController::class)->middleware(['can:dashboard_secretario']);

//COHORTES
Route::resource('cohortes', CohorteController::class)->middleware(['can:dashboard_secretario']);

//Postulaciones
Route::resource('postulaciones', PostulanteController::class);
Route::post('postulante/store', [DashboardPostulanteController::class, 'store'])->middleware('can:dashboard_postulante')->name('dashboard_postulante.store');
Route::get('postulantes/{dni}/carta-aceptacion', [DashboardPostulanteController::class, 'carta_aceptacionPdf'])->middleware('can:dashboard_postulante')->name('postulantes.carta_aceptacion');
Route::post('/postulantes/{dni}/convertir', [PostulanteController::class, 'convertirEnEstudiante'])->middleware('can:dashboard_secretario')->name('postulantes.convertir');

//COHORTES DOCENTES
Route::get('cohortes_docentes/create/{docente_dni}/{asignatura_id?}', [CohorteDocenteController::class, 'create'])
    ->middleware(['can:dashboard_secretario'])
    ->name('cohortes_docentes.create1');
Route::resource('cohortes_docentes', CohorteDocenteController::class)->middleware(['can:dashboard_secretario']);

//PERMISOS DE CALIFICACION
Route::post('/guardar-cambios', [DocenteController::class, 'guardarCambios'])->name('guardarCambios')->middleware(['can:dashboard_secretario']);

//MENSAJERIA
Route::post('mensajes', [MessageController::class, 'store'])->name('messages.store');
Route::get('mensajes/buzon', [MessageController::class, 'index'])->name('messages.index');
Route::delete('/mensajes/{id_message}', [MessageController::class, 'destroy'])->name('messages.destroy');

//Pagos
Route::middleware(['can:dashboard_secretario_epsu'])->group(function () {
    // Listar todos los pagos (equivalente a index)
    Route::get('/pagos/dashboard', [PagoController::class, 'index'])->name('pagos.index');

    Route::patch('/pagos/{pago}/verificar', [PagoController::class, 'verificar_pago'])->name('pagos.verificar');


    // Mostrar el formulario para editar un pago (equivalente a edit)
    Route::get('/pagos/{pago}/edit', [PagoController::class, 'edit'])->name('pagos.edit');

    // Actualizar un pago existente (equivalente a update)
    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
    Route::patch('/pagos/{pago}', [PagoController::class, 'update']);

    // Eliminar un pago (equivalente a destroy)
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
});

//Matriculas
Route::get('/matriculas/create/{alumno_id}', [MatriculaController::class, 'create'])->middleware('can:dashboard_secretario');
Route::get('/matriculas/create/{alumno_id}/{cohorte_id}', [MatriculaController::class, 'create'])->middleware('can:dashboard_secretario');
Route::resource('matriculas', MatriculaController::class)->middleware('can:dashboard_secretario');

//NOTAS ADMINISTRADOR
Route::get('/notas/create/{alumno_id}', [NotaController::class, 'create'])->middleware('can:dashboard_secretario');
Route::resource('notas', NotaController::class)->middleware('can:dashboard_admin');

//PDFS ASIGNATURAS NOTAS
Route::get('/generar-pdf/{docenteId}/{asignaturaId}/{cohorteId}/{aulaId?}/{paraleloId?}', [NotasAsignaturaController::class, 'show'])
    ->middleware(['can:dashboard_docente'])
    ->name('pdf.notas.asignatura');

//PDFS RECORD ACADEMICO ADMINISTRADOR
Route::resource('record', RecordController::class)->middleware('can:dashboard_secretario');

//CALIFICACIONES DOCENTES
Route::get('/calificaciones/create/{docente_id}/{asignatura_id}/{cohorte_id}', [CalificacionController::class, 'create'])->where('docente_id', '.*')->middleware('can:dashboard_docente')->name('calificaciones.create1');
Route::get('/calificaciones/show/{alumno_id}/{docente_id}/{asignatura_id}/{cohorte_id}', [CalificacionController::class, 'show'])->where('alumno_id', '.*')->where('docente_id', '.*')->middleware('can:dashboard_docente')->name('calificaciones.show1');
Route::get('/calificaciones/edit/{alumno_id}/{docente_id}/{asignatura_id}/{cohorte_id}', [CalificacionController::class, 'edit'])->where('docente_id', '.*')->middleware('can:dashboard_docente')->name('calificaciones.edit1');
Route::resource('calificaciones', CalificacionController::class)->middleware('can:dashboard_docente');

//EXCEL LISTA DE ALUMNOS
Route::get('/exportar-excel/{docenteId}/{asignaturaId}/{cohorteId}/{aulaId?}/{paraleloId?}', [DashboardDocenteController::class, 'exportarExcel'])
    ->name('exportar.excel');

//NOTIFICACIONES
Route::resource('notificaciones', NotificacionesController::class)->only(['index', 'destroy']);
Route::get('/cantidad-notificaciones', [NotificacionesController::class, 'contador']);

// Mostrar el formulario para descuento
Route::get('/descuento', [PagoController::class, 'showDescuentoForm'])
    ->name('pago.descuento.form')
    ->middleware('auth');

// Procesar el descuento
Route::post('/descuento', [PagoController::class, 'processDescuento'])
    ->name('pago.descuento.process')
    ->middleware('auth');

// Guardar un nuevo pago (equivalente a store)
Route::post('/pagos', [PagoController::class, 'store'])
    ->name('pagos.store')
    ->middleware('auth');

//PAGOS ALUMNOS
Route::middleware(['can:dashboard_alumno'])->group(function () {
    Route::get('/pagos/pago/estudiante', [PagoController::class, 'pago'])->name('pagos.pago');
    Route::post('/pagos/elegir-modalidad', [PagoController::class, 'elegirModalidad'])->name('pagos.elegir-modalidad');
});

//Perfiles
Route::post('/perfiles/actualizar', [PerfilController::class, 'actualizar_p'])->name('perfil.actualizar')->middleware('auth');
Route::get('/actualizar_perfil', [PerfilAlumnoController::class, 'edit'])->name('edit_datosAlumnos');
Route::post('/actualizar_perfil/procesar', [PerfilAlumnoController::class, 'update'])->name('update_datosAlumnos');