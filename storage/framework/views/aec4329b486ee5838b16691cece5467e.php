<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name')); ?> • Admin - <?php echo $__env->yieldContent('title'); ?></title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
</head>
<style>
    :root {
        --terracota: #A0522D;
        --beige: #EDE8E0;
    }

    /* Ícone + texto em terracota */
    .nav-sidebar .nav-link {
        color: var(--terracota) !important;
    }

    .nav-sidebar .nav-link .nav-icon {
        color: var(--terracota) !important;
    }

    .nav-sidebar .nav-link p {
        color: var(--terracota) !important;
        margin: 0;
    }

    /* ativo: fundo terracota, texto/beige */
    .nav-sidebar .nav-link.active {
        background-color: var(--terracota) !important;
        color: var(--beige) !important;
    }

    .nav-sidebar .nav-link.active .nav-icon,
    .nav-sidebar .nav-link.active p {
        color: var(--beige) !important;
    }

    /* hover manter contraste */
    .nav-sidebar .nav-link:hover {
        background-color: rgba(160, 82, 45, 0.1);
        color: var(--terracota) !important;
    }

    /* Fundo bege para navbar e sidebar */
    .main-header.navbar,
    .main-sidebar {
        background-color: var(--beige) !important;
    }

    /* Ajuste de contraste do botão de menu */
    .main-header .nav-link,
    .main-header .btn {
        color: var(--terracota) !important;
    }

    /* Botão de logout */
    .btn-logout {
        border: 1px solid var(--terracota);
        color: var(--terracota);
        background-color: transparent;
        transition: all .2s;
    }

    .btn-logout:hover {
        background-color: var(--terracota);
        color: var(--beige);
    }

    /* Coloque logo após os outros overrides de cores: */
    .nav-sidebar .nav-header {
        color: var(--terracota) !important;
        padding: .5rem 1rem;
        font-weight: 700;
        font-size: .75rem;
        text-transform: uppercase;
    }

    /* Estilo para os headers de grupo */
    .nav-sidebar .nav-header {
        background-color: var(--terracota);
        color: var(--beige) !important;
        padding: .5rem 1rem;
        font-weight: 700;
        font-size: .75rem;
        text-transform: uppercase;
        margin-top: 1rem;
    }

    /* Fundo levemente contrastado para o nível 1 (submenu) */
    .nav-sidebar .nav-treeview {
        background-color: rgba(160, 82, 45, 0.05);
    }

    /* Indentação extra e fonte menor para subitens */
    .nav-sidebar .nav-treeview .nav-link {
        padding-left: 2.5rem !important;
        font-size: .9rem;
    }

    /* Ícones de submenu menores */
    .nav-sidebar .nav-treeview .nav-icon {
        font-size: .85rem;
    }

    /* Mantém ativo destacado no submenu */
    .nav-sidebar .nav-treeview .nav-link.active {
        background-color: var(--terracota) !important;
        color: var(--beige) !important;
    }

.btn-logout {
  padding: .25rem .75rem;              /* deixa um “hit-area” confortável */
  border: 1px solid var(--terracota);
  background-color: transparent !important;
  color: var(--terracota) !important;
  transition: background-color .2s, color .2s;
}

.btn-logout:hover {
  background-color: var(--terracota) !important;
  color: var(--beige)   !important;
}

</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand-link text-center py-4">
                <img src="<?php echo e(asset('images/logo-terracota.png')); ?>" alt="<?php echo e(config('app.name')); ?> Admin"
                    class="img-fluid" style="max-height: 60px; object-fit: contain;">
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.dashboard')); ?>"
                                class="nav-link <?php echo e(Route::is('admin.dashboard') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        
                        <li
                            class="nav-item has-treeview <?php echo e(Route::is('admin.sales.*', 'admin.cash-flow.*', 'admin.financial-transactions.*', 'admin.purchases.*') ? 'menu-open' : ''); ?>">
                            <a href="#"
                                class="nav-link <?php echo e(Route::is('admin.sales.*', 'admin.cash-flow.*', 'admin.financial-transactions.*', 'admin.purchases.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-wallet"></i>
                                <p>
                                    Financeiro
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.sales.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.sales.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-cash-register nav-icon"></i>
                                        <p>Vendas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.cash-flow.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.cash-flow.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-chart-line nav-icon"></i>
                                        <p>Fluxo de Caixa</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.financial-transactions.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.financial-transactions.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-money-bill-wave nav-icon"></i>
                                        <p>Transações</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.purchases.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.purchases.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                        <p>Contas a Pagar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        
                        <li
                            class="nav-item has-treeview <?php echo e(Route::is('admin.pricing.*', 'admin.products.*', 'admin.stock.*', 'admin.misc-items.*', 'admin.misc-categories.*') ? 'menu-open' : ''); ?>">
                            <a href="#"
                                class="nav-link <?php echo e(Route::is('admin.pricing.*', 'admin.products.*', 'admin.stock.*', 'admin.misc-items.*', 'admin.misc-categories.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>
                                    Produtos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.pricing.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.pricing.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-tag nav-icon"></i>
                                        <p>Precificação</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.products.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.products.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-box-open nav-icon"></i>
                                        <p>Catálogo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.stock.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.stock.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-warehouse nav-icon"></i>
                                        <p>Estoque</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.misc-items.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.misc-items.*') || Route::is('admin.misc-categories.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-puzzle-piece nav-icon"></i>
                                        <p>Custos Diversos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        
                        <li
                            class="nav-item has-treeview <?php echo e(Route::is('admin.suppliers.*', 'admin.clients.*') ? 'menu-open' : ''); ?>">
                            <a href="#"
                                class="nav-link <?php echo e(Route::is('admin.suppliers.*', 'admin.clients.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-address-book"></i>
                                <p>
                                    Cadastros
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.suppliers.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.suppliers.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-truck nav-icon"></i>
                                        <p>Fornecedores</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.clients.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.clients.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-id-card nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        
                        <li
                            class="nav-item has-treeview <?php echo e(Route::is('admin.linktree-manager.*', 'admin.users.*') ? 'menu-open' : ''); ?>">
                            <a href="#"
                                class="nav-link <?php echo e(Route::is('admin.linktree-manager.*', 'admin.users.*') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-tools"></i>
                                <p>
                                    Utilitários
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.linktree-manager.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.linktree-manager.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-link nav-icon"></i>
                                        <p>Linktree</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.users.index')); ?>"
                                        class="nav-link <?php echo e(Route::is('admin.users.*') ? 'active' : ''); ?>">
                                        <i class="fas fa-users nav-icon"></i>
                                        <p>Usuários</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>


        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><?php echo $__env->yieldContent('header'); ?></h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </section>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/layouts/layout.blade.php ENDPATH**/ ?>