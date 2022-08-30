@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Действия пользователей
                </div>
                <div class="card-body">
                    @if(count($tg_users))
                        <div class="tree-view">
                            <table id="list_table" class="invisible display table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>tg id</th>
                                    <th>профиль</th>
                                    <th>имя</th>
                                    <th>фамилия</th>
{{--                                    <th>телефон</th>--}}
                                    <th>действие</th>
                                    <th>дата</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tg_users as $tg_user)
                                    <tr>
                                        <td>
                                            {{$tg_user['id']}}
                                        </td>
                                        <td>
                                            {{$tg_user['tg_id']}}
                                        </td>
                                        <td>
                                            {{$tg_user['username']}}
                                        </td>
                                        <td>
                                            {{$tg_user['first_name']}}
                                        </td>
                                        <td>
                                            {{$tg_user['last_name']}}
                                        </td>
{{--                                        <td>--}}
{{--                                            {{$tg_user['phone']}}--}}
{{--                                        </td>--}}
                                        <td>
                                            {{$tg_user['last_action']}}
                                        </td>
                                        <td>
                                            {{$tg_user['created_at']}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        данные отсутствуют
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">

        $(document).ready(function () {
            var list_table = $('#list_table');
            list_table.DataTable( {
                "language": {
                    "sProcessing":    "Процесс ...",
                    "sLengthMenu":    "Показать записи _MENU_",
                    "sZeroRecords":   "По выбранным параметрам результатов не найдено",
                    "sEmptyTable":    "Данные отсутствуют в таблице",
                    "sInfo":          "Показаны записи с _START_ по _END_ из _TOTAL_",
                    "sInfoEmpty":     "Показано с 0 по 0 из 0 записей",
                    "sInfoFiltered":  "отфильтровано из _MAX_ записей",
                    "sInfoPostFix":   "",
                    "sSearch":        "Поиск",
                    "sUrl":           "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Загрузка ...",
                    "oPaginate": {
                        "sFirst":       "Первый",
                        "sLast":        "Прошлой",
                        "sNext":        "назад",
                        "sPrevious":    "вперед"
                    },
                    "oAria": {
                        "sSortAscending":  "Сортировать по возрастанию",
                        "sSortDescending": "Сортировать по убыванию"
                    }
                },
            });
            list_table.removeClass('invisible');
        });

    </script>
@endsection
