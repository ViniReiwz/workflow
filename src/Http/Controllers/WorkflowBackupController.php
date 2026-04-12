<?php

namespace Uspdev\Workflow\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Uspdev\Workflow\Models\WorkflowDefinition;
use File;

class WorkflowBackupController extends Controller
{

    public function backups_index()
    {
        $workflowDefinitions = WorkflowDefinition::all();

        return view('uspdev-workflow::show.list-bckps', ['workflowDefinitions' => $workflowDefinitions, 'activeTab' => 'backup']);
    }

    public function def_bckp_gen(WorkflowDefinition $workflowDefinition)
    {
        $file_dir = config('uspdev-workflow.storagePath');
        if(!is_dir($file_dir))
        {
            mkdir($file_dir,0777);
        }

        $file_path = $file_dir . '/' . $workflowDefinition['name'] . '@' . now()->format('d-m-Y_H:i:s') . '.json';

        try
        {
            $json_file = fopen($file_path,'w');
        }
        catch(Exception $e)
        {
            print("Erro ao abrir arquivo: " . $e);
            return;
        }

        $json_encoded = json_encode($workflowDefinition->definition,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        fwrite($json_file,$json_encoded);

        fclose($json_file);

        return redirect()->back()->with('alert-success','Backup de ' . $workflowDefinition->name . ' gerado com sucesso.');
    }

    public function bckp_gen_all()
    {
        $wrkflw_defs = WorkflowDefinition::all();
        foreach($wrkflw_defs as $wrkflow_def)
        {
            $this->def_bckp_gen($wrkflow_def);
        }

        return redirect()->back()->with('alert-success','Backups de todas as definições gerados com sucesso.');
    }

    public function def_bckp_list(WorkflowDefinition $workflowDefinition)
    {
        $file_dir = config('uspdev-workflow.storagePath');

        $files = scandir($file_dir);
        $files = array_filter($files, function($filename) use ($workflowDefinition)
        {
            return str_contains($filename,$workflowDefinition->name);
        });

        $time_data = [];

        foreach($files as $filename)
        {
            $created_time = explode('@',$filename)[1];
            $created_time = explode('.',$created_time)[0];
            
            $last_mod_time = date('d-m-Y_H:i:s', filemtime($file_dir .'/'. $filename));

            // Grava no formato: tempo_criado => tempo_ultima_mod
            $time_data[$created_time] = $last_mod_time;
        }

        return view('uspdev-workflow::show.list-def-bckps', ['workflowDefinition' => $workflowDefinition, 'time_data' => $time_data]);
    }

    public function remove_bckp(WorkflowDefinition $workflowDefinition, string $created_time)
    {
        $created_time = str_replace(' - ','_',$created_time);
        $created_time = str_replace('/','-',$created_time);

        // Remonta o nome do arquivo
        $filename = $workflowDefinition->name . '@' . $created_time . '.json';
        
        // Remonta o caminho completo do arquivo
        $filepath = config('uspdev-workflow.storagePath') . '/' . $filename;

        // Caso o arquivo exista no caminho remontado anteriormente, o remove
        if(File::exists($filepath))
        {    
            File::delete($filepath);
            return redirect()->back()->with('alert-warning','Backup ' . $filename . ' removido com sucesso.' );
        }

        // Caso contrário, exibe uma mensagem de erro.
        else
        {
            return redirect()->back()->with('alert-danger', 'Impossível remover ' . $filename .' => arquivo não existe.');
        }
    }

    public function remove_def_bckps(WorkflowDefinition $workflowDefinition)
    {
        // Recupera o diretório em que os backups são salvos
        $file_dir = config('uspdev-workflow.storagePath');

        // Filtra os arquivos pelos nomes que contém o nome da definição
        $files = array_filter(scandir($file_dir),function($filename) use ($workflowDefinition){return str_contains($filename,$workflowDefinition->name);});

        // Percorre todos os arquivos
        foreach($files as $filename)
        {
            // Reconstrói o caminho dos arquivo
            $filepath = $file_dir . '/' . $filename;

            // Verifica a existência e deleta em caso afirmativo
            if(File::exists($filepath));
            {
                File::delete($filepath);
            }
        }

        return redirect()->back()->with('alert-warning', 'Backups de ' . $workflowDefinition->name . ' removidos com sucesso.');
    }

    public function remove_all_bckps()
    {
        // Recupera o diretório em que os arquivos são salvos
        $file_dir = config('uspdev-workflow.storagePath');

        // Filtra para obter apenas os arquivos .json(evita '.' e '..', além de possível lixo)
        $files = array_filter(scandir($file_dir), function($file) { return str_contains($file,'.json'); });

        // Percorre todos os arquivos do diretório
        foreach($files as $filename)
        {
            // Reconstrói o caminho do arquivo
            $filepath = $file_dir . '/' . $filename;

            // Verifica se o mesmo existe, e o deleta em caso afirmativo
            if(File::exists($filepath))
            {
                File::delete($filepath);
            }
        }

        return redirect()->back()->with('alert-warning', 'Backups removidos com sucesso.');
    }

    public function restore_backup(WorkflowDefinition $workflowDefinition, string $created_time)
    {
        $file_dir = config('uspdev-workflow.storagePath');
        $filename = $workflowDefinition->name . '@' . $created_time;
        $filepath = $file_dir . '/' . $filename . '.json';

        Artisan::call('workflow:sync', ['--path' => $filepath]);

        return redirect()->back()->with('alert-success','Backup ' . $filename . ' restaurado com sucesso');
    }
}