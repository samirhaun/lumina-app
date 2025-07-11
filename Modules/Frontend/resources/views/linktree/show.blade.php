{{-- resources/views/frontend/linktree/show.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['linktree_handle'] ?? 'Nossos Links' }} • Lúmina</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Gtv4rZ/3jacerfY3kO7Z7+8K1J3Qj1K5r7a+e0kC0qN2jO1fK5Ne3B8z6T9m1p8F" crossorigin="anonymous">

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;600&display=swap" rel="stylesheet">

<style>
  body {
    /* fundo geral (caso queira manter só no card, pode trocar por uma cor sólida aqui) */
    background: linear-gradient(135deg, #FDFAF6 0%, #FFEBD6 100%);
    font-family: 'Josefin Sans', sans-serif;
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }

  .linktree-container {
    width: 100%;
    max-width: 420px;
    padding: 1.5rem;
    text-align: center;

    /* aqui o degradê */
    background: linear-gradient(135deg, #FDFAF6 0%, #FFEBD6 100%);

    border-radius: .75rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .logo-img {
    width: 180px;
    max-width: 60%;
    margin-bottom: 1rem;
  }

  .handle {
    font-size: 1.75rem;
    font-weight: 600;
    color: #A0522D;
    margin-bottom: .5rem;
  }

  .bio {
    font-size: .95rem;
    color: #666;
    margin-bottom: 1.5rem;
  }

  /* botões principais */
  .links-list .link-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #fff;
    color: #A0522D;
    border: 2px solid #A0522D;
    border-radius: 50px;
    padding: .65rem 1rem;
    margin: .5rem 0;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all .2s ease-in-out;

    /* para criar profundidade 3D */
    box-shadow: 4px 4px 0 rgba(160,82,45, .3);
  }
  .links-list .link-btn i {
    margin-right: .5rem;
    font-size: 1.1rem;
  }
  .links-list .link-btn:hover {
    background-color: #A0522D;
    color: #fff;
    transform: translate(-2px,-2px);
    box-shadow: 2px 2px 0 rgba(160,82,45, .3);
  }

  /* ícones sociais */
  .socials-list {
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
  }
  .socials-list .social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.8rem;
    height: 2.8rem;
    border-radius: 50%;
    background-color: #fff;
    color: #A0522D;
    font-size: 1.5rem;
    transition: background-color .2s, color .2s, transform .2s;
    box-shadow: 2px 2px 0 rgba(160,82,45, .3);
  }
  .socials-list .social-icon:hover {
    background-color: #A0522D;
    color: #fff;
    transform: translate(-2px,-2px) scale(1.1);
  }
</style>
</head>

<body>
    <div class="linktree-container">
        {{-- Logo --}}
        <img src="{{ asset('images/logo-terracota.png') }}" alt="Lúmina" class="logo-img img-fluid">

        {{-- Handle --}}
        <div class="handle">
            {{ $settings['linktree_handle'] ?? '@SeuUsuario' }}
        </div>

        {{-- Bio --}}
        <div class="bio">
            {{ $settings['linktree_bio'] ?? 'Entre em contato usando um dos links abaixo' }}
        </div>

        {{-- Links --}}
        <div class="links-list">
            @forelse($links as $link)
                <a href="{{ $link->url }}" target="_blank" class="link-btn">
                    @if ($link->icon_class)
                        <i class="{{ $link->icon_class }}"></i>
                    @endif
                    <span>{{ $link->title }}</span>
                </a>
            @empty
                <p class="text-muted">Nenhum link disponível no momento.</p>
            @endforelse
        </div>

        {{-- 4) Redes Sociais --}}
        <div class="socials-list">
            @foreach ($socials as $s)
                <a href="{{ $s->url }}" target="_blank" class="social-icon">
                    <i class="{{ $s->icon_class }}"></i>
                </a>
            @endforeach
        </div>
    </div>
</body>

</html>
