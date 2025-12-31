<h3>¡Hola, {{ $invitacion->profesor->name }}!</h3>
<p>El alumno <strong>{{ $alumno->name }}</strong> ({{ $alumno->email }}) ha aceptado tu invitación y ya forma parte de tu equipo.</p>
@if($invitacion->grupo)
    <p>Se ha unido automáticamente al grupo: <strong>{{ $invitacion->grupo->nombre }}</strong>.</p>
@endif
<p>Puedes ver su perfil y gestionar sus entrenamientos desde tu panel de control.</p>
<br>
<p>Saludos,<br>El equipo de TeamTracker</p>
