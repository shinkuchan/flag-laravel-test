@extends('layout')

@section('content')

<table class="table">
    <tr>
    <td>Список фильмов:</td><td> GET {{ Config::get('app.url') }}/api/movies</td>
    </tr>
    <tr>
        <td>Фильтр:</td><td> GET {{ Config::get('app.url') }}/api/movies?filter[]=1<br> 1 - Комедия, 2 - Ужасы</td>
    </tr>
    <tr>
        <td>Сортировка:</td><td> GET {{ Config::get('app.url') }}/api/movies?sort=name<br> GET {{ Config::get('app.url') }}/api/movies?sort=-name<br> Сортируется по id, name, release_date</td>
    </tr>
    <tr>
        <td>Постраничка:</td><td> GET {{ Config::get('app.url') }}/api/movies?page=2</td>
    </tr>
    <tr>
        <td>Добавить фильм:</td><td> POST {{ Config::get('app.url') }}/api/movies</td>
    </tr>
    <tr>
        <td>Информация о фильме:</td><td> GET {{ Config::get('app.url') }}/api/movies/{id}</td>
    </tr>
    <tr>
        <td>Редактировать фильм:</td><td> PUT {{ Config::get('app.url') }}/api/movies/{id}</td>
    </tr>
    <tr>
        <td>Удалить фильм:</td><td> DELETE {{ Config::get('app.url') }}/api/movies/{id}</td>
    </tr>
</table>

<hr>

<form method="POST" action="/api/movies" enctype="multipart/form-data">
    <h3>Добавить</h3>
    
    <div class="form-group">
        <label for="name">Название</label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="form-group">
        <label for="description">Описание</label>
        <textarea class="form-control" id="description" name="description"></textarea>
    </div>
    <div class="form-group">
        <label for="image">Картинка</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>
    <div class="form-group">
        <label for="release_date">Дата выхода (дд.мм.гггг)</label>
        <input type="text" class="form-control" id="release_date" name="release_date">
    </div>
    
    <div class="form-group">
        <label for="genres">Жанры</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="genres[]" id="genre1" value="1">
            <label class="form-check-label" for="genre1">Комедия</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="genres[]" id="genre2" value="2">
            <label class="form-check-label" for="genre2">Ужасы</label>
        </div>
    </div>
    
    <button type="submit">Добавить</button>
</form>

<hr>

<form id="edit_form" method="POST" action="/api/movies/" enctype="multipart/form-data">
    
    <h3>Редактировать</h3>
    
    <div class="form-group">
        <label for="name">ID</label>
        <input type="text" class="form-control" id="id" name="id">
    </div>
    
    <div class="form-group">
        <label for="name">Название</label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="form-group">
        <label for="description">Описание</label>
        <textarea class="form-control" id="description" name="description"></textarea>
    </div>
    <div class="form-group">
        <label for="image">Картинка</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>
    <div class="form-group">
        <label for="release_date">Дата выхода (дд.мм.гггг)</label>
        <input type="text" class="form-control" id="release_date" name="release_date">
    </div>
    
    <div class="form-group">
        <label for="genres">Жанры</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="genres[]" id="genre11" value="1">
            <label class="form-check-label" for="genre11">Комедия</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="genres[]" id="genre21" value="2">
            <label class="form-check-label" for="genre21">Ужасы</label>
        </div>
    </div>
    
    <button type="submit">Редактировать</button>
</form>

<hr>

<form id="delete_form" method="POST" action="/api/movies/" enctype="multipart/form-data">
    
    <h3>Удалить</h3>
    
    <div class="form-group">
        <label for="id2">ID</label>
        <input type="text" class="form-control" id="id2">
    </div>
    
    <button type="submit">Удалить</button>
</form>

@endsection

@section('scripts')

<script type="text/javascript">
$(document).ready(function(){
    $('#edit_form').on('change keyup keypress', '#id', function(){
        
        $('#edit_form').prop('action', '/api/movies/'+$('#edit_form #id').val());
    });
    
    $('#delete_form').on('change keyup keypress', '#id2', function(){
        
        $('#delete_form').prop('action', '/api/movies/'+$('#delete_form #id2').val());
    });
});
</script>

@endsection