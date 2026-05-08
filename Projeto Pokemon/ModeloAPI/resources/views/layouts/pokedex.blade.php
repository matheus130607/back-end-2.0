{{-- resources/views/layouts/pokedex.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>@yield('title', 'Pokédex')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', 'VT323', monospace;
            background-image: url('https://images.unsplash.com/photo-1448375240586-882707db888b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 0;
        }

        /* Logo do Pokémon */
        .logo-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }

        .pokemon-logo {
            width: 280px;
            height: auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
            transition: transform 0.3s ease;
        }

        .pokemon-logo:hover {
            transform: scale(1.05);
        }

        /* Container principal */
        .main-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding: 40px 20px;
        }

        /* Mensagens de notificação */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            padding: 15px 25px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            animation: slideInRight 0.5s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            cursor: pointer;
        }

        .alert-success {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-left: 5px solid #ffd700;
        }

        .alert-error {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            border-left: 5px solid #ffeb3b;
        }

        .alert-info {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            border-left: 5px solid #ffd700;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        /* Botão flutuante de voltar ao topo */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            background: #e33535;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 24px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            border: 2px solid #aa2222;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
            background: #cc0000;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            margin-top: 40px;
        }

        /* Loading spinner */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2000;
            background: rgba(0,0,0,0.8);
            padding: 20px;
            border-radius: 15px;
            display: none;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #e33535;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .pokemon-logo {
                width: 200px;
            }
            
            .main-container {
                padding: 80px 15px 20px;
            }
            
            .alert {
                top: auto;
                bottom: 20px;
                right: 20px;
                left: 20px;
                padding: 12px 20px;
                font-size: 0.9rem;
            }
            
            .back-to-top {
                bottom: 80px;
                right: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Logo do Pokémon -->
    <div class="logo-container">
        <a href="{{ route('pokedex.lista') }}" title="Voltar para Pokedex">
            <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/International_Pok%C3%A9mon_logo.svg" 
                 alt="Pokémon Logo" 
                 class="pokemon-logo">
        </a>
    </div>

    <!-- Container principal -->
    <div class="main-container">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>© 2024 Pokédex - Todos os Pokémon são propriedade da Nintendo, Game Freak e The Pokémon Company</p>
        <p style="margin-top: 5px;">✨ Pokémon customizados criados por você serão salvos no seu banco de dados ✨</p>
    </div>

    <!-- Botão voltar ao topo -->
    <a href="#" class="back-to-top" id="backToTop">↑</a>

    <!-- Loading spinner -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
        <span>Carregando...</span>
    </div>

    <script>
        // Fechar alertas automaticamente após 5 segundos
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
            
            // Clicar para fechar
            alert.addEventListener('click', () => {
                alert.style.animation = 'fadeOut 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            });
        });

        // Botão voltar ao topo
        const backToTop = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
        
        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Função para mostrar loading
        window.showLoading = function() {
            document.getElementById('loading').style.display = 'flex';
        };
        
        window.hideLoading = function() {
            document.getElementById('loading').style.display = 'none';
        };

        // Previne submit duplicado em formulários
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Processando...';
                }
                showLoading();
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
