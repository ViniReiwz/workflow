<a href="{{ route('form-definitions.def-backup-remove-all',['formDefinition' => $formDefinition]) }}" class="btn btn-sm btn-danger ml-2" onclick="return confirm('Tem certeza de que quer remover todos os backups de {{ $formDefinition->name }} ?')">
  Remover todos os backups da definição
</a>
