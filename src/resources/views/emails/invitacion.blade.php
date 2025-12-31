<h3>Has sido invitado por {{ $invitacion->profesor->name }}</h3>
@if($existeUsuario)
    <p>Has sido invitado a unirte a su grupo. Puedes aceptar la invitación haciendo clic en el siguiente enlace:</p>
    <a href="{{ route('invitaciones.aceptar', ['token' => $invitacion->token]) }}">Aceptar invitación</a>
@else
    <p>Has sido invitado a unirte a TeamTracker. Regístrate para unirte automáticamente a su grupo:</p>
    <a href="{{ route('signup', ['invitation_token' => $invitacion->token]) }}">Registrarse y unirse</a>
@endif
