<?php

namespace Uspdev\Workflow\Console\Commands   ;

use Illuminate\Console\Command;
use Uspdev\Workflow\Services\WorkflowSyncService;

class WorkflowSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:sync {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronizes the workflow backup on the specified path with de database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Pega o arquivo passado na option --path.
        // Caso não esteja definido, pega o diretório padrão de armazenamento em uspdev-workflow.php
        $path = $this->option('path') ?: config('uspdev-workflow.storagePath');
        $this->info('Sicnronizando workflows do caminho: ' . $path);

        // Chama o serviço de sincronizar no caminho especificado
        app(WorkflowSyncService::class)->workflow_sync($path);
    }
}
