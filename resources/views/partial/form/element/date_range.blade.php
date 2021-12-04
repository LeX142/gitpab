<?php
$startDate = $input['date_start'] ?? old('date_start');
$endDate = $input['date_end'] ?? old('date_end');
$dateStartFieldName = $date_start_field_name ?? 'date_start';
$dateEndFieldName = $date_end_field_name ?? 'date_end';
?>
<div class="form-group {{ $errors->has($dateStartFieldName) || $errors->has($dateEndFieldName) ? 'has-error' : '' }}">
    <div class="form-group">
        <label>{{ $label }}</label>

        <div id="reportrange_{{ $name }}" class="pull-right datepicker">
            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp; @lang('calendar.Period')
            <span></span> <b class="caret"></b>

            {!! Form::hidden($dateStartFieldName) !!}
            {!! Form::hidden($dateEndFieldName) !!}
        </div>

        <span class="help-block">{{ $errors->first($dateStartFieldName) }}</span>
        <span class="help-block">{{ $errors->first($dateEndFieldName) }}</span>
    </div>
</div>

@section('js')
<script>
    $(function () {
        var element = $("#reportrange_{{ $name }}"),
                startDate = moment().subtract(7, 'days'),
                endDate = moment(),
                isDatesSelect = false;

        // Default values
        @if (!empty($input['date_start']) AND !empty($input['date_end']))
        startDate = moment( '{{ $startDate }}' );
        endDate = moment( '{{ $endDate }}' );
        isDatesSelect = true;
        @endif

        // Daterangepicker callback function
        var onChangeDaterangepicker = function (start, end) {
            element.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            element.find('input[name=<?php echo $dateStartFieldName; ?>]').val(start.format('YYYY-MM-DD'));
            element.find('input[name=<?php echo $dateEndFieldName; ?>]').val(end.format('YYYY-MM-DD'));
        };

        //Date range as a button
        element.daterangepicker(
            {
                ranges: {
                    '@lang('calendar.Today')': [moment(), moment()],
                    '@lang('calendar.First pay period')': [moment().startOf('month'), moment().startOf('month').add(14,'days')],
                    '@lang('calendar.Second pay period')': [moment().startOf('month').add(15,'days'), moment().endOf('month')],
                    '@lang('calendar.Yesterday')': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '@lang('calendar.Last 7 days')': [moment().subtract(6, 'days'), moment()],
                    '@lang('calendar.Last 30 days')': [moment().subtract(29, 'days'), moment()],
                    '@lang('calendar.Current month')': [moment().startOf('month'), moment().endOf('month')],
                    '@lang('calendar.Last month')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: startDate,
                endDate: endDate,
                locale: {
                    fromLabel: '@lang('calendar.From')',
                    toLabel: '@lang('calendar.To')',
                    applyLabel: '@lang('calendar.Select')',
                    cancelLabel: '@lang('calendar.Close')',
                    customRangeLabel: '@lang('calendar.Range')'
                }
            },
            onChangeDaterangepicker
        );

        if (isDatesSelect)
        {
            element.data('daterangepicker').setStartDate(startDate);
            element.data('daterangepicker').setEndDate(endDate);
            onChangeDaterangepicker(startDate, endDate);
        }
    });
</script>
@append
