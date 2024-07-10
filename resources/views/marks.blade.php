<x-app-layout>

<!-- Подключение необходимых пакетов -->
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

<!-- Подключение стилей -->
<style>
  .alert-message 
  {
    color: red;
  }

  section 
  {
    float: left;
    margin: 0 1.5%;
    width: 47%;
  }

  aside 
  {
    float: right;
    margin: 0 1.5%;
    width: 47%;
  }

  #map 
  {
    width: 100%;
    height: 700px;
  }

  h2 
  {
    margin-top: 0.83px;
    margin-bottom: 0.83px;
    margin: 0 2.5%; 
  }
</style>

<body>
<h2>  Добавьте метку! </h2>

<!-- Всплывающее окно для редактирования -->
<div class="modal fade" id="mark-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-name"></h4>
        </div>
        <div class="modal-body">
            <form name="userForm" class="form-horizontal">
               <input type="hidden" name="mark_id" id="mark_id">
                <div class="form-group">
                    <label for="name" class="col-sm-12">Название метки</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Введите название">
                        <span id="nameError" class="alert-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2">Долгота</label>
                    <div class="col-sm-12">
                        <textarea class="form-control" id="longitude" name="longitude" placeholder="Введите долготу" rows="4" cols="50">
                        </textarea>
                        <span id="longitudeError" class="alert-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2">Широта</label>
                    <div class="col-sm-12">
                        <textarea class="form-control" id="width" name="width" placeholder="Введите широту" rows="4" cols="50">
                        </textarea>
                        <span id="widthError" class="alert-message"></span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
        <button id="submitSave" class="btn btn-success">Сохранить</button> 
      </div>
    </div>
  </div>
</div>

<!-- Блок для отображения формы и таблицы -->
<section>
<!-- Форма добавления метки -->
<div class="form-container">
  <form id="addMarkForm">
    {{ csrf_field() }}
    <input type="hidden" name="mark_id" id="mark_id">
    <div class="form-group">
        <label for="name" class="col-sm-12">Название метки</label>
        <div class="col-sm-12">
            <input type="text" class="form-control" id="name" name="name" placeholder="Введите метку">
            <span id="nameError" class="alert-message"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="name" class="col-sm-2">Долгота</label>
        <div class="col-sm-12">
            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Введите долготу">
            <span id="nameError" class="alert-message"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="name" class="col-sm-2">Широта</label>
        <div class="col-sm-12">
            <input type="text" class="form-control" id="width" name="width" placeholder="Введите широту">
            <span id="nameError" class="alert-message"></span>
        </div>
    </div>

      <button id="submitAdd" class="btn btn-success">Добавить метку</button>
    </form>
  </div>
  
  <!-- Таблица меток -->
  <div class="table-container">
    <table id="list_marks" class="table table-striped table-bordered">
      <thead>
          <tr>
            <th>Название метки</th>
            <th>Долгота</th>
            <th>Широта</th>
            <th></th>
            <th></th>
          </tr>
      </thead>
      <tbody>
          @foreach($marks as $mark)
          <tr id="row_{{$mark->id}}">
            <td data-id="{{ $mark->id }}" onclick="setCenterOnMark(this)">{{ $mark->name }}</td>
            <td data-id="{{ $mark->id }}" onclick="setCenterOnMark(this)">{{ $mark->longitude }}</td>
            <td data-id="{{ $mark->id }}" onclick="setCenterOnMark(this)">{{ $mark->width }}</td>
            <td>
            <button data-id="{{ $mark->id }}" class="btn btn-success edit-button">Редактировать</button>
            </td>
            <td>
            <button data-id="{{ $mark->id }}" data-longitude="{{ $mark->longitude }}" data-width="{{ $mark->width }}" class="btn btn-success delete-button">Удалить</button>
            </td>
          </tr>
          @endforeach
      </tbody>
    </table>
  </div>
</section>
  
<!-- Блок для яндекс карты -->
  <aside>
    <div id="map" style="height: 400px"></div>
  </aside>
</body>
</x-app-layout>

<script>

var myMap; 
var marksCollection; 

// Инициализация коллекции меток
ymaps.ready(function () 
{
  marksCollection = new ymaps.GeoObjectCollection();
});

// Инициализация яндекс карты
ymaps.ready(function () 
{
    myMap = new ymaps.Map('map', {
        center: [55.75, 37.62],
        zoom: 10
    },
    {
      searchControlProvider: 'yandex#search'
    });
});

// Отображение меток на карте
ymaps.ready(init);
function init() 
{
  var geolocation = ymaps.geolocation;
     
  geolocation.get
  ({
      provider: 'browser',
      mapStateAutoApply: true
  }).then(function (result) 
  {
    result.geoObjects.options.set('preset', 'islands#blueCircleIcon');
    result.geoObjects.get(0).properties.set
    ({
        balloonContentBody: 'Мое местоположение'
    });

    marksCollection.add(result.geoObjects);
    
    @foreach(auth()->user()->marks as $mark)
    marksCollection.add(new ymaps.Placemark([{{$mark->longitude}}, {{$mark->width}}], 
      {
        balloonContent: '{{$mark->name}}',
        id: '{{$mark->id}}'
      }, 
      {
        preset: 'islands#icon',
        iconColor: '#0095b6'
      }));
    @endforeach  

    myMap.geoObjects.add(marksCollection);
  });
}

// Центрирование карты на метке
function setCenterOnMark(element) 
{
  const recordId = element.dataset.id;
  element.style.backgroundColor = "#e0e0e0"; 
  element.addEventListener("mouseout", function() 
  {
    element.style.backgroundColor = ""; 
  });  

  const longitude = document.querySelector(`#row_${recordId} td:nth-child(2)`).textContent;
  const latitude = document.querySelector(`#row_${recordId} td:nth-child(3)`).textContent;

  myMap.setCenter([longitude, latitude], 10);
}   

// Добавление метки на карту
function addCoordinate(id, name, longitude, width) 
{
  var marker = new ymaps.Placemark([longitude, width], {id: id, balloonContent: name});

  marksCollection.add(marker);
  myMap.geoObjects.add(marksCollection);
}

// Удаление метки с карты
function deleteCoordinate(id) 
{
  marksCollection.each(function (mark) 
  {
    var indexId = mark.properties.get("id");
    if (Number(indexId) === Number(id)) 
    {
      marksCollection.remove(mark);
    }
  });

  // Очищаем карту и добавляем обновленную коллекцию
  myMap.geoObjects.removeAll();
  myMap.geoObjects.add(marksCollection); 
}

$('#list_marks').DataTable();   

// Добавление метки
function addMark() 
{
  $("#mark_id").val('');
  $('#mark-modal').modal('show');
}

// Редактирование метки
$(document).ready(function() 
{
  $(document).on('click', '.edit-button', function(e) 
  {
    e.preventDefault();
    
    var id  = $(this).data("id");
    let _url = `/marks/${id}`;
    $('#nameError').text('');
    $('#longitudeError').text('');
    $('#widthError').text('');

    $.ajax(
      {
      url: _url,
      type: "GET",
      success: function(response) 
      {
          if(response) 
          {
            $("#mark_id").val(response.id);
            $("#name").val(response.name);
            $("#longitude").val(response.longitude);
            $("#width").val(response.width);
            $('#mark-modal').modal('show');
          }
        } 
      });
    });
});

// Удаление метки
$(document).ready(function() 
{
  $(document).on('click', '.delete-button', function(e) 
  {
    e.preventDefault();

    var id = $(this).data("id");
    let _url = `/marks/${id}`;

    if (confirm("Вы действительно хотите удалить эту метку?")) 
    {
      $.ajax(
        {
          url: _url, 
          type: 'DELETE',  
          data: 
          {
            _token: $('meta[name="csrf-token"]').attr('content')
          },

          success: function(response) 
          {
            $(`#row_${id}`).remove(); 
            deleteCoordinate(id);
          },
          
          error: function(error) 
          {
            console.error('Ошибка при удалении метки:', error);
          }
      });
    }
  });
});

// Добавление метки на форме
$(document).ready(function() 
{
  $('#submitAdd').click(function(e) 
  {
    e.preventDefault();

    const successAnswer = 200;
    var name = $('#name').val();
    var longitude = $('#longitude').val();
    var width = $('#width').val();
    var id = $('#mark_id').val();
    let _url     = `/marks`;
    let _token   = $('meta[name="csrf-token"]').attr('content');
    
    $.ajax(
      {
        url: _url,
        type: "post",
        dataType: "json",
        data: $('#addMarkForm').serialize(),

        success: function(response) 
        {
          if(response.code == successAnswer) 
          {
            if(id != "")
            {
              $("#row_"+id+" td:nth-child(3)").html(response.data.name);
              $("#row_"+id+" td:nth-child(4)").html(response.data.longitude);
              $("#row_"+id+" td:nth-child(5)").html(response.data.width);
            } 

            else 
            {
              const id = response.data.id;
              const name = response.data.name;
              const width = response.data.width;
              const longitude = response.data.longitude;
              addCoordinate(id, name, longitude, width);
              $('table tbody').prepend('<tr id="row_'+response.data.id+'"><td data-id="'+response.data.id+'" onclick="setCenterOnMark(this)">'+response.data.name+'</td><td data-id="'+response.data.id+'" onclick="setCenterOnMark(this)">'+response.data.longitude+'</td><td data-id="'+response.data.id+'" onclick="setCenterOnMark(this)">'+response.data.width+'</td><td><button data-id="'+response.data.id+'" class="btn btn-success edit-button">Редактировать</button></td><td><button data-id="'+response.data.id+'" data-longitude="'+response.data.longitude+'" data-width="'+response.data.width+'" class="btn btn-success delete-button">Удалить</button></td></tr>');
            }

            document.getElementById("addMarkForm").reset();
          }
        }
      });
    });
});

// Добавление метки во всплывающем окне
$(document).ready(function() 
{
  $('#submitSave').click(function(e) 
  {
    e.preventDefault();

    const successAnswer = 200;
    var id = $('#mark_id').val();
    deleteCoordinate(id);
    var name = $('#name').val();
    var longitude = $('#longitude').val();
    var width = $('#width').val();
    let _url = `/marks`;
    let _token = $('meta[name="csrf-token"]').attr('content');
    
    $.ajax(
      {
        url: _url,
        type: "POST",
        data: 
        {
          id: id,
          name: name,
          longitude: longitude,
          width: width,
          _token: _token 
        },
        success: function(response) 
        {
          if(response.code == successAnswer) 
          {
            if(id != "")
            {
              $("#row_"+id+" td:nth-child(1)").html(response.data.name);
              $("#row_"+id+" td:nth-child(2)").html(response.data.longitude);
              $("#row_"+id+" td:nth-child(3)").html(response.data.width);
              
              addCoordinate(id, response.data.name, response.data.longitude, response.data.width);
            } 
            else
            {
              const id = response.data.id;
              const name = response.data.name;
              const width = response.data.width;
              const longitude = response.data.longitude;
              
              // Добавление координаты на карту
              addCoordinate(id, name, longitude, width);
              $('table tbody').prepend('<tr id="row_'+response.data.id+'"><td>'+response.data.id+'</td><td>'+response.data.name+'</td><td>'+response.data.longitude+'</td><td>'+response.data.width+'</td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" onclick="editMark(event.target)" class="btn btn-info">Edit</a></td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" class="btn btn-danger" onclick="deleteMark(event.target)">Delete</a></td></tr>');
            }
          
            $('#name').val('');
            $('#longitude').val('');
            $('#width').val('');
            $('#mark-modal').modal('hide');
          }
        },
        error: function(response) 
        {
          $('#nameError').text(response.responseJSON.errors.name);
          $('#longitudeError').text(response.responseJSON.errors.longitude);
          $('#widthError').text(response.responseJSON.errors.width);
        }
      });
    });
});
</script>