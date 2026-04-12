<?php

namespace Uspdev\Workflow\Services;

use Uspdev\Workflow\Models\WorkflowDefinition;

class WorkflowSyncService
{
    private function sync_file(string $filepath)
    {   
        $json_encoded = file_get_contents($filepath);

        $decoded_json = json_decode($json_encoded, true);

        // dd($decoded_json);
        $workflowDefintion = WorkflowDefinition::updateOrCreate(
            ['name' => $decoded_json['name']], 
            [
                'definition' => $decoded_json,
                'description' => $decoded_json['description'],
            ]);
    }

    private function sync_dir(string $dir_path)
    {
        $all_files = scandir($dir_path);

        $most_recent_updt = [];
        foreach($all_files as  $filename)
        {
            if(str_contains($filename,'.json'))
            {
                
                $curr_def = explode('@',$filename)[0];
                $filepath = $dir_path . '/' . $filename;

                if(!array_key_exists($curr_def,$most_recent_updt))
                {
                    $most_recent_updt[$curr_def] = $filepath;
                }

                elseif(filemtime($most_recent_updt[$curr_def]) < filemtime($filepath))
                {
                    $most_recent_updt[$curr_def] = $filepath;
                }
            }
        }

        foreach($most_recent_updt as $to_restore)
        {
            $this->sync_file($to_restore);
        }
    }

    public function workflow_sync(string $path)
    {
        if(is_dir($path))
        {
            $this->sync_dir($path);
        }
        elseif(is_file($path))
        {
            $this->sync_file($path);
        }
        else
        {
            return redirect()->back()->with('alert-danger','O caminho indicado: ' . $path . ' não representa um diretório nem um arquivo existente.');
        }
    }
}