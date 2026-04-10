@extends('uspdev-forms::layouts.app')

@section('header')
@endsection

@section('content')

<div class="col-2">@include('uspdev-workflow::show.partials.tabs')</div>
<div class="card">
    <div class="card-header h4 card-header-sticky d-flex justify-content-between align-items-center">
      <div>
        <span class="text-danger">USPdev workflow</span> >
        Backups
      </div>
      <div>
        @include('uspdev-forms::partials.ajuda-modal')
      </div>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($workflowDefinitions as $workflowDefinition)
            <tr>
              <td>
                {{ $workflowDefinition->name }}
                <span class="badge badge-warning badge-pill" title="Backups existentes">
                  {{-- 
                    Verifica se o diretório que guarda os formulários existe.
                    Caso exista, exibe o número de backups do formulário existem dentro dele.
                    Senão, mostra 0.
                  --}}
                  {{ is_dir(config('uspdev-forms.forms_storage_dir')) ? count(array_filter(scandir(config('uspdev-forms.forms_storage_dir')), fn($filename) => str_contains($filename,$workflowDefinition->name))) : 0 }}
                </span>
              </td>
              <td>
                {{ $workflowDefinition->description }}
              </td>
              <td class="d-flex justify-content-start">
                @include('uspdev-workflow::show.partials.edit-btn')
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
