@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Реферальные ссылки
                    <a href="/create-link" class="float-md-right btn btn-outline-info">добавить ссылку</a>
                </div>
                <div class="card-body">
                    @if(count($referrer_links))
                        <div class="tree-view">
                            <table id="list_table" class="invisible display table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>ссылка</th>
                                    <th>описание</th>
                                    <th>кол-во</th>
                                    <th>кол-во уник.</th>
                                    <th>действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($referrer_links as $referrer_link)
                                    <tr>
                                        <td>
                                            {{$referrer_link['id']}}
                                        </td>
                                        <td>
                                            <a href="{{$referrer_link['link']}}">{{$referrer_link['link']}}</a>
                                        </td>
                                        <td>
                                            {{$referrer_link['caption']}}
                                        </td>
                                        <td>
                                            {{$referrer_link['count']}}
                                        </td>
                                        <td>
                                            {{$referrer_link['uniq_count']}}
                                        </td>
                                        <td>
                                            <a href="/links/{{$referrer_link['id']}}/delete">удалить</a>
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
