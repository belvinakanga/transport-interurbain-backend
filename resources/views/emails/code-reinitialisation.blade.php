<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code de réinitialisation Tokende</title>
</head>

<body style="margin:0; padding:0; background:#F4F5F8; font-family:Arial, Helvetica, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#F4F5F8; padding:32px 16px;">
    <tr>
        <td align="center">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:520px; background:#FFFFFF; border-radius:14px; overflow:hidden;">

                {{-- En-tête --}}
                <tr>
                    <td style="background:#0A2A66; padding:28px 32px;">
                        <div style="color:#FFFFFF; font-size:26px; font-weight:bold; letter-spacing:-0.5px;">
                            TOK<span style="color:#FF6B00;">ENDE</span>
                        </div>
                    </td>
                </tr>

                {{-- Corps --}}
                <tr>
                    <td style="padding:32px;">

                        <p style="margin:0 0 16px; font-size:16px; color:#0A2A66; font-weight:bold;">
                            Bonjour {{ $nomUtilisateur }},
                        </p>

                        <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#5B6682;">
                            Vous avez demandé à réinitialiser le mot de passe de votre compte
                            Tokende. Saisissez le code ci-dessous dans l'application.
                        </p>

                        {{-- Code --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center"
                                    style="background:#FFF3EB; border-radius:12px; padding:22px;">
                                    <div style="font-size:34px; font-weight:bold; color:#0A2A66;
                                                letter-spacing:10px;">
                                        {{ $code }}
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0; font-size:14px; line-height:1.6; color:#5B6682;">
                            Ce code expire dans {{ $minutesValidite }} minutes et ne peut servir
                            qu'une seule fois.
                        </p>

                        <p style="margin:16px 0 0; font-size:14px; line-height:1.6; color:#5B6682;">
                            Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail :
                            votre mot de passe reste inchangé.
                        </p>

                    </td>
                </tr>

                {{-- Pied --}}
                <tr>
                    <td style="padding:20px 32px; border-top:1px solid #E1E5F0;">
                        <p style="margin:0; font-size:12px; color:#8A93A8;">
                            Tokende &middot; Transport interurbain &middot; République du Congo
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>

</html>
