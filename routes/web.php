<?php

use Illuminate\Support\Facades\Route;
use Uspdev\Workflow\Http\Controllers\WorkflowBackupController;

Route::group(['prefix' => config('uspdev-workflow.prefix'), 'middleware' => ['web']], function () {

    Route::get('/backups', [WorkflowBackupController::class, 'backups_index'])->name('workflows.backups-idx');
    Route::get('/backups/gen-backups', [WorkflowBackupController::class, 'bckp_gen_all'])->name('workflows.gen-all-backups');
    Route::get('/backups/{workflowDefinition}/gen-backup', [WorkflowBackupController::class, 'def_bckp_gen'])->name('workflows.gen-backup');
    Route::get('/backups/{workflowDefinition}/list',[WorkflowBackupController::class, 'def_bckp_list'])->name('workflows.def-backup-list');
});