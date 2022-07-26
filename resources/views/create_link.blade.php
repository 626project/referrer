@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Реферальные ссылки. Добавление
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="/create-link">
                        @csrf

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                                <div class="form-group">
                                    <label for="label">
                                        Метка (используется для создания ссылки. разрешенные символы: <b>a-z</b>, <b>A-Z</b>, <b>0-9</b>, <b>_</b>)
                                    </label>
                                    <input type="text" class="form-control" id="label" name="label" placeholder="" value="">
                                </div>
                            </div>
                        </div>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                                <div class="form-group">
                                    <label for="caption">Описание</label>
                                    <input type="text" class="form-control" id="caption" name="caption" placeholder="" value="">
                                </div>
                            </div>
                        </div>

                        <a href="/" class="btn btn-outline-secondary btn-mg" role="button" aria-pressed="true">Вернутся на список</a>
                        <button type="submit" class="btn btn-outline-primary">Добавить</button>
                    </form>
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
            list_table.DataTable();
            list_table.removeClass('invisible');

            const label_regexp = /[^\d_a-zA-Z]+/;
            $('#label').on('keyup', function (event) {
                const label_element = $(this);
                const new_value = label_element.val().replace(label_regexp, '');
                label_element.val(new_value);
            });
        });

    </script>
@endsection
