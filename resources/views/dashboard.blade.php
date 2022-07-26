@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Referrer links
                    <a href="/create-link" class="float-md-right btn btn-outline-info">create link</a>
                </div>
                <div class="card-body">
                    @if(count($referrer_links))
                        <div class="tree-view">
                            <table id="list_table" class="invisible display table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>link</th>
                                    <th>caption</th>
                                    <th>count</th>
                                    <th>uniq count</th>
                                    <th>actions</th>
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
                                            <a href="/links/{{$referrer_link['id']}}/delete">delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        empty data
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
            list_table.DataTable();
            list_table.removeClass('invisible');
        });

    </script>
@endsection
