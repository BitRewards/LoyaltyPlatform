@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        Интерфейс кассира<small>доступен с мобильных</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">Интерфейс кассира</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">
                    Здесь скоро будет удобный интерфейс для
                    <ol>
                        <li>Поиска пользователей по номеру телефона, имени или номеру пластиковой карты</li>
                        <li>Просмотра их баланса и истории действий</li>
                        <li>Начисления / списания баллов лояльности</li>
                    </ol>
            </div>
        </div>
    </div>
@endsection
