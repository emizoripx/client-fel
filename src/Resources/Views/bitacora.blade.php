<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <title>Document</title>

</head>

<body style="font-size: 14px;">

  <div class="container">
    <h3>Bitacora de Client Fel Library</h3>
    <table class="table table-striped" style="table-layout: fixed; width: 100%">
      <thead>
        <tr>
          <th width="80px">#</th>
          <th width="80px">Tipo</th>
          <th width="200px">Fecha</th>
          <th>Message</th>
          <th>Evento</th>
        </tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        <tr>
          <td>{!! $log->id !!}</td>
          <td>{!! $log->button_type !!}</td>
          <td> {{$log->created_at}} </td>
          <td style="overflow-x: scroll; white-space: nowrap;"> {{ $log->message}} </td>
          <td> {{ $log->event}} </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $logs->links() }}

  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>