@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>{{ __('Reports') }}</h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {!! Form::open(['url' => route('admin.reports.show'), 'method' => 'POST', 'class' => 'js-reports-form']) !!}
            <div class="box">
                <div class="box-header with-border">
                    <h3>{{ __('Choose Report Period') }}</h3>
                </div>
                <div class="box-body row">
                    <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                    <input type="hidden" name="date_till" value="{{ $dateTill }}">
                    @include('vendor.backpack.crud.fields.text', [
                        'field' => [
                            'label' => __('Report Period'),
                            'name' => 'report_date_range',
                        ],
                    ])
                </div>
                <div class="box-footer">
                    <button type="button" class="btn btn-primary js-load-report-button">
                        <span class="fa fa-eye" role="presentation" aria-hidden="true"></span> {{  __('Show Report') }}
                    </button>
                </div>
            </div>

            <div class="box js-report-preview hidden">
                <div class="box-header with-border">
                    <h3 class="report-period-title"></h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{{  __('Statistic name') }}</th>
                                    <th>{{ __('Value') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ __('Number of the loyalty program users at the beggining of the period') }}</td>
                                    <td class="report-data-value" data-type="users-count-start"></td>
                                </tr>
                                <tr>
                                    <td>{{ __('Number of users in the loyalty program at the end of the period') }}</td>
                                    <td class="report-data-value" data-type="users-count-end"></td>
                                </tr>
                                <tr>
                                    <td>{{ _('User growth for the period') }}</td>
                                    <td class="report-data-value" data-type="users-diff"></td>
                                </tr>
                                <tr>
                                    <td>{{  __('The total amount of purchase with discounts (purchased for bonus points; confirmed purchases only)') }}</td>
                                    <td class="report-data-value" data-type="bonus-orders-sum"></td>
                                </tr>
                                <tr>
                                    <td>{{  __('Average amount of purchase with discount (purchased for bonus points; confirmed purchases only)') }}</td>
                                    <td class="report-data-value" data-type="bonus-orders-avg"></td>
                                </tr>
                                <tr>
                                    <td>{{  __('The total amount of purchases for the period (confirmed purchases only)') }}</td>
                                    <td class="report-data-value" data-type="total-orders-sum"></td>
                                </tr>
                                <tr>
                                    <td>{{  __('Average amount of purchases for the period (confirmed purchases only)') }}</td>
                                    <td class="report-data-value" data-type="total-orders-avg"></td>
                                </tr>
                                <tr>
                                    <td>{{  __('The amount of points awarded for the period') }}</td>
                                    <td class="report-data-value" data-type="given-bonus-points"></td>
                                </tr>
                                <tr>
                                    <td>{{  __('The amount of points spent for the period') }}</td>
                                    <td class="report-data-value" data-type="taken-bonus-points"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="report-table-header first-header">{{ __('Most popular rewards') }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th data-field="title">{{ __('Reward') }}</th>
                                    <th data-field="total_amount">{{ __('Total points spent') }}</th>
                                    <th data-field="acquisitions_count" data-sortable="true">{{ __('The number of acquisitions for the period') }}</th>
                                </tr>
                                </thead>
                                <tbody class="report-data-table" data-type="popular-rewards"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="report-table-header">{{ __('Most popular actions') }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th data-field="title">{{ __('Action') }}</th>
                                    <th data-field="total_amount">{{ __('Total points earned') }}</th>
                                    <th data-field="completions_count" data-sortable="true">{{ __('Completed during the period') }}</th>
                                </tr>
                                </thead>
                                <tbody class="report-data-table" data-type="popular-actions"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="report-table-header">{{ __('Most active users') }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th data-field="name">{{ __('Name') }}</th>
                                    <th data-field="email">{{ __('Email') }}</th>
                                    <th data-field="phone">{{ __('Phone') }}</th>
                                    <th data-field="transactions_count" data-sortable="true">{{ __('Number of transactions for the period') }}</th>
                                </tr>
                                </thead>
                                <tbody class="report-data-table" data-type="active-users"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="report-table-header">{{ __('Users with most earned points for the period') }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th data-field="name">{{ __('Name') }}</th>
                                    <th data-field="email">{{ __('Email') }}</th>
                                    <th data-field="phone">{{ __('Phone') }}</th>
                                    <th data-field="points" data-sortable="true">{{ __('Point earned for the period') }}</th>
                                </tr>
                                </thead>
                                <tbody class="report-data-table" data-type="most-earned-users"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="report-table-header">{{ __('Users with most spent points for the period') }}</h4>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th data-field="name">{{ __('Name') }}</th>
                                    <th data-field="email">{{ __('Email') }}</th>
                                    <th data-field="phone">{{ __('Phone') }}</th>
                                    <th data-field="points" data-sortable="true">{{ __('Points spent for the period') }}</th>
                                </tr>
                                </thead>
                                <tbody class="report-data-table" data-type="most-spent-users"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('after_styles')
    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')

    <link rel="stylesheet" href="{{ asset('/loyalty/css/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('/loyalty/css/vendor/bootstrap-table.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@endsection

@section('after_scripts')
    <script src="{{ asset('/loyalty/js/vendor/bootstrap-table.min.js') }}"></script>
    <script src="{{ asset('/vendor/adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('/vendor/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(function () {
            initDateRangePicker();

            $('.js-load-report-button').on('click', function () {
                if ($(this).hasClass('disabled')) {
                    return;
                }

                var el = $(this).addClass('disabled');

                $.post('{{ route('admin.reports.show') }}', $('.js-reports-form').serialize())
                    .done(function (response) {
                        return fillReportsTable(response.data);
                    })
                    .always(function () {
                        el.removeClass('disabled')
                    });
            });
        });

        function initDateRangePicker() {
            var startDateInput = $('input[name="date_from"]');
            var endDateInput = $('input[name="date_till"]');

            $('input[name="report_date_range"]').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                },
                startDate: '{{ $dateFrom }}',
                endDate: '{{ $dateTill }}'
            }, function (start, end) {
                console.log(start, end);
                startDateInput.val(start.format('YYYY-MM-DD'));
                endDateInput.val(end.format('YYYY-MM-DD'));
            });
        }

        function fillReportsTable(data) {
            var mapping = {
                'users-count-start': data.users.start_count,
                'users-count-end': data.users.end_count,
                'users-diff': data.users.diff,
                'bonus-orders-sum': data.orders.paid_with_bonuses.sum,
                'bonus-orders-avg': data.orders.paid_with_bonuses.avg,
                'total-orders-sum': data.orders.sum,
                'total-orders-avg': data.orders.avg,
                'given-bonus-points': data.bonuses.given,
                'taken-bonus-points': data.bonuses.taken,
            };

            for (var key in mapping) {
                $('.report-data-value[data-type="' + key + '"]').html(mapping[key])
            }

            $('.report-period-title').text(data.period.title)

            createReportTableData('popular-rewards', data.popular_rewards, function (item) {
                return {
                    title: item.title,
                    total_amount: item.total_amount,
                    acquisitions_count: item.acquisitions_count
                }
            });

            createReportTableData('popular-actions', data.popular_actions, function (item) {
                return {
                    title: item.title,
                    total_amount: item.total_amount,
                    comletions_count: item.comletions_count
                }
            });

            createReportTableData('active-users', data.active_users, function (item) {
                return {
                    name: item.name,
                    email: item.email ? item.email : '—',
                    phone: item.phone ? item.phone : '—',
                    transactions_count: item.transactions_count
                }
            });

            createReportTableData('most-earned-users', data.most_earned_users, function (item) {
                return {
                    name: item.name,
                    email: item.email ? item.email : '—',
                    phone: item.phone ? item.phone : '—',
                    points: item.points
                }
            });

            createReportTableData('most-spent-users', data.most_spent_users, function (item) {
                return {
                    name: item.name,
                    email: item.email ? item.email : '—',
                    phone: item.phone ? item.phone : '—',
                    points: item.points
                }
            });

            $('.js-report-preview').removeClass('hidden');
        }

        function createReportTableData(type, data, callback) {
            if (typeof callback !== 'function') {
                callback = function (item) {
                    return item
                };
            }

            var tbody = $(`.report-data-table[data-type="${type}"]`);

            if (!tbody.length) {
                throw new Error(`Table "${type}" was not found.`);
            }

            var table = tbody.closest('table').bootstrapTable('destroy');

            if (!data || !data.length) {
                var cellsCount = $('th', $('thead', table)).length;
                $('tr', tbody).remove();

                return tbody.append(
                    `<tr><td colspan="${cellsCount}" class="text-center">{{ __('No data available for given period') }}</td></tr>`
                )
            }

            var rows = [];

            for (var key in data) {
                rows.push(callback(data[key]))
            }

            table.bootstrapTable({
                data: data,
            })
        }
    </script>
@endsection
