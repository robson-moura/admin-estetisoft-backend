<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $isUpdate ? 'Atendimento Atualizado' : 'Novo Atendimento' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background: #f6f8fa; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; margin: 0; padding: 0;">
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

                    <!-- Título -->
                    <tr>
                        <td style="font-size: 24px; color: #222; font-weight: bold; text-align: center; padding-bottom: 16px;">
                            {{ $isUpdate ? 'Atendimento Atualizado' : 'Novo Atendimento Agendado' }}
                        </td>
                    </tr>

                    <!-- Mensagem -->
                    <tr>
                        <td style="font-size: 16px; color: #444; text-align: center; padding-bottom: 28px; line-height: 1.6;">
                            Olá, <strong>{{ $appointment->client->full_name ?? 'Cliente' }}</strong>!<br>
                            @if($isUpdate)
                                Seu atendimento foi <strong>atualizado com sucesso</strong>.<br>
                                Caso tenha dúvidas ou precise alterar alguma informação, estamos à disposição.
                            @else
                                Ficamos felizes por você escolher a {{ config('app.name', 'nossa empresa') }}.<br>
                                Um novo atendimento foi agendado especialmente para você!
                            @endif
                        </td>
                    </tr>

                    <!-- Detalhes do Atendimento -->
                    <tr>
                        <td style="background: #f9fafc; border: 1px solid #e1e4e8; border-radius: 12px; padding: 24px; font-size: 15px; color: #333; text-align: left; line-height: 1.8;">
                            <div>📅 <strong>Data:</strong> {{ $appointment->date_br ?? $appointment->date }}</div>
                            <div>⏰ <strong>Hora:</strong> {{ $appointment->time }}</div>
                            <div>💆 <strong>Serviço:</strong> {{ $appointment->service->name ?? '-' }}</div>
                            <div>👤 <strong>Profissional:</strong> {{ $appointment->user->name ?? '-' }}</div>
                        </td>
                    </tr>

                    <!-- Botão CTA -->
                    <tr>
                        <td align="center" style="padding: 28px 0;">
                            <a href="{{ $appointmentLink ?? '#' }}" style="background-color: #007BFF; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-size: 15px;">
                                Visualizar Atendimento
                            </a>
                        </td>
                    </tr>

                    <!-- Aviso de Segurança -->
                    <tr>
                        <td style="font-size: 13px; color: #888; text-align: center; line-height: 1.5;">
                            Se você não reconhece este agendamento, entre em contato com nossa equipe.
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
