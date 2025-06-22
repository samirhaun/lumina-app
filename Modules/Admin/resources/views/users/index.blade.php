@extends('admin::layouts.layout')

@section('title', 'Gestão de Usuários')

@section('content')
<div id="usersContent">
  <style>
    :root {
      --terracota: #A0522D;
      --beige:     #EDE8E0;
      --white:     #ffffff;
    }

    /* Card header terracota */
    #usersContent .card-terracotta > .card-header {
      background-color: var(--terracota);
      color: var(--white);
      border-bottom: none;
    }

    /* Botão primário terracota */
    #usersContent .btn-primary {
      background-color: var(--terracota) !important;
      border-color: var(--terracota) !important;
      color: var(--white) !important;
    }
  </style>

  <div class="card card-terracotta">
<div class="card-header d-flex justify-content-end   align-items-center">
  <div class="card-tools">
    <button type="button"
            class="btn btn-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#userModal">
      <i class="fas fa-plus"></i> Novo Usuário
    </button>
  </div>
</div>
    </div>

    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome de Usuário</th>
            <th>Nome Completo</th>
            <th>E-mail</th>
            <th style="width:150px">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->username }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                <button type="button"
                        class="btn btn-primary btn-sm edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#userModal"
                        data-id="{{ $user->id }}"
                        data-username="{{ $user->username }}"
                        data-name="{{ $user->name }}"
                        data-email="{{ $user->email }}"
                        data-url="{{ route('admin.users.update', $user->id) }}">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button"
                        class="btn btn-danger btn-sm delete-btn"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        data-url="{{ route('admin.users.destroy', $user->id) }}">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer clearfix">
      {{ $users->links() }}
    </div>
  </div>

  {{-- Modal de cadastro/edição permanece igual --}}
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // --- ABRIR MODAL (CRIAR VS EDITAR) ---
            $('#userModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                $('#userForm')[0].reset();
                modal.find('input').removeClass('is-invalid');
                modal.find('.invalid-feedback').remove();

                if (button.hasClass('edit-btn')) {
                    modal.find('.modal-title').text('Editar Usuário');
                    modal.find('#userFormSubmitButton').text('Atualizar').removeClass('btn-success')
                        .addClass('btn-primary');
                    $('#password-help-text').show();
                    $('#password').prop('required', false);

                    modal.find('#user_id').val(button.data('id'));
                    modal.find('#name').val(button.data('name'));
                    modal.find('#username').val(button.data('username')); // LÓGICA ADICIONADA
                    modal.find('#email').val(button.data('email'));

                    $('#userForm').data('action', button.data('url'));
                    $('#userForm').data('method', 'PUT');

                } else {
                    modal.find('.modal-title').text('Novo Usuário');
                    modal.find('#userFormSubmitButton').text('Salvar').removeClass('btn-primary').addClass(
                        'btn-success');
                    $('#password-help-text').hide();
                    $('#password').prop('required', true);

                    $('#userForm').data('action', '{{ route('admin.users.store') }}');
                    $('#userForm').data('method', 'POST');
                }
            });


            // --- SUBMISSÃO DO FORMULÁRIO (CRIAR E EDITAR) VIA AJAX ---
            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.data('action');
                var method = form.data('method');
                var formData = form.serialize();

                Swal.fire({
                    title: 'Salvando...',
                    text: 'Por favor, aguarde.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#userModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: response.success,
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.close();

                        if (xhr.status === 419) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Sessão Expirada',
                                text: 'Sua página ficou inativa por muito tempo. Por favor, recarregue a página e tente novamente.',
                            }).then(() => {
                                location.reload();
                            });
                        } else if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            form.find('.is-invalid').removeClass('is-invalid');
                            form.find('.invalid-feedback').remove();

                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid')
                                    .after('<div class="invalid-feedback">' + value[0] +
                                        '</div>');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro Inesperado',
                                text: 'Ocorreu um erro no servidor. Verifique o console para mais detalhes.',
                            });
                        }
                    }
                });
            });

            // --- EXCLUSÃO COM CONFIRMAÇÃO SWAL ---
            $('.delete-btn').on('click', function() {
                var button = $(this);
                var userName = button.data('name');
                var url = button.data('url');
                var csrfToken = $('input[name="_token"]').val();

                Swal.fire({
                    title: 'Você tem certeza?',
                    html: "Você deseja excluir o usuário <b>" + userName +
                        "</b>?<br><small>Esta ação não pode ser desfeita.</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                '_token': csrfToken
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Excluído!',
                                    response.success,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                if (xhr.status === 419) {
                                    Swal.fire('Sessão Expirada!',
                                        'Recarregue a página e tente novamente.',
                                        'error').then(() => location.reload());
                                } else {
                                    Swal.fire('Erro!',
                                        'Não foi possível excluir o usuário.',
                                        'error');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
