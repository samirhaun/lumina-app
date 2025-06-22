

<?php $__env->startSection('title', 'Gestão de Usuários'); ?>

<?php $__env->startSection('content'); ?>
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
          <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($user->id); ?></td>
              <td><?php echo e($user->username); ?></td>
              <td><?php echo e($user->name); ?></td>
              <td><?php echo e($user->email); ?></td>
              <td>
                <button type="button"
                        class="btn btn-primary btn-sm edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#userModal"
                        data-id="<?php echo e($user->id); ?>"
                        data-username="<?php echo e($user->username); ?>"
                        data-name="<?php echo e($user->name); ?>"
                        data-email="<?php echo e($user->email); ?>"
                        data-url="<?php echo e(route('admin.users.update', $user->id)); ?>">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button"
                        class="btn btn-danger btn-sm delete-btn"
                        data-id="<?php echo e($user->id); ?>"
                        data-name="<?php echo e($user->name); ?>"
                        data-url="<?php echo e(route('admin.users.destroy', $user->id)); ?>">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card-footer clearfix">
      <?php echo e($users->links()); ?>

    </div>
  </div>

  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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

                    $('#userForm').data('action', '<?php echo e(route('admin.users.store')); ?>');
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/users/index.blade.php ENDPATH**/ ?>