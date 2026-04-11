<div>
  <a href="{{ route('form-definitions.def-backup-restore', ['formDefinition' => $formDefinition,'created_time'=> $created_time]) }}" class="btn btn-sm btn-success ml-2" onclick="return confirm('Tem certeza de que quer restaurar o backup de {{ $formDefinition['name'] }} criado em {{ $created_time }} ? ')">
    Restaurar
  </a>
</div>