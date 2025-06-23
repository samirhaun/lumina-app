@extends('admin::layouts.layout')
@section('title', 'Editar Banner')

@section('content')
    <div id="catalogContent">
        <style>
            :root {
                --terracota: #A0522D;
                --white: #ffffff;
            }

            /* Card geral */
            #catalogContent .card {
                border-radius: .375rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
            }

            /* Cabeçalho do card em terracota */
            #catalogContent .card.card-primary>.card-header {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
                border-bottom: none;
            }

            /* Botões primários em terracota */
            #catalogContent .btn-primary,
            #catalogContent .btn-success {
                background-color: var(--terracota) !important;
                border-color: var(--terracota) !important;
            }

            /* Botões secundários (cancelar) */
            #catalogContent .btn-secondary {
                background-color: #6c757d !important;
                border-color: #6c757d !important;
            }

            /* Close button no modal/card */
            #catalogContent .modal-header .btn-close,
            #catalogContent .card-header .btn-close {
                width: 1.6rem;
                height: 1.6rem;
                background-color: #dc3545;
                border-radius: .25rem;
                position: relative;
            }

            #catalogContent .modal-header .btn-close::before,
            #catalogContent .card-header .btn-close::before {
                content: "\f00d";
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
                color: #fff;
                font-size: .9rem;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            /* Form inputs */
            #catalogContent .form-control {
                border-radius: .25rem;
            }

            #catalogContent .form-check-input {
                accent-color: var(--terracota);
            }

            #catalogContent .col-md-3.d-flex.align-items-end {
                align-items: center !important;
            }

            /* (Opcional) Se quiser um pouquinho de espaço acima do checkbox)
                                    #catalogContent .form-check {
                                        margin-top: .5rem;
                                    }
                                    */

            /* 2) Espaço entre os botões no footer */
            #catalogContent .card-footer .btn+.btn {
                margin-left: .5rem;
            }

            #catalogContent .form-check-input {
                accent-color: var(--terracota);
            }

            /* Toggle Switch Styles */
            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
                vertical-align: middle;
                margin-right: .5rem;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 24px;
            }

            .slider::before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: #fff;
                transition: .4s;
                border-radius: 50%;
            }

            .switch input:checked+.slider {
                background-color: var(--terracota);
            }

            .switch input:checked+.slider::before {
                transform: translateX(26px);
            }
        </style>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar Banner #{{ $banner->id }}</h3>
            </div>
            <form action="{{ route('admin.hero-banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <label for="image" class="form-label">Nova Imagem do Banner (Opcional)</label>
                        <input type="file" name="image" id="image" class="form-control">
                        <small class="form-text text-muted">
                            Envie uma nova imagem apenas se desejar substituir a atual.
                        </small>
                        <div class="mt-3">
                            <label class="d-block">Imagem Atual:</label>
                            <img src="{{ Storage::url($banner->image_path) }}" alt="Imagem atual" class="img-thumbnail"
                                style="max-height: 120px;">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="link_url" class="form-label">Redirecionar para a Categoria (Opcional)</label>
                        <select name="link_url" id="link_url" class="form-control">
                            <option value="">Nenhum link</option>
                            @foreach ($productTypes as $type)
                                <option value="{{ route('frontend.products.index', ['type' => $type->id]) }}"
                                    {{ old('link_url', $banner->link_url) == route('frontend.products.index', ['type' => $type->id]) ? 'selected' : '' }}>
                                    Categoria: {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label d-block">Status</label>
                        <label class="switch">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span>Ativo?</span>
                    </div>
                    <div class="col-12">
                        <label for="sort_order" class="form-label">Ordem de Exibição</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control"
                            value="{{ old('sort_order', $banner->sort_order) }}" required>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('admin.hero-banners.index') }}" class="btn btn-secondary me-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
