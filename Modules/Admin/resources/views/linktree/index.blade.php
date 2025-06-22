@extends('admin::layouts.layout')

@section('title', 'Gerenciar Linktree')
@section('header', 'Gerenciador de Links')

@push('styles')
    <!-- DataTables Bootstrap5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div id="linktreeContent" class="p-4" style="background-color: #EDE8E0;">
        <style>
            :root {
                --terracota: #A0522D;
                --white: #ffffff;
            }

            /* Cards */
            #linktreeContent .card {
                border: none;
                border-radius: .5rem;
                box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
                margin-bottom: 1.5rem;
            }

            #linktreeContent .card-header {
                background-color: var(--terracota);
                color: var(--white);
                border-bottom: none;
                font-weight: 500;
            }

            #linktreeContent .btn-primary {
                background-color: var(--terracota);
                border-color: var(--terracota);
            }

            /* DataTable styling overrides */
            #linktreeContent .dataTables_wrapper .dataTables_filter input,
            #linktreeContent .dataTables_wrapper .dataTables_length select {
                border-radius: .25rem;
            }

            #linktreeContent table.dataTable {
                border-collapse: separate !important;
                border-spacing: 0 0.5rem;
            }

            #linktreeContent table.dataTable th,
            #linktreeContent table.dataTable td {
                background-color: #fff;
                vertical-align: middle;
                border: none;
            }

            #linktreeContent table.dataTable tbody tr:hover {
                background-color: #f8f2e8;
            }

            /* Icon spacing */
            #linktreeContent #linksTable i {
                margin-right: .5rem;
            }
        </style>

        {{-- 1) Configurações da Página --}}
        <div class="card">
            <div class="card-header">Configurações da Página de Links</div>
            <form id="linktreeSettingsForm">
                @csrf
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Título Principal (ex: @SeuUsuario)</label>
                        <input type="text" name="linktree_handle" class="form-control"
                            value="{{ $settings['linktree_handle'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Descrição / Bio</label>
                        <input type="text" name="linktree_bio" class="form-control"
                            value="{{ $settings['linktree_bio'] ?? '' }}">
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                </div>
            </form>
        </div>

        {{-- 2) Tabela de Links --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button id="addLinkBtn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#linkModal">
                    <i class="fas fa-plus me-1"></i>Novo Link
                </button>
            </div>
            <div class="card-body">
                <table id="linksTable" class="table table-sm table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th style="width:20px;"></th>
                            <th>Título</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th style="width:120px">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        {{-- 3) Modal de CRUD --}}
        <div class="modal fade" id="linkModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-lg shadow-sm">
                    <div class="modal-header" style="background-color: var(--terracota); color: var(--white);">
                        <h5 class="modal-title" id="linkModalLabel">Novo Link</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="linkForm">
                        @csrf
                        <input type="hidden" id="link_id" name="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="title">Título do Botão</label>
                                <input type="text" id="title" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="url">URL de Destino</label>
                                <input type="url" id="url" name="url" class="form-control"
                                    placeholder="https://exemplo.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="icon_class">Ícone (Opcional)</label>
                                <select id="icon_class" name="icon_class" class="form-select" style="width:100%">
                                    <option value="">Sem Ícone</option>
                                    <option value="fab fa-whatsapp" data-icon="fab fa-whatsapp">WhatsApp</option>
                                    <option value="fab fa-instagram" data-icon="fab fa-instagram">Instagram</option>
                                    <option value="fab fa-facebook" data-icon="fab fa-facebook">Facebook</option>
                                    <option value="fab fa-telegram" data-icon="fab fa-telegram">Telegram</option>
                                    <option value="fas fa-globe" data-icon="fas fa-globe">Site</option>
                                    <option value="fas fa-shopping-cart" data-icon="fas fa-shopping-cart">Loja</option>
                                    <option value="fas fa-envelope" data-icon="fas fa-envelope">E-mail</option>
                                    <option value="fas fa-map-marker-alt" data-icon="fas fa-map-marker-alt">Endereço
                                    </option>
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Link Ativo</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- 4) Tabela de Redes Sociais --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button id="addSocialBtn" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#socialModal">
                    <i class="fab fa-instagram me-1"></i>Nova Rede
                </button>
            </div>
            <div class="card-body">
                <table id="socialsTable" class="table table-sm table-hover w-100">
                    <thead>
                        <tr>
                            <th style="width:20px;"></th>
                            <th>Ícone</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th style="width:120px">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            {{-- Modal CRUD Redess Sociais --}}
            <div class="modal fade" id="socialModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: var(--terracota); color: var(--white);">
                            <h5 class="modal-title" id="socialModalLabel">Nova Rede Social</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="socialForm">
                            @csrf
                            <input type="hidden" id="social_id" name="id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="social_icon" class="form-label">Ícone</label>
                                    <select id="social_icon" name="icon_class" class="form-select" style="width:100%">
                                        <option value="fab fa-instagram" data-icon="fab fa-instagram">Instagram</option>
                                        <option value="fab fa-tiktok" data-icon="fab fa-tiktok">TikTok</option>
                                        <option value="fab fa-whatsapp" data-icon="fab fa-whatsapp">WhatsApp</option>
                                        <option value="fab fa-pinterest" data-icon="fab fa-pinterest">Pinterest</option>
                                        <option value="fab fa-facebook" data-icon="fab fa-facebook">Facebook</option>
                                        <option value="fab fa-twitter" data-icon="fab fa-twitter">Twitter</option>
                                        <option value="fab fa-linkedin" data-icon="fab fa-linkedin">LinkedIn</option>
                                        <option value="fab fa-youtube" data-icon="fab fa-youtube">YouTube</option>
                                        <option value="fab fa-telegram" data-icon="fab fa-telegram">Telegram</option>
                                        {{-- e assim por diante... --}}
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="social_url" class="form-label">URL</label>
                                    <input type="url" id="social_url" name="url" class="form-control"
                                        placeholder="https://..." required>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="social_active" name="is_active"
                                        checked>
                                    <label class="form-check-label" for="social_active">Ativo</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

@endsection

@push('scripts')
    <!-- DataTables + Bootstrap5 integration -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- jQuery UI sortable -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            // CSRF setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Generic error handler
            const handleError = xhr => {
                let msg = 'Algo deu errado';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                Swal.fire('Erro', msg, 'error');
            };

            // Icon select2
            function iconTpl(opt) {
                if (!opt.id) return opt.text;
                return $(`<span><i class="${$(opt.element).data('icon')} me-2"></i>${opt.text}</span>`);
            }
            $('#icon_class').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#linkModal'),
                templateResult: iconTpl,
                templateSelection: iconTpl,
                escapeMarkup: m => m
            });

            // Save settings
            $('#linktreeSettingsForm').submit(e => {
                e.preventDefault();
                const btn = $('button[type=submit]', e.target),
                    html = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $.post("{{ route('admin.linktree-manager.settings.update') }}", $(e.target).serialize())
                    .done(res => Swal.fire({
                        icon: 'success',
                        title: 'Salvo',
                        text: res.success,
                        timer: 1500,
                        showConfirmButton: false
                    }))
                    .fail(handleError)
                    .always(() => btn.prop('disabled', false).html(html));
            });

            // DataTable init
            const table = $('#linksTable').DataTable({
                paging: false, // desliga a paginação e mostra tudo
                searching: false, // remove a caixa de busca
                lengthChange: false, // remove o dropdown "Show entries"
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.linktree-manager.data') }}",
                columns: [{
                        data: 'display_order',
                        orderable: false,
                        render: () => '<i class="fas fa-arrows-alt-v"></i>'
                    },
                    {
                        data: 'title',
                        render: (t, _, r) => r.icon_class ? `<i class="${r.icon_class} me-2"></i>${t}` :
                            t
                    },
                    {
                        data: 'url',
                        render: u => `<a href="${u}" target="_blank">${u}</a>`
                    },
                    {
                        data: 'is_active',
                        render: v => v ?
                            '<span class="badge bg-success">Ativo</span>' :
                            '<span class="badge bg-secondary">Inativo</span>'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: r =>
                            `
            <button class="btn btn-info btn-sm edit-btn" data-id="${r.id}"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger btn-sm delete-btn" data-id="${r.id}"><i class="fas fa-trash-alt"></i></button>`
                    }
                ],
                dom: `
        <'row mb-3'<'col-sm-6'l><'col-sm-6'f>>
        <'table-responsive'tr>
        <'row mt-3'<'col-sm-5'i><'col-sm-7'p>>
      `,
                pagingType: 'simple_numbers',
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                initComplete() {
                    makeSortable();
                }
            });
            table.on('draw', makeSortable);

            function makeSortable() {
                $('#linksTable tbody').sortable({
                    handle: '.fa-arrows-alt-v',
                    helper(e, tr) {
                        const $orig = tr.children(),
                            $clone = tr.clone();
                        $clone.children().each((i, td) => $(td).width($orig.eq(i).width()));
                        return $clone;
                    },
                    update() {
                        const order = $('#linksTable tbody tr').map(function() {
                            return $(this).find('.edit-btn').data('id');
                        }).get();
                        $.post("{{ route('admin.linktree-manager.update-order') }}", {
                                order
                            })
                            .done(() => table.ajax.reload(null, false))
                            .fail(handleError);
                    }
                }).disableSelection();
            }

            // New link
            $('#addLinkBtn').click(() => {
                $('#linkModalLabel').text('Novo Link');
                $('#linkForm')[0].reset();
                $('#link_id').val('');
                $('#icon_class').val('').trigger('change');
                $('#is_active').prop('checked', true);
            });

            // Edit link
            $('#linksTable').on('click', '.edit-btn', function() {
                const row = table.row($(this).closest('tr')).data();
                $('#linkModalLabel').text('Editar Link');
                $('#link_id').val(row.id);
                $('#title').val(row.title);
                $('#url').val(row.url);
                $('#icon_class').val(row.icon_class).trigger('change');
                $('#is_active').prop('checked', row.is_active == 1);
                $('#linkModal').modal('show');
            });

            // Delete link
            $('#linksTable').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Tem certeza?',
                    icon: 'warning',
                    showCancelButton: true
                }).then(res => {
                    if (!res.isConfirmed) return;
                    $.ajax({
                            url: `{{ url('admin/linktree-manager') }}/${id}`,
                            type: 'DELETE'
                        })
                        .done(r => {
                            Swal.fire('Removido!', r.success, 'success');
                            table.ajax.reload(null, false);
                        })
                        .fail(handleError);
                });
            });

            // Save link (create/update)
            $('#linkForm').submit(e => {
                e.preventDefault();
                const id = $('#link_id').val();
                const url = id ?
                    `{{ url('admin/linktree-manager') }}/${id}` :
                    `{{ route('admin.linktree-manager.store') }}`;
                const type = id ? 'PUT' : 'POST';
                const btn = $('button[type=submit]', e.target),
                    html = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $.ajax({
                        url,
                        type,
                        data: $(e.target).serialize()
                    })
                    .done(r => {
                        Swal.fire('Sucesso!', r.success, 'success');
                        $('#linkModal').modal('hide');
                        table.ajax.reload(null, false);
                    })
                    .fail(handleError)
                    .always(() => btn.prop('disabled', false).html(html));
            });

            // logo abaixo do initComplete() do links...
            const socialTable = $('#socialsTable').DataTable({
                paging: false,
                searching: false,
                lengthChange: false,
                processing: true,
                serverSide: false,
                ajax: "{{ route('admin.linktree-manager.socials.data') }}",
                columns: [{
                        data: 'display_order',
                        orderable: false,
                        render: () => ' <i class="fas fa-arrows-alt-v"></i>'
                    },
                    {
                        data: 'icon_class',
                        render: c => `<i class="${c} me-2"></i>`
                    },
                    {
                        data: 'url',
                        render: u => `<a href="${u}" target="_blank">${u}</a>`
                    },
                    {
                        data: 'is_active',
                        render: v => v ?
                            '<span class="badge bg-success">Ativo</span>' :
                            '<span class="badge bg-secondary">Inativo</span>'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: r =>
                            `<button class="btn btn-info btn-sm edit-social" data-id="${r.id}"><i class="fas fa-edit"></i></button>
         <button class="btn btn-danger btn-sm del-social"  data-id="${r.id}"><i class="fas fa-trash"></i></button>`
                    }
                ],
                initComplete() {
                    makeSocialSortable();
                }
            });
            socialTable.on('draw', makeSocialSortable);

            function makeSocialSortable() {
                $('#socialsTable tbody').sortable({
                    handle: '.fa-arrows-alt-v',
                    helper(e, tr) {
                        const $orig = tr.children(),
                            $clone = tr.clone();
                        $clone.children().each((i, td) => $(td).width($orig.eq(i).width()));
                        return $clone;
                    },
                    update() {
                        const order = $('#socialsTable tbody tr').map(function() {
                            return $(this).find('.edit-social').data('id');
                        }).get();
                        $.post("{{ route('admin.linktree-manager.socials.order') }}", {
                                order
                            })
                            .done(() => socialTable.ajax.reload(null, false))
                            .fail(handleError);
                    }
                }).disableSelection();
            }
            // 1) Inicia o Select2 no modal de Social
            $('#social_icon').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#socialModal'),
                templateResult: iconTpl,
                templateSelection: iconTpl,
                escapeMarkup: m => m
            });

            // 2) Ao clicar em “Nova Rede” limpa form
            $('#addSocialBtn').click(() => {
                $('#socialModalLabel').text('Nova Rede Social');
                $('#socialForm')[0].reset();
                $('#social_id').val('');
                $('#social_icon').val('fab fa-instagram').trigger('change');
                $('#social_active').prop('checked', true);
            });

            // 3) Editar
            $('#socialsTable').on('click', '.edit-social', function() {
                const row = socialTable.row($(this).closest('tr')).data();
                $('#socialModalLabel').text('Editar Rede Social');
                $('#social_id').val(row.id);
                $('#social_icon').val(row.icon_class).trigger('change');
                $('#social_url').val(row.url);
                $('#social_active').prop('checked', row.is_active == 1);
                $('#socialModal').modal('show');
            });

            // 4) Submissão do form Social (store/update)
            $('#socialForm').submit(e => {
                e.preventDefault();
                const id = $('#social_id').val();
                const url = id ?
                    `{{ url('admin/linktree-manager/socials') }}/${id}` :
                    `{{ route('admin.linktree-manager.socials.store') }}`;
                const type = id ? 'PUT' : 'POST';
                const btn = $('button[type=submit]', e.target),
                    html = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $.ajax({
                        url,
                        type,
                        data: $(e.target).serialize()
                    })
                    .done(r => {
                        Swal.fire('Sucesso!', r.success, 'success');
                        $('#socialModal').modal('hide');
                        socialTable.ajax.reload(null, false);
                    })
                    .fail(handleError)
                    .always(() => btn.prop('disabled', false).html(html));
            });

            // 5) Delete
            $('#socialsTable').on('click', '.del-social', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirma exclusão?',
                    icon: 'warning',
                    showCancelButton: true
                }).then(res => {
                    if (!res.isConfirmed) return;
                    $.ajax({
                            url: `{{ url('admin/linktree-manager/socials') }}/${id}`,
                            type: 'DELETE'
                        })
                        .done(r => {
                            Swal.fire('Removido!', r.success, 'success');
                            socialTable.ajax.reload(null, false);
                        })
                        .fail(handleError);
                });
            });


        });
    </script>
@endpush
