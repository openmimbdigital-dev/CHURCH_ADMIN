@php
    $code = '500';
    $title = 'Error del servidor';
    $heading = 'Algo salió mal';
    $message = 'Ocurrió un error inesperado en el servidor. Nuestro equipo ya puede estar trabajando en ello. Intenta de nuevo en unos momentos.';
@endphp

@include('errors.layout')
