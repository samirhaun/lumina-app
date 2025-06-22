@extends('admin::layouts.layout')

@section('title', 'Precificação de Produtos')

@section('content')
<div id="pricingContent">
  <style>
    :root {
      --terracota: #A0522D;
      --beige:     #EDE8E0;
      --white:     #ffffff;
    }

    /* Só dentro do #pricingContent */
    #pricingContent .card-terracotta.card-tabs > .card-header {
      border-bottom: none;
      background-color: var(--terracota);
      color: var(--white);
    }
    #pricingContent .card-terracotta.card-tabs > .card-header .nav-link {
      background: transparent;
      border: 0;
      color: rgba(240,230,230, .8);
    }
    #pricingContent .card-terracotta.card-tabs > .card-header .nav-link.active {
      background: var(--beige);
      color: var(--terracota);
      border-color: #dee2e6 #dee2e6 var(--beige);
    }

    /* Cards e botões primários */
    #pricingContent .card-primary .card-header {
      background-color: var(--terracota);
      color: var(--white);
      border-bottom: none;
    }
    #pricingContent .btn-primary {
      background-color: var(--terracota);
      border-color: var(--terracota);
    }
  </style>

  {{-- 1) Configurações de Precificação --}}
  <div class="card card-primary mb-4">
    <div class="card-header">
      <h3 class="card-title">Configurações de Precificação Padrão</h3>
    </div>
    <form id="globalSettingsForm">
      @csrf
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <label>Custos Diversos Padrão (Embalagem, etc.)</label>
            <div class="input-group">
              <span class="input-group-text">R$</span>
              <input type="text" class="form-control global-price-input"
                     name="global_default_misc_costs"
                     value="{{ number_format($settings['global_default_misc_costs'] ?? 0, 2, ',', '.') }}">
            </div>
          </div>
          <div class="col-md-4">
            <label>Margem de Lucro Padrão</label>
            <div class="input-group">
              <input type="text" class="form-control global-price-input"
                     name="global_profit_margin"
                     value="{{ number_format($settings['global_profit_margin'] ?? 0, 2, ',', '.') }}">
              <span class="input-group-text">%</span>
            </div>
          </div>
        </div>
        <p class="text-muted mt-2 small">
          Alterar estes valores irá recalcular o "Preço Sugerido" de todos os produtos na tabela abaixo em tempo real.
        </p>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">Salvar Configurações Globais</button>
      </div>
    </form>
  </div>

  {{-- 2) Abas de filtro --}}
  <div class="card card-terracotta card-tabs mb-3">
    <div class="card-header p-0 pt-1 border-bottom-0">
      <ul class="nav nav-tabs" id="pricing-type-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#" data-category="">Todos os Tipos</a>
        </li>
        @foreach ($productTypes as $type)
        <li class="nav-item">
          <a class="nav-link" href="#" data-category="{{ $type->name }}">
            {{ $type->name }}
          </a>
        </li>
        @endforeach
      </ul>
    </div>
    <div class="card-body p-0">
      {{-- 3) Tabela de Produtos --}}
      <table id="pricingTable" class="table table-bordered table-hover mb-0" style="width:100%;">
        <thead class="bg-light">
          <tr>
            <th>Produto</th>
            <th>Tipo</th>
            <th>Custo Médio</th>
            <th>Custo Total (com Diversos)</th>
            <th>Preço Sugerido (com Margem)</th>
            <th>Preço de Venda Final</th>
            <th style="width: 80px;">Ação</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div> {{-- fecha #pricingContent --}}
@endsection

        @push('scripts')
            <!-- jQuery Mask Plugin -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

            <script>
                $(function() {
                    // Função para reaplicar a máscara em inputs gerados dinamicamente
                    function applyCurrencyMask() {
                        $('.global-price-input, .input-price, .input-sale-price')
                            .mask('000.000.000.000.000,00', {
                                reverse: true
                            });
                    }

                    // Aplica máscara aos campos estáticos
                    applyCurrencyMask();

                    // Setup AJAX com CSRF
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    // Helpers
                    const formatCurrency = v => !isNaN(parseFloat(v)) ?
                        parseFloat(v).toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        }) :
                        'R$ 0,00';
                    const parseInputNumber = v => parseFloat(String(v).replace(/\./g, '').replace(',', '.')) || 0;
                    const dtLanguage = {
                        sEmptyTable: "Nenhum produto para precificar",
                        sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ produtos",
                        sInfoEmpty: "Mostrando 0 até 0 de 0 registros",
                        sInfoFiltered: "(Filtrados de _MAX_ registros no total)",
                        sLengthMenu: "_MENU_ resultados por página",
                        sLoadingRecords: "Carregando...",
                        sProcessing: "Processando...",
                        sZeroRecords: "Nenhum registro encontrado",
                        sSearch: "Pesquisar",
                        oPaginate: {
                            sNext: "Próximo",
                            sPrevious: "Anterior",
                            sFirst: "Primeiro",
                            sLast: "Último"
                        }
                    };
                    const handleAjaxError = xhr => {
                        Swal.close();
                        if (xhr.responseJSON?.errors) {
                            let html = '<ul class="text-start">';
                            $.each(xhr.responseJSON.errors, (k, v) => html += `<li>${v[0]}</li>`);
                            html += '</ul>';
                            Swal.fire('Erro de Validação', html, 'error');
                        } else {
                            Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
                        }
                    };

                    // Variável da DataTable
                    let pricingTable;

                    // Recalcula custos e preços sugeridos
                    function recalculateAllRows() {
                        if (!pricingTable) return;
                        const miscCosts = parseInputNumber($('input[name="global_default_misc_costs"]').val());
                        const profitMargin = parseInputNumber($('input[name="global_profit_margin"]').val());
                        pricingTable.rows({
                            page: 'current'
                        }).every(function() {
                            const rowNode = this.node();
                            const data = this.data();
                            const avgCost = parseFloat(data.average_cost) || 0;
                            const totalCost = avgCost + miscCosts;
                            const suggestedPrice = totalCost * (1 + profitMargin / 100);
                            $(rowNode).find('.total-cost-display').text(formatCurrency(totalCost));
                            $(rowNode).find('.suggested-price-display').text(formatCurrency(suggestedPrice));
                        });
                    }

                    // Inicializa o DataTable
                    pricingTable = $('#pricingTable').DataTable({
                        processing: true,
                        ajax: "{{ route('admin.pricing.data') }}",
                        columns: [{
                                data: 'name'
                            },
                            {
                                data: 'type_name'
                            },

                            {
                                data: 'average_cost',
                                className: 'text-end table-secondary',
                                render: formatCurrency
                            },
                            {
                                data: null,
                                className: 'text-end table-secondary',
                                orderable: false,
                                render: () => '<span class="total-cost-display"></span>'
                            },
                            {
                                data: null,
                                className: 'text-end table-info',
                                orderable: false,
                                render: () => '<span class="suggested-price-display"></span>'
                            },
                            {
                                data: 'sale_price',
                                orderable: false,
                                render: d => `
                    <input type="text"
                           class="form-control form-control-sm input-price input-sale-price fw-bold"
                           value="${parseFloat(d).toFixed(2).replace('.',',')}">`
                            },
                            {
                                data: null,
                                orderable: false,
                                className: 'text-center',
                                render: (d, t, r) => `
                    <button class="btn btn-sm btn-success save-pricing-btn"
                            data-id="${r.id}" title="Salvar Preço">
                      <i class="fas fa-check"></i>
                    </button>`
                            }
                        ],
                        language: dtLanguage,
                        initComplete: function() {
                            recalculateAllRows();
                            applyCurrencyMask();
                        },
                        drawCallback: function() {
                            recalculateAllRows();
                            applyCurrencyMask();
                        }
                    });

                    // Eventos

                    // --- NOVA LÓGICA DE FILTRAGEM POR ABAS ---
                    $('#pricing-type-tabs').on('click', '.nav-link', function(e) {
                        e.preventDefault();
                        $('#pricing-type-tabs .nav-link').removeClass('active');
                        $(this).addClass('active');
                        const category = $(this).data('category');
                        // Filtra a coluna 1 (Tipo)
                        pricingTable.column(1).search(category).draw();
                    });
                    $('.global-price-input').on('input', recalculateAllRows);

                    $('#globalSettingsForm').on('submit', function(e) {
                        e.preventDefault();
                        $.post("{{ route('admin.pricing.settings.update') }}", $(this).serialize())
                            .done(res => Swal.fire('Sucesso!', res.success, 'success'))
                            .fail(handleAjaxError);
                    });

                    $('#pricingTable tbody').on('click', '.save-pricing-btn', function() {
                        const btn = $(this);
                        const row = btn.closest('tr');
                        const id = btn.data('id');
                        const url = `{{ url('admin/pricing') }}/${id}`;
                        const data = {
                            _method: 'POST',
                            sale_price: parseInputNumber(row.find('.input-sale-price').val())
                        };

                        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                        $.post(url, data)
                            .done(res => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Salvo!',
                                    text: res.success,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                pricingTable.ajax.reload(null, false);
                            })
                            .fail(handleAjaxError)
                            .always(() => btn.prop('disabled', false).html('<i class="fas fa-check"></i>'));
                    });
                });
            </script>
        @endpush
