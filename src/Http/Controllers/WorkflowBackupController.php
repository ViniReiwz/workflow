<?php

namespace Uspdev\Workflow\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Uspdev\Workflow\Models\WorkflowDefinition;

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
}