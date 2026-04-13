<?php

namespace Uspdev\Workflow\Services;

use Uspdev\Workflow\Models\WorkflowDefinition;

class WorkflowSyncService
{

    /**
     * Sincroniza um arquivo de backup (Persiste a definição no banco de dados)
     * @param string $filepath
     * @return void
     */
    private function sync_file(string $filepath)
    {   
        // Recupera a definição do workflow salva no backup (em formato .json)
        $json_encoded = file_get_contents($filepath);

        // Decodifica par aum array associativo
        $decoded_json = json_decode($json_encoded, true);

        // Busca a definição eplo nome e atualiza caso exita.
        // Se não, cria uma nova definição com as informações do arquivo
        WorkflowDefinition::updateOrCreate(
            ['name' => $decoded_json['name']], 
            [
                'name' => $decoded_json['name'],
                'description' => $decoded_json['description'],
                'definition' => $decoded_json,
            ]);
    }

    /**
     * Sincroniza todo um diretório de backup
     * @param string $dir_path
     * @return void
     */
    private function sync_dir(string $dir_path)
    {
        // Recupera todos os backups existentes no diretório especificado
        $all_files = scandir($dir_path);

        // Vetor auxiliar para recuperar o backup mais recentemente modificado
        $most_recent_updt = [];

        // Percorre todos os arquivos existentes no diretório
        foreach($all_files as  $filename)
        {
            if(str_contains($filename,'.json'))
            {
                
                // Recupera o nome da definição do backup
                $curr_def = explode('@',$filename)[0];

                // Remonta o caminho completo do arquivo
                $filepath = $dir_path . '/' . $filename;

                // Se ainda não existe a chave, cria e associa ao caminho atual
                if(!array_key_exists($curr_def,$most_recent_updt))
                {
                    $most_recent_updt[$curr_def] = $filepath;
                }

                // Se existe, e o tempo de modificação do elemento contido no vetor for menor que o do arquivo atual
                //(significa que a última modificação do contido foi anterior à do arquivo atual), faz a substituição
                elseif(filemtime($most_recent_updt[$curr_def]) < filemtime($filepath))
                {
                    $most_recent_updt[$curr_def] = $filepath;
                }
            }
        }

        // Para cada elemento dentro do vetor de 'mais recentemente atualizado', sincroniza o arquivo de backup
        foreach($most_recent_updt as $to_restore)
        {
            $this->sync_file($to_restore);
        }
    }

    /**
     * Sincroniza o arquivo/diretório no arquivo passado.
     * @param string $path
     * @return \Illuminate\Http\RedirectResponse
     */
    public function workflow_sync(string $path)
    {   
        // Caso seja um diretório
        if(is_dir($path))
        {
            $this->sync_dir($path);
        }
        
        // Caso seja arquivo
        elseif(is_file($path))
        {
            $this->sync_file($path);
        }

        // Mensagem de erro
        else
        {
            return redirect()->back()->with('alert-danger','O caminho indicado: ' . $path . ' não representa um diretório nem um arquivo existente.');
        }
    }
}