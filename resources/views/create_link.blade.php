@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Referrer links. Create
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
                                    <label for="name">Caption</label>
                                    <input type="text" class="form-control" id="caption" name="caption" placeholder="" value="">
                                </div>
                            </div>
                        </div>

                        <a href="/" class="btn btn-outline-secondary btn-mg" role="button" aria-pressed="true">Back</a>
                        <button type="submit" class="btn btn-outline-primary">Create</button>
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
        });

    </script>
@endsection
