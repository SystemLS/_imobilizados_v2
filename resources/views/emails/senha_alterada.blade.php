@component('mail::message')
# Olá, {{ $nome }}

Sua senha no sistema **{{ config('app.name') }}** foi alterada com sucesso.

Se você não realizou esta alteração, entre em contato com o administrador imediatamente.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
