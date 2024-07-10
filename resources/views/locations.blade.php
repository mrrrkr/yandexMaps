<x-app-layout>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://api-maps.yandex.ru/2.1/?apikey=5af9222a-d741-4252-90b2-8c78db8f3b4e&lang=ru_RU" type="text/javascript"></script>

</head>

<style>
    h1 
    {
        margin-top: 0.83px;
        margin-bottom: 0.83px;
        margin: 0 2.5%; 
    }
</style>

<body>
    <h1>Список локаций</h1>

    <table id="locations" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Название метки</th>
                <th>Долгота</th>
                <th>Широта</th>
            </tr>
        </thead>
        <tbody>
        @foreach($marks as $mark)
        <tr>
        <td>{{ $mark->name }}</td>
        <td>{{ $mark->longitude }}</td>
        <td>{{ $mark->width }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</body>
</x-app-layout>