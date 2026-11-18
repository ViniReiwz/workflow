<?php

use Illuminate\Support\Facades\Route;
use Uspdev\Workflow\Http\Controllers\WorkflowBackupController;
use Uspdev\Workflow\Http\Controllers\WorkflowController;

Route::group(['prefix' => config('uspdev-workflow.prefix'), 'middleware' => ['web']], function () {

    // ---------- Definições ----------
    Route::controller(WorkflowController::class)->group(function () {

        Route::get('/createdefinition', 'createDefinition')->name('workflows.create-definition');
        Route::post('/createdefinition', 'storeDefinition')->name('workflows.store-definition');
        Route::get('/listdefinitions', 'listDefinitions')->name('workflows.list-definitions');
        Route::post('/updatedefinition', 'updateDefinition')->name('workflows.updateDefinition');
        Route::get('/editdefinition/{definitionName}/{version}', 'editDefinition')->name('workflows.editDefinition');
        Route::get('/exportdefinition/{definitionName}', 'exportDefinition')->name('workflows.exportDefinition');

        Route::prefix('definition')->group(function () {
            Route::get('/{definitionName}/{version}', 'showDefinition')->name('workflows.showDefinition');
            Route::delete('/remove/{definitionName}/{version}', 'destroyDefinition')->name('workflows.destroyDefinition');
            Route::get('/publish/{definitionName}/{version}', 'publishDefinition')->name('workflows.publishDefinition');
            Route::get('/draft/{definitionName}/{version}', 'draftDefinition')->name('workflows.draftDefinition');
        });

        Route::put('/listdefinitions/setuser', 'mngUserRole')->name('workflows.setuser');
    });

    // ---------- Objetos ----------
    Route::controller(WorkflowController::class)->group(function () {
        Route::get('/viewcreateobject', 'viewCreateObject')->name('workflows.viewCreateObject');
        Route::get('/createobject/{definitionName}/{version}', 'createObject')->name('workflows.createObject');
        Route::post('/createobject/{definitionName}', 'submitForm');
        Route::get('/showuserobjects', 'showUserObjects')->name('workflows.show-user-objects');
        Route::post('/apply-transition/{id}', 'applyTransition')->name('workflows.applyTransition');
        Route::delete('/delete-object/{object}', 'deleteObject')->name('workflows.delete-object');
        Route::get('/atendimentos', 'atendimentos')->name('workflows.atendimentos');

        Route::prefix('object')->group(function () {
            Route::get('/{id}', 'showObject')->name('workflows.showObject');
            Route::post('/{id}', 'applyTransition')->name('workflows.showObject');
            Route::get('/{id}/form/{transition}', 'showForm')->name('workflows.showForm');
        });
    });

    // ---------- Backups ----------
    Route::controller(WorkflowBackupController::class)
        ->prefix('backups')
        ->group(function () {
            Route::get('/', 'backups_index')->name('workflows.backups-idx');
            Route::get('/gen-backups', 'bckp_gen_all')->name('workflows.gen-all-backups');
            Route::get('/remove-all', 'remove_all_bckps')->name('workflows.rmv-bckps');

            Route::prefix('{workflowDefinition}')->group(function () {
                Route::get('/gen-backup', 'def_bckp_gen')->name('workflows.gen-backup');
                Route::get('/list', 'def_bckp_list')->name('workflows.def-backup-list');
                Route::get('/remove/{created_time}', 'remove_bckp')->name('workflows.def-remove-bckp');
                Route::get('/remove-all', 'remove_def_bckps')->name('workflows.def-rmv-bckps');
                Route::get('/restore/{created_time}', 'restore_backup')->name('workflows.restore-bckp');
            });
        });
});