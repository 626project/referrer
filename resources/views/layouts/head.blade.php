    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js"></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('style')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/ju/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sl-1.3.1/datatables.min.css"/>

    <style>
        a.active {
            font-weight: bold;
        }
        .vertical-menu {
            width: 100% /* Set a width if you like */
        }

        .vertical-menu a {
            background-color: #eee; /* Grey background color */
            color: black; /* Black text color */
            display: block; /* Make the links appear below each other */
            padding: 12px; /* Add some padding */
            text-decoration: none; /* Remove underline from links */
        }

        .vertical-menu a:hover {
            background-color: #ccc; /* Dark grey background on mouse-over */
        }

        .vertical-menu a.active {
            background-color: #4380D3; /* Add a green color to the "active/current" link */
            color: white;
        }

        .nowrap {
            white-space: nowrap !important;
        }




        table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
            padding-right: 30px;
        }
        table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled {
            cursor: pointer;
            position: relative;
        }
        table.dataTable td, table.dataTable th {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
        }

        @font-face{
            font-family:'Glyphicons Halflings';
            src:url(https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.eot);
            src:url(https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.eot?#iefix)
            format('embedded-opentype'),url(https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.woff2)
            format('woff2'),
            url(https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.woff)
            format('woff'),
            url(https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.ttf)
            format('truetype'),
            url(https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular)
            format('svg')
        }

        td.details-control {
            background: url('/img/details_open.png') no-repeat center center;
            cursor: pointer;
        }
        tr.shown td.details-control {
            background: url('/img/details_close.png') no-repeat center center;
        }

        table.dataTable thead .sorting:after {
            opacity: 0.2;
            content: "\e150";
        }
        table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after {
            position: absolute;
            bottom: 8px;
            right: 8px;
            display: block;
            font-family: 'Glyphicons Halflings';
            opacity: 0.5;
        }
        :after, :before {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
             box-sizing: border-box;
        }

        table.dataTable thead .sorting_asc:after {
            content: "\e155";
        }
        table.dataTable thead .sorting_desc:after {
            content: "\e156";
        }

        button .margin-right {
            margin-right: 5px;
        }

        table.article-list .block-button {
            font-weight: bold;
            padding: 2px 5px;
            font-size: 80%;
            border-color: black;
        }
        table.article-list .block-button.premium {
            background-color: #ffed4a;
            color: black;
        }
        table.article-list .block-button.article {
            background-color: #28a745;
            color: white;
        }
        table.article-list .block-button.achievement {
            background-color: #563d7c;
            color: white;
        }
        table.article-list .article-image {
            width: 24px;
        }

        .no-active {
            color: lightgray;
        }

    </style>

    <!-- Datatables -->
    <script type="text/javascript">
        var csrf_token = "{{ csrf_token() }}";

        $(document).ready(function () {
            $.noConflict(); //todo
        });
    </script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/ju/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sl-1.3.1/datatables.min.js"></script>
