# Teste de e-mails com Mailpit

O projeto está configurado para enviar e-mails via SMTP local em ambiente de desenvolvimento.

Configuração esperada no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="sistema@meusistema.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Como testar:

1. Rode o Mailpit localmente e acesse a interface web dele.
2. Crie ou edite um pedido no painel Filament.
3. Garanta que o cliente selecionado tenha e-mail cadastrado.
4. Altere o status do pedido para `Finalizado` e salve.
5. Verifique o e-mail recebido no Mailpit e o registro em `Vendas > Caixa de E-mail`.

Se o cliente não tiver e-mail cadastrado, o sistema registra a tentativa como `sem_email` e não quebra o fluxo de salvamento do pedido.
