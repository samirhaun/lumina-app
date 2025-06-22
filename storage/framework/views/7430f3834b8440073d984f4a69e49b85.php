

<?php $__env->startSection('title', 'Fluxo de Caixa'); ?>
<?php $__env->startSection('header', 'Relatório de Fluxo de Caixa'); ?>

<?php $__env->startSection('content'); ?>


<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Filtrar por Período</h3>
    </div>
    <form id="cashFlowForm">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4 form-group">
                    <label for="start_date">Data de Início</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo e(date('Y-m-01')); ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label for="end_date">Data de Fim</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo e(date('Y-m-t')); ?>">
                </div>
                <div class="col-md-4 form-group">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-search"></i> Gerar Relatório
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


<div id="resultsContainer" style="display: none;">
    
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="totalInflow">R$ 0,00</h3>
                    <p>Total de Entradas (Crédito)</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="totalOutflow">R$ 0,00</h3>
                    <p>Total de Saídas (Débito)</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="netBalance">R$ 0,00</h3>
                    <p>Saldo do Período</p>
                </div>
                <div class="icon"><i class="fas fa-balance-scale"></i></div>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lançamentos do Período</h3>
        </div>
        <div class="card-body">
            <table id="cashFlowTable" class="table table-bordered table-striped" style="width:100%;">
                <thead>
                    <tr>
                        <th style="width: 150px;">Data</th>
                        <th>Descrição</th>
                        <th class="text-end">Entradas (Crédito)</th>
                        <th class="text-end">Saídas (Débito)</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.js"></script>
<script>
$(function () {
    // --- SETUP E FUNÇÕES GLOBAIS ---
    const dtLanguage = { "sEmptyTable": "Nenhum lançamento para o período selecionado", "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ lançamentos", "sInfoEmpty": "Mostrando 0 até 0 de 0 registros", "sInfoFiltered": "", "sLengthMenu": "_MENU_ resultados por página", "sLoadingRecords": "Carregando...", "sProcessing": "Processando...", "sZeroRecords": "Nenhum registro encontrado", "sSearch": "Pesquisar", "oPaginate": { "sNext": "Próximo", "sPrevious": "Anterior", "sFirst": "Primeiro", "sLast": "Último" } };
    const formatCurrency = value => !isNaN(parseFloat(value)) ? parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : 'R$ 0,00';
    const formatDate = dateString => dateString ? new Date(dateString).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) : 'N/A';
    const handleAjaxError = xhr => { Swal.close(); if (xhr.status === 422) { let errors = xhr.responseJSON.errors; let errorHtml = '<ul class="text-start">'; $.each(errors, (k, v) => { errorHtml += `<li>${v[0]}</li>`; }); errorHtml += '</ul>'; Swal.fire({title: 'Erro de Validação', html: errorHtml, icon: 'error'}); } else { Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error'); } };

    // --- INICIALIZAÇÃO DA DATATABLE (COM AGRUPAMENTO) ---
    const cashFlowTable = $('#cashFlowTable').DataTable({
        data: [], 
        columns: [
            { data: 'date', visible: false }, 
            { data: 'description' },
            { data: 'credit', className: 'text-end text-success fw-bold', render: d => d > 0 ? formatCurrency(d) : '-' },
            { data: 'debit', className: 'text-end text-danger fw-bold', render: d => d > 0 ? formatCurrency(d) : '-' }
        ],
        language: dtLanguage,
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        paging: false,
        info: true,
        
        drawCallback: function (settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var last = null;
 
            api.column(0, { page: 'current' }).data().each(function (group, i) {
                let groupDate = formatDate(group);
                if (last !== groupDate) {
                    // VERSÃO FINAL COM MÁXIMO DESTAQUE
                    $(rows).eq(i).before(
                        `<tr class="bg-dark text-white">
                            <td colspan="4" class="text-center fw-bold p-2" style="font-size: 1.1em; letter-spacing: 1px;">
                                <i class="fas fa-calendar-day me-2"></i> ${groupDate}
                            </td>
                        </tr>`
                    );
                    last = groupDate;
                }
            });
        }
    });

    // --- LÓGICA DO FORMULÁRIO DE FILTRO ---
    $('#cashFlowForm').on('submit', function(e) {
        e.preventDefault();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            Swal.fire('Atenção', 'Por favor, selecione a data de início e de fim.', 'warning');
            return;
        }

        Swal.fire({ title: 'Gerando relatório...', text: 'Buscando e processando as transações.', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const url = `<?php echo e(route('admin.cash-flow.report')); ?>?start_date=${startDate}&end_date=${endDate}`;

        $.get(url).done(response => {
            $('#totalInflow').text(formatCurrency(response.summary.total_inflow));
            $('#totalOutflow').text(formatCurrency(response.summary.total_outflow));
            $('#netBalance').text(formatCurrency(response.summary.net_balance));

            const balanceBox = $('#netBalance').closest('.small-box');
            balanceBox.removeClass('bg-info bg-success bg-danger');
            if (response.summary.net_balance >= 0) {
                balanceBox.addClass('bg-success');
            } else {
                balanceBox.addClass('bg-danger');
            }

            cashFlowTable.clear().rows.add(response.transactions).draw();
            
            $('#resultsContainer').slideDown();
            Swal.close();

        }).fail(handleAjaxError);
    });

    // Gera o relatório do mês atual ao carregar a página
    $('#cashFlowForm').trigger('submit');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/cash-flow/index.blade.php ENDPATH**/ ?>