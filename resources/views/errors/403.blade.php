@php
    $code = '403';
    $title = 'Acceso denegado';
    $heading = 'No tienes permisos';
    $message = $message ?? 'No tienes permisos para acceder a esta funcionalidad. Si crees que es un error, contacta al administrador del sistema.';
@endphp

@include('errors.layout')
