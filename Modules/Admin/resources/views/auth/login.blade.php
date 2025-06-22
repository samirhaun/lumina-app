<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name') }} • Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Montserrat:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        /* --- VARIÁVEIS DE ESTILO --- */
        :root {
            --color-terracotta: #b35c3a;
            --color-gold: #D4AF37;
            --color-light-bg: #f8f9fa;
            /* Fundo principal da direita */
            --color-card-bg: #ffffff;
            /* Fundo do card */
            --color-dark-text: #4A2C2A;

            /* Usando a fonte Serifada para Títulos e uma Sans-serif limpa para o corpo */
            --font-serif-glamour: "Bodoni MT", "Cormorant Garamond", serif;
            --font-sans-body: "Montserrat", sans-serif;
        }

        /* --- ESTILOS GERAIS E DO WRAPPER --- */
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: var(--font-sans-body);
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            height: 100%;
            display: flex;
        }

        /* --- PAINEL ESQUERDO (Fundo Terracota, Logo Centralizado) --- */
        .login-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: var(--color-terracotta);
            color: var(--color-dark-text);
            /* Pode ser ajustado dependendo do logo */
            padding: 2rem;
            text-align: center;
        }

        @media(max-width: 991px) {
            .login-left {
                display: none;
                /* Oculta em telas menores se desejar */
            }
        }

        .login-left .logo-center {
            max-width: 1200px;
            /* <<< ALTERADO DE 200px para 350px */
            height: auto;
        }

        /* --- PAINEL DIREITO --- */
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 8rem 2rem;
            background: var(--color-light-bg);
            overflow-y: auto;
        }

        .logo-terracotta-top {
            max-width: 350px;
            /* <<< ALTERADO DE 120px para 150px */
            margin-bottom: 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--color-card-bg);
            border-radius: 1rem;
            border: 1px solid rgba(212, 175, 55, 0.4);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            transition: all 0.3s ease;
        }

        .login-card .card-title {
            font-family: var(--font-serif-glamour);
            font-weight: 600;
            font-size: 2.75rem;
            color: var(--color-dark-text);
            text-align: center;
            margin-bottom: 2.5rem;
        }

        /* --- NOVOS ESTILOS PARA OS CAMPOS DO FORMULÁRIO --- */
        .form-group {
            position: relative;
            margin-bottom: 2rem;
        }

        .form-input {
            width: 100%;
            border: 0;
            border-bottom: 1px solid #ccc;
            background: transparent;
            padding: 0.5rem 0.2rem;
            font-size: 1rem;
            color: var(--color-dark-text);
            transition: border-color 0.3s ease;
            border-radius: 0;
        }

        .form-input:focus {
            outline: none;
            border-bottom: 2px solid var(--color-gold);
            box-shadow: none;
        }

        .form-label {
            font-size: 0.85rem;
            color: var(--color-dark-text);
            opacity: 0.7;
        }

        /* Estilos para o "Lembrar-me" */
        .form-check-input {
            border-color: var(--color-gold);
        }

        .form-check-input:checked {
            background-color: var(--color-terracotta);
            border-color: var(--color-terracotta);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(224, 122, 95, 0.25);
        }

        /* --- NOVO BOTÃO DE LOGIN ESTILIZADO --- */
        .btn-glamour {
            background: var(--color-terracotta);
            color: #fff;
            border: none;
            padding: 0.9rem 1.5rem;
            width: 100%;
            font-weight: 500;
            font-family: var(--font-sans-body);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-glamour:hover {
            background: #c75e44;
            color: #fff;
            transform: translateY(-3px);
        }

        /* Mensagens de erro */
        .invalid-feedback {
            font-weight: 500;
        }

        .form-input.is-invalid,
        .form-input.is-invalid:focus {
            border-bottom: 2px solid #dc3545;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">

        <div class="login-left">
            <img src="{{ asset('images/lumina-logo.png') }}" alt="Logo Lúmina" class="logo-center">
        </div>

        <div class="login-right">
            <img src="{{ asset('images/logo-terracota.png') }}" alt="Logo Terracota" class="logo-terracotta-top">
            <div class="login-card">
                <h2 class="card-title">Acessar</h2>

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label for="username" class="form-label">Nome de Usuário</label> <input id="username"
                            name="username" type="text" class="form-input @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" required> @error('username')
                            <div class="invalid-feedback mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input id="password" name="password" type="password"
                            class="form-input @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Lembrar-me</label>
                    </div>

                    <button type="submit" class="btn-glamour">Entrar</button>

                    @if ($errors->has('username') || $errors->has('password'))
                        @unless ($errors->has('username') && $errors->has('password'))
                        @endunless
                    @endif
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
