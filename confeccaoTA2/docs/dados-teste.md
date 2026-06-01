# Dados de teste

Após executar `php artisan migrate:fresh --seed`, o sistema terá usuários, cargos, permissões, clientes, produtos, pedidos, itens e registros da Caixa de E-mail.

Usuários de login:

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador Sistema | admin@sistema.com | password |
| Gerente Vendas | gerente@sistema.com | password |
| Vendedor Sistema | vendedor@sistema.com | password |
| Usuário Teste | usuario@sistema.com | password |
| Cliente Teste | cliente@sistema.com | password |
| Fornecedor Teste | fornecedor@sistema.com | password |

O dashboard fica populado com pedidos em todos os status usados pelo sistema: `Pendente`, `Em Produção`, `Finalizado` e `Cancelado`.

A Caixa de E-mail fica populada com registros de pedidos finalizados, incluindo envios com sucesso, pendente e erro simulado.

Em ambiente local com `APP_DEBUG=true`, a tela de login do Filament exibe a seção **Acessos rápidos para teste**. Ao clicar em Administrador, Cliente ou Fornecedor, o formulário recebe o e-mail correspondente e a senha `password`, sem enviar o login automaticamente.
