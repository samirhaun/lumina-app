@extends('admin::layouts.layout')

@section('title', 'Gerenciar Banners')
@section('header', 'Banners da Homepage')

@section('content')
    <div id="bannerCatalog">
        <style>
            :root {
                --terracota: #A0522D;
                --white: #ffffff;
            }

            /* Container geral */
            #bannerCatalog {
                margin: 1rem;
            }

            /* Card principal */
            #bannerCatalog .card {
                border: 1px solid var(--terracota);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* Cabeçalho do card em terracota */
            #bannerCatalog .card-header {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
                border-bottom: none;
            }

            /* Botões (Adicionar, Editar) em terracota */
            #bannerCatalog .btn-success,
            #bannerCatalog .btn-primary {
                background-color: var(--terracota) !important;
                border-color: var(--terracota) !important;
                color: var(--white) !important;
            }

            #bannerCatalog .btn-success:hover,
            #bannerCatalog .btn-primary:hover {
                background-color: darken(var(--terracota), 5%) !important;
                border-color: darken(var(--terracota), 5%) !important;
            }

            /* Título e botão alinhados */
            #bannerCatalog .card-header .d-flex {
                width: 100%;
            }

            /* Tabela */
            #bannerCatalog table.dataTable thead th {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
            }

            #bannerCatalog table.dataTable tbody tr:hover {
                background-color: rgba(160, 82, 45, 0.1);
            }

            /* Badges de status no esquema terracota */
            #bannerCatalog .badge.bg-success {
                background-color: var(--terracota) !important;
            }

            /* Paginação Active */
            #bannerCatalog .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
                border: none;
            }

            /* Filtro e LengthMenu */
            #bannerCatalog .dataTables_wrapper .dataTables_filter input,
            #bannerCatalog .dataTables_wrapper .dataTables_length select {
                border: 1px solid var(--terracota);
                border-radius: .25rem;
            }
        </style>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Banners da Homepage</h3>
                <a href="{{ route('admin.hero-banners.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Adicionar Novo Banner
                </a>
            </div>
            <div class="card-body">
                <table id="bannersTable" class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Link de Destino</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th style="width: 100px">Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const baseUrl = "{{ url('admin/hero-banners') }}";
            const dataTableUrl = "{{ route('admin.hero-banners.data') }}";
            const csrfToken = "{{ csrf_token() }}";

            const table = $('#bannersTable').DataTable({
                processing: true,
                ajax: dataTableUrl,
                columns: [{
                        data: 'image_url',
                        render: url => `<img src="${url}" height="50" class="img-thumbnail" />`
                    },
                    {
                        data: 'link_url',
                        defaultContent: '<em>Nenhum</em>'
                    },
                    {
                        data: 'sort_order'
                    },
                    {
                        data: 'is_active',
                        render: d => d ?
                            '<span class="badge bg-success">Ativo</span>' :
                            '<span class="badge bg-danger">Inativo</span>'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (data, type, row) => `
    <a href="${baseUrl}/${data.id}/edit" class="btn btn-sm btn-primary" title="Editar">
      <i class="fas fa-edit"></i>
    </a>
    <button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}" title="Excluir">
      <i class="fas fa-trash"></i>
    </button>
  `
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
                }
            });

            // Deletar com SweetAlert
            $('#bannersTable').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: 'Este banner será removido permanentemente!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                            url: `${baseUrl}/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                        })
                        .done(resp => {
                            table.ajax.reload(null, false);
                            Swal.fire('Excluído!', resp.success, 'success');
                        })
                        .fail(() => {
                            Swal.fire('Erro!', 'Não foi possível excluir.', 'error');
                        });
                });
            });
        });
    </script>
@endpush
