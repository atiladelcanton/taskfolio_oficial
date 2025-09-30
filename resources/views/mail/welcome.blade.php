<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Taskfolio</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f7;">
<table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f4f4f7; padding: 40px 0;">
    <tr>
        <td align="center">
            <!-- Container Principal -->
            <table role="presentation" style="width: 100%; max-width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                <!-- Header com Logo -->
                <tr>
                    <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
                        <!-- Logo do Taskfolio -->
                        <div style="background-color: #ffffff; width: 220px; padding:15px;   margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                            <img src="{{asset('images/logo.png')}}" alt="TaskFolio" />
                        </div>
                    </td>
                </tr>

                <!-- Corpo do Email -->
                <tr>
                    <td style="padding: 40px 30px;">
                        <!-- Saudação -->
                        <h2 style="color: #2d3748; font-size: 28px; font-weight: 700; margin: 0 0 20px 0; text-align: center;">
                            🎉 Bem-vindo(a) ao Taskfolio!
                        </h2>

                        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0; text-align: center;">
                            Olá, <strong style="color: #667eea;">{{ $name }}</strong>!
                        </p>

                        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                            É com grande satisfação que te recebemos na nossa plataforma de gestão de projetos!
                            Estamos muito felizes em tê-lo(a) conosco. 🚀
                        </p>

                        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;">
                            O Taskfolio foi desenvolvido para simplificar a gestão dos seus projetos, colaboradores
                            e pagamentos, tudo em um único lugar. Prepare-se para uma experiência incrível!
                        </p>

                        <!-- Card de Credenciais -->
                        <div style="background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%); border-radius: 12px; padding: 30px; margin: 30px 0; border-left: 4px solid #667eea;">
                            <h3 style="color: #2d3748; font-size: 18px; font-weight: 700; margin: 0 0 20px 0; display: flex; align-items: center;">
                                <span style="background-color: #667eea; color: white; width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 16px;">🔑</span>
                                Seus Dados de Acesso
                            </h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #cbd5e0;">
                                        <strong style="color: #4a5568; font-size: 14px; display: block; margin-bottom: 5px;">
                                            📧 Email:
                                        </strong>
                                        <span style="color: #2d3748; font-size: 16px; font-family: 'Courier New', monospace; background-color: #ffffff; padding: 8px 12px; border-radius: 6px; display: inline-block;">
                                                {{ $email }}
                                            </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <strong style="color: #4a5568; font-size: 14px; display: block; margin-bottom: 5px;">
                                            🔐 Senha Temporária:
                                        </strong>
                                        <div style="background-color: #fff3cd; border: 2px dashed #ffc107; border-radius: 8px; padding: 15px; text-align: center; margin-top: 8px;">
                                                <span style="color: #856404; font-size: 24px; font-weight: 700; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                                                    {{ $password }}
                                                </span>
                                        </div>
                                        <p style="color: #dc3545; font-size: 13px; margin: 10px 0 0 0; font-weight: 600;">
                                            ⚠️ Por segurança, altere sua senha no primeiro acesso!
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Botão de Acesso -->
                        <div style="text-align: center; margin: 35px 0;">
                            <a href="{{ $loginUrl }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; font-size: 18px; font-weight: 700; text-decoration: none; padding: 16px 48px; border-radius: 50px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); transition: all 0.3s ease;">
                                Acessar Painel →
                            </a>
                        </div>

                        <!-- Informações Adicionais -->
                        <div style="background-color: #e6f7ff; border-radius: 8px; padding: 20px; margin: 30px 0;">
                            <h4 style="color: #0066cc; font-size: 16px; font-weight: 700; margin: 0 0 15px 0;">
                                💡 Primeiros Passos
                            </h4>
                            <ul style="color: #4a5568; font-size: 14px; line-height: 1.8; margin: 0; padding-left: 20px;">
                                <li>Faça login e altere sua senha temporária</li>
                                <li>Complete seu perfil com suas informações</li>
                            </ul>
                        </div>

                        <!-- Mensagem de Suporte -->
                        <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0; text-align: center;">
                            Precisa de ajuda? Nossa equipe está à disposição!<br>
                            <a href="mailto:suporte@taskfolio.com" style="color: #667eea; text-decoration: none; font-weight: 600;">
                                suporte@taskfolio.com
                            </a>
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #2d3748; padding: 30px 20px; text-align: center;">
                        <p style="color: #a0aec0; font-size: 14px; line-height: 1.6; margin: 0 0 15px 0;">
                            Taskfolio - Gestão de Projetos Simplificada
                        </p>



                        <p style="color: #718096; font-size: 12px; margin: 15px 0 0 0;">
                            © 2025 Taskfolio. Todos os direitos reservados.<br>
                            Este é um email automático, por favor não responda.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
