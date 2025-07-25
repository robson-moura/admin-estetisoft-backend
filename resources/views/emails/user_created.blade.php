<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao Sistema</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background: #f6f8fa; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 560px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 40px; box-sizing: border-box;">
                    
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <img src="{{ asset('logo.png') }}" alt="Logo do Sistema" style="height: 60px;">
                        </td>
                    </tr>

                    <!-- Saudação -->
                    <tr>
                        <td style="font-size: 24px; color: #222; font-weight: bold; text-align: center; padding-bottom: 16px;">
                            🎉 Bem-vindo, {{ $user->name }}!
                        </td>
                    </tr>

                    <!-- Mensagem -->
                    <tr>
                        <td style="font-size: 16px; color: #444; text-align: center; padding-bottom: 28px; line-height: 1.6;">
                            Sua conta foi criada com sucesso em nosso sistema.<br>
                            Para começar a usar, é necessário criar sua senha de acesso.
                        </td>
                    </tr>

                    <!-- Botão CTA -->
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
                            <a href="{{ $resetUrl }}" style="background-color: #007BFF; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 600; display: inline-block;">
                                Criar minha senha
                            </a>
                        </td>
                    </tr>

                    <!-- Aviso de segurança -->
                    <tr>
                        <td style="font-size: 13px; color: #888; text-align: center; line-height: 1.5;">
                            Se você não solicitou este cadastro, ignore este e-mail.<br>
                            Em caso de dúvidas, entre em contato com nossa equipe.
                        </td>
                    </tr>

                    <!-- Contato -->
                    <tr>
                        <td style="font-size: 13px; color: #666; text-align: center; padding-top: 8px;">
                            📞 (11) 1234-5678 &nbsp; | &nbsp; ✉️ contato@seusistema.com
                        </td>
                    </tr>

                    <!-- Rodapé -->
                    <tr>
                        <td style="font-size: 12px; color: #aaa; text-align: center; padding-top: 24px;">
                            &copy; {{ date('Y') }} Seu Sistema. Todos os direitos reservados.
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
