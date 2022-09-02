@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Profile data</div>
                <form class="form-horizontal" action="/profile" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Id</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$user->id}}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="email">E-mail</label>
                            <div class="col-md-9">
                                <p class="form-control-static">{{$user->email}}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="actual_password">Текущий пароль</label>
                            <div class="col-md-9">
                                <input class="form-control" id="actual_password" type="password" name="actual_password" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="actual_password">Новый пароль</label>
                            <div class="col-md-9">
                                <input class="form-control" id="actual_password" type="password" name="new_password" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="new_password_confirm">Подтвердите новый пароль</label>
                            <div class="col-md-9">
                                <input class="form-control" id="new_password_confirm" type="password" name="new_password_confirm" value="">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-info" id="save_profile_btn" type="submit">
                            <i class="fas fa-save"></i>
                            <span class="hide-650-down">Update</span>
                        </button>
                    </div>
                    <input type="hidden" name="_method" value="put" />
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
