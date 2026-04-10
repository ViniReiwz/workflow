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

    public function def_bckp_gen(WorkflowDefinition $wrkflw_def)
    {
        $file_dir = config('workflow.storagePath');
        if(!is_dir($file_dir))
        {
            mkdir($file_dir,0777);
        }

        $file_path = $file_dir . '/' . $wrkflw_def['name'] . '@' . now()->format('d-m-Y_H:i:s') . '.json';

        try
        {
            $json_file = fopen($file_path,'w');
        }
        catch(Exception $e)
        {
            print("Erro ao abrir arquivo: " . $e);
            return;
        }

        $json_encoded = json_encode($wrkflw_def->definition,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        fwrite($json_file,$json_encoded);

        fclose($json_file);
    }

    public function bckp_gen_all()
    {
        $wrkflw_defs = WorkflowDefinition::all();
        foreach($wrkflw_defs as $wrkflow_def)
        {
            $this->def_bckp_gen($wrkflow_def);
        }
    }
}