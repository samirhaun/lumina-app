@extends('frontend::layouts.layout')
@section('title', 'Finalizar Compra')
@push('styles')
    {{-- O CSS que estava aqui foi movido para store.css --}}
@endpush

@section('content')
    <div class="container my-5">
        <div class="hero-section bg-light rounded-4 shadow-sm text-center py-5 position-relative overflow-hidden">
            {{-- Bolhas de cor de fundo --}}
            <span class="bg-terracota rounded-circle position-absolute"
                style="width:200px; height:200px; top:-50px; left:calc(50% - 100px); opacity:0.08;"></span>

            <h1 class="display-4 fw-bold text-terracota mb-3">Finalize sua compra</h1>
            <p class="lead text-muted">
                Quase lá! Preencha seus dados para concluir o pedido.
            </p>
        </div>
    </div>

    <form id="checkoutForm" class="needs-validation" novalidate>
        @csrf
        <div class="row g-5 mt-4">
            {{-- Coluna Esquerda: Formulários --}}
            <div class="col-lg-7">
                {{-- ETAPA 1: DADOS PESSOAIS --}}
                <div class="checkout-step">
                    <div class="checkout-step-header">
                        <div class="step-number">1</div>
                        <h4 class="mb-0">Seus Dados</h4>
                    </div>
                    <div class="row g-3">
                        <div class="col-12"><label for="customer_name" class="form-label">Nome Completo</label><input
                                type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="col-12"><label for="customer_phone" class="form-label">WhatsApp (com
                                DDD)</label><input type="tel" class="form-control" id="customer_phone"
                                name="customer_phone" placeholder="Ex: (38)99999-9999" required></div>
                    </div>
                </div>

                {{-- ETAPA 2: ENTREGA --}}
                <div class="checkout-step">
                    <div class="checkout-step-header">
                        <div class="step-number">2</div>
                        <h4 class="mb-0">Entrega</h4>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="radio" name="delivery_method" value="delivery" id="delivery"
                                class="choice-card-input" checked required>
                            <label class="choice-card" for="delivery"><strong><i class="fas fa-truck me-2"></i>Receber
                                    em casa</strong>
                                <p>Preencha seu endereço de entrega.</p>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" name="delivery_method" value="pickup" id="pickup"
                                class="choice-card-input" required>
                            <label class="choice-card" for="pickup"><strong><i class="fas fa-store me-2"></i>Retirar
                                    no local</strong>
                                <p>Busque seu pedido sem custo.</p>
                            </label>
                        </div>
                    </div>

                    {{-- Formulário de Endereço --}}
                    <div id="deliveryAddressForm" class="mt-4">
                        <div class="row g-3">
                            {{-- Campos de endereço como estavam antes --}}
                            {{-- CEP --}}
                            <div class="col-sm-6">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep" name="cep">
                                <div class="invalid-feedback">Por favor, informe seu CEP.</div>
                            </div>

                            {{-- Endereço --}}
                            <div class="col-12">
                                <label for="street" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="street" name="street">
                                <div class="invalid-feedback">Por favor, informe seu endereço.</div>
                            </div>

                            {{-- Número --}}
                            <div class="col-sm-4">
                                <label for="number" class="form-label">Número</label>
                                <input type="text" class="form-control" id="number" name="number">
                                <div class="invalid-feedback">Por favor, informe o número.</div>
                            </div>

                            {{-- Complemento --}}
                            <div class="col-sm-8">
                                <label for="complement" class="form-label">
                                    Complemento
                                    <span class="text-muted">(Opcional)</span>
                                </label>
                                <input type="text" class="form-control" id="complement" name="complement">
                                <div class="invalid-feedback">
                                    Por favor, informe o complemento.
                                </div>
                            </div>

                            {{-- Bairro --}}
                            <div class="col-sm-6">
                                <label for="neighborhood" class="form-label">Bairro</label>
                                <input type="text" class="form-control" id="neighborhood" name="neighborhood">
                                <div class="invalid-feedback">Por favor, informe o bairro.</div>
                            </div>

                            {{-- Cidade --}}
                            <div class="col-sm-4">
                                <label for="city" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="city" name="city">
                                <div class="invalid-feedback">Por favor, informe a cidade.</div>
                            </div>

                            {{-- UF --}}
                            <div class="col-sm-2">
                                <label for="state" class="form-label">UF</label>
                                <input type="text" class="form-control" id="state" name="state"
                                    maxlength="2">
                                <div class="invalid-feedback">Por favor, informe a UF.</div>
                            </div>
                        </div>
                    </div>
                    {{-- Informação de Retirada --}}
                    <div id="pickupAddressInfo" class="alert alert-secondary mt-4" style="display: none;">
                        <strong>Endereço para Retirada:</strong><br>
                        Avenida Mestra Fininha, 3890, Ap 202<br>
                        Bairro Augusta Mota, Montes Claros - MG
                        <small class="d-block mt-2">Após finalizar, aguarde o contato para agendar o melhor
                            horário.</small>
                    </div>
                </div>

                {{-- ETAPA 3: PAGAMENTO --}}
                <div class="checkout-step">
                    <div class="checkout-step-header">
                        <div class="step-number">3</div>
                        <h4 class="mb-0">Pagamento</h4>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="radio" name="payment_method" value="Cartão de Crédito" id="credit_card"
                                class="choice-card-input" checked required>
                            <label class="choice-card" for="credit_card"><strong><i
                                        class="far fa-credit-card me-2"></i>Cartão de Crédito</strong>
                                <p>Pagamento a ser combinado.</p>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="radio" name="payment_method" value="Pix" id="pix"
                                class="choice-card-input" required>
                            <label class="choice-card" for="pix"><strong><i
                                        class="fas fa-qrcode me-2"></i>Pix</strong>
                                <p>A chave será enviada por WhatsApp.</p>
                            </label>
                        </div>
                    </div>
                    {{-- Opções de Parcela --}}
                    <div class="row mt-3" id="installmentsSection">
                        <div class="col-md-7">
                            <label for="installments" class="form-label">Opções de Parcelamento</label>
                            <select class="form-select" id="installments" name="installments">
                                <option value="1x sem juros">1x sem juros</option>
                                <option value="2x sem juros">2x sem juros</option>
                                <option value="3x sem juros">3x sem juros</option>
                                <option value="4x (taxa a calcular)">4x (taxa a calcular)</option>
                                <option value="5x (taxa a calcular)">5x (taxa a calcular)</option>
                                <option value="6x (taxa a calcular)">6x (taxa a calcular)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna Direita: Resumo do Pedido --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-terracota text-black d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                        <span class="badge bg-white text-terracota rounded-pill">{{ Cart::count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
@foreach ($cartItems as $item)
    <li class="list-group-item d-flex align-items-center px-3 py-2">
        @php
            $imgUrl = $productImages[$item->id][0]->image_url ?? asset('images/placeholder.png');
            $code   = $codesByProduct[$item->id] ?? '';
        @endphp

        <img src="{{ $imgUrl }}" alt="{{ $item->name }}" class="rounded me-3"
             style="width:50px;height:50px;object-fit:cover;">

        <div class="flex-fill">
            <h6 class="mb-1">
                {{ $item->name }}
                @if($code)
                    <span class="badge bg-secondary ms-2">Código: {{ $code }}</span>
                @endif
            </h6>
            <small class="text-muted">Qtd: {{ $item->qty }}</small>
        </div>

        <span class="fw-semibold">
            R$ {{ number_format((float) $item->subtotal,2,',','.') }}
        </span>
    </li>
@endforeach
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center px-3 py-3 bg-light">
                                <span class="fw-bold">Total</span>
                                <span class="fs-5 fw-bold">R$ {{ number_format((float) $total, 2, ',', '.') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <hr class="my-4 border-0" style="height:1px; background: rgba(0,0,0,0.1);">

        <div class="text-center py-4">
            <button class="btn btn-terracota btn-lg px-5 py-3 position-relative overflow-hidden" type="submit">
                <span class="btn-bg position-absolute top-0 start-0 w-100 h-100"></span>
                <i class="fab fa-whatsapp me-2"></i>
                Finalizar e Chamar no WhatsApp
            </button>
        </div>
    </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function() {
            // máscaras
            $('#cep').mask('00000-000');
            $('#customer_phone').mask('(00) 00000-0000');

            const deliveryForm = $('#deliveryAddressForm');
            const pickupInfo = $('#pickupAddressInfo');
            const installmentsSection = $('#installmentsSection');

            // Mapa de traduções
            const validationMessagesPT = {
                customer_name: 'Por favor, informe seu nome completo.',
                customer_phone: 'Por favor, informe seu WhatsApp com DDD.',
                cep: 'O CEP é obrigatório quando o método de entrega é “Receber em casa”.',
                street: 'O endereço é obrigatório quando o método de entrega é “Receber em casa”.',
                number: 'O número é obrigatório quando o método de entrega é “Receber em casa”.',
                neighborhood: 'O bairro é obrigatório quando o método de entrega é “Receber em casa”.',
                city: 'A cidade é obrigatória quando o método de entrega é “Receber em casa”.',
                state: 'A UF é obrigatória quando o método de entrega é “Receber em casa”.'
            };

            function toggleForms() {
                const deliveryMethod = $('input[name="delivery_method"]:checked').val();
                const paymentMethod = $('input[name="payment_method"]:checked').val();
                const formEl = document.querySelector('#checkoutForm');

                // Ao mudar qualquer método, limpa toda validação já exibida
                formEl.classList.remove('was-validated');
                // limpa as classes de validação de todos os campos de endereço
                $('#cep, #street, #number, #complement, #neighborhood, #city, #state')
                    .removeClass('is-invalid is-valid');

                // ENTREGA vs RETIRADA
                if (deliveryMethod === 'delivery') {
                    deliveryForm.slideDown();
                    pickupInfo.slideUp();
                    $('#cep, #street, #number, #complement, #neighborhood, #city, #state')
                        .attr('required', true);
                } else {
                    deliveryForm.slideUp();
                    pickupInfo.slideDown();
                    $('#cep, #street, #number, #complement, #neighborhood, #city, #state')
                        .removeAttr('required');
                }

                // Parcelamento
                if (paymentMethod === 'Cartão de Crédito') {
                    installmentsSection.slideDown();
                } else {
                    installmentsSection.slideUp();
                }
            }
            // dispara toggle ao mudar radio e na carga inicial
            $('input[name="delivery_method"], input[name="payment_method"]')
                .on('change', toggleForms);
            toggleForms();

            // API ViaCEP
            $('#cep').on('blur', function() {
                const cep = $(this).val().replace(/\D/g, '');
                if (cep.length !== 8) return;
                $('#street, #neighborhood, #city, #state')
                    .val('Buscando...')
                    .prop('disabled', true);

                $.getJSON(`https://viacep.com.br/ws/${cep}/json/`)
                    .done(data => {
                        if (!data.erro) {
                            $('#street').val(data.logradouro);
                            $('#neighborhood').val(data.bairro);
                            $('#city').val(data.localidade);
                            $('#state').val(data.uf);
                            $('#number').focus();
                        } else {
                            Swal.fire('CEP não encontrado',
                                'Por favor, preencha o endereço manualmente.', 'warning'
                            );
                            $('#street, #neighborhood, #city, #state').val('');
                        }
                    })
                    .fail(() => {
                        Swal.fire('Erro na Consulta',
                            'Não foi possível buscar o CEP. Preencha manualmente.', 'error'
                        );
                    })
                    .always(() => {
                        $('#street, #neighborhood, #city, #state').prop('disabled', false);
                    });
            });

            // Validação Bootstrap
            (function() {
                'use strict';
                const form = document.querySelector('#checkoutForm');
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        form.classList.add('was-validated');
                    }
                }, false);
            })();

            // Envio via AJAX com mensagens traduzidas
            $('#checkoutForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = form.find('button[type="submit"]');
                const originalHtml = btn.html();

                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span> Processando...');

                $.ajax({
                    url: "{{ route('frontend.checkout.place-order') }}",
                    type: 'POST',
                    data: form.serialize(),
                    success(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pedido Registrado!',
                                text: 'Você será redirecionado para o WhatsApp.',
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href = response.whatsapp_url;
                            });
                        }
                    },
                    error(xhr) {
                        btn.prop('disabled', false).html(originalHtml);
                        if (xhr.status === 422) {
                            let html = '<ul class="text-start">';
                            Object.entries(xhr.responseJSON.errors).forEach(([field, msgs]) => {
                                const custom = validationMessagesPT[field];
                                html += `<li>${ custom || msgs[0] }</li>`;
                            });
                            html += '</ul>';
                            Swal.fire({
                                title: 'Dados Inválidos',
                                html,
                                icon: 'error'
                            });
                        } else {
                            Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
