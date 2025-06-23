@extends('admin::layouts.layout')
@section('title', 'Novo Banner')

@section('content')
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Adicionar Novo Banner</h3></div>
    <form action="{{ route('admin.hero-banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </div>
            @endif

            <div class="form-group mb-3">
                <label for="image">Imagem do Banner</label>
                <input type="file" name="image" id="image" class="form-control" required>
                <small class="form-text text-muted">A imagem já deve conter os textos desejados.</small>
            </div>

            <div class="form-group mb-3">
                <label for="link_url">Redirecionar para a Categoria (Opcional)</label>
                <select name="link_url" id="link_url" class="form-control">
                    <option value="">Nenhum link</option>
                    @foreach ($productTypes as $type)
                        <option value="{{ route('frontend.products.index', ['type' => $type->id]) }}">
                            Categoria: {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sort_order">Ordem de Exibição</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="0" required>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="is_active">Ativo?</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salvar Banner</button>
            <a href="{{ route('admin.hero-banners.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
