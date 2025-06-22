

<?php $__env->startSection('title', 'Dashboard de Performance'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-center justify-content-md-end">
            <div class="btn-group" role="group" id="dashboard-period-filter">
                <button type="button" class="btn btn-outline-primary" data-period="today">Hoje</button>
                <button type="button" class="btn btn-outline-primary" data-period="week">Esta Semana</button>
                <button type="button" class="btn btn-primary active" data-period="month">Este Mês</button>
                <button type="button" class="btn btn-outline-primary" data-period="fortnight">Últimos 30 dias</button>
            </div>
        </div>
    </div>

    
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-5 g-3 mb-3">
        
        <div class="col">
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="small-box bg-success text-white d-block">
                <div class="inner">
                    <h3 id="kpi-sales-value">R$ 0,00</h3>
                    <p>Total em Vendas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="small-box-footer" id="kpi-sales-comparison">--</div>
            </a>
        </div>


        
        <div class="col">
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="small-box bg-info text-white d-block">
                <div class="inner">
                    <h3 id="kpi-sales-count-value">0</h3>
                    <p>Quantidade de Vendas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="small-box-footer" id="kpi-sales-count-comparison">--</div>
            </a>
        </div>

        
        <div class="col">
            <div class="small-box bg-primary text-white">
                <div class="inner">
                    <h3 id="kpi-profit-value">R$ 0,00</h3>
                    <p>Lucro Bruto</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="small-box-footer" id="kpi-profit-comparison">--</div>
            </div>
        </div>

        
        <div class="col">
            <a href="<?php echo e(route('admin.purchases.index')); ?>" class="small-box bg-danger text-white d-block">
                <div class="inner">
                    <h3 id="kpi-expenses-value">R$ 0,00</h3>
                    <p>Total em Saídas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-circle-down"></i>
                </div>
                <div class="small-box-footer" id="kpi-expenses-comparison">--</div>
            </a>
        </div>

        
        <div class="col">
            <a href="<?php echo e(route('admin.stock.index')); ?>" class="small-box bg-warning text-dark d-block">
                <div class="inner">
                    <h3>Estoque</h3>
                    <p>Ver Alertas e Posição</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="small-box-footer text-dark">
                    Ver Estoque Completo <i class="fas fa-arrow-circle-right"></i>
                </div>
            </a>
        </div>
    </div>

    
    <div class="row mb-3">
        <section class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">…</div>
                <div class="card-body">
                    <div class="chart" style="height:300px;">
                        <canvas id="flowChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
    <div class="row">
        <section class="col-12">
            <div class="card card-success card-outline">
                <div class="card-header">…</div>
                <div class="card-body">
                    <div class="chart" style="height:407px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            let flowChartInstance, topProductsChartInstance;

            const formatCurrency = value => 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // FUNÇÃO DE COMPARAÇÃO COM CORREÇÃO DE COR
            function updateComparisonText(elementId, value) {
                const el = $(elementId);
                const icon = value >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
                // O texto agora sempre terá alto contraste
                const color = 'text-white-50'; // Um branco semi-transparente para destaque

                el.html(
                    `<span class="${color}"><i class="${icon}"></i> ${value.toFixed(1)}%</span> vs. período anterior`
                );
            }

            function fetchDashboardData(period) {
                Swal.fire({
                    title: 'Atualizando Dashboard...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.get("<?php echo e(route('admin.dashboard.data')); ?>", {
                    period: period
                }).done(response => {
                    // Atualiza os KPIs
                    $('#kpi-sales-value').text(formatCurrency(response.kpis.sales.value));
                    updateComparisonText('#kpi-sales-comparison', response.kpis.sales.comparison);
                    $('#kpi-sales-count-value').text(response.kpis.sales_count.value);
                    updateComparisonText('#kpi-sales-count-comparison', response.kpis.sales_count
                        .comparison);
                    $('#kpi-profit-value').text(formatCurrency(response.kpis.profit.value));
                    updateComparisonText('#kpi-profit-comparison', response.kpis.profit.comparison);

                    $('#kpi-expenses-value').text(formatCurrency(response.kpis.expenses.value));
                    updateComparisonText('#kpi-expenses-comparison', response.kpis.expenses.comparison);

                    // Atualiza Gráfico de Fluxo
                    if (flowChartInstance) flowChartInstance.destroy();
                    flowChartInstance = new Chart(document.getElementById('flowChart').getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: response.charts.flow.labels,
                            datasets: [{
                                    label: 'Vendas (R$)',
                                    data: response.charts.flow.sales,
                                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                    borderColor: '#28a745',
                                    fill: true,
                                    tension: 0.3
                                },
                                {
                                    label: 'Compras Pagas (R$)',
                                    data: response.charts.flow.purchases,
                                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                                    borderColor: '#dc3545',
                                    fill: true,
                                    tension: 0.3
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        callback: (value) => 'R$ ' + value
                                    }
                                }
                            }
                        }
                    });

                    // Atualiza Gráfico de Produtos Rentáveis
                    if (topProductsChartInstance) topProductsChartInstance.destroy();
                    topProductsChartInstance = new Chart(document.getElementById('topProductsChart')
                        .getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: response.charts.top_products.labels,
                                datasets: [{
                                        label: 'Lucro Total (R$)',
                                        data: response.charts.top_products.profit_data,
                                        backgroundColor: '#007bff',
                                        yAxisID: 'y'
                                    },
                                    {
                                        label: 'Unidades Vendidas',
                                        data: response.charts.top_products.quantity_data,
                                        backgroundColor: '#17a2b8',
                                        yAxisID: 'y1'
                                    }
                                ]
                            },
                            options: {
                                indexAxis: 'y',
                                maintainAspectRatio: false,
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom'
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            display: true
                                        }
                                    },
                                    x: {
                                        display: true,
                                        position: 'left',
                                        ticks: {
                                            callback: (value) => 'R$ ' + value
                                        },
                                        title: {
                                            display: true,
                                            text: 'Lucro'
                                        }
                                    },
                                    x1: {
                                        display: true,
                                        position: 'right',
                                        grid: {
                                            drawOnChartArea: false
                                        },
                                        ticks: {
                                            callback: (value) => value + ' un.'
                                        },
                                        title: {
                                            display: true,
                                            text: 'Unidades'
                                        }
                                    }
                                }
                            }
                        });

                    Swal.close();
                }).fail(() => Swal.fire('Erro!', 'Não foi possível carregar os dados do dashboard.', 'error'));
            }

            // Event listener para os botões de filtro
            $('#dashboard-period-filter .btn').on('click', function() {
                $('#dashboard-period-filter .btn').removeClass('active btn-primary').addClass(
                    'btn-outline-primary');
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                fetchDashboardData($(this).data('period'));
            });

            // Carga inicial dos dados
            fetchDashboardData('month');
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/dashboard.blade.php ENDPATH**/ ?>