<?php

if (! function_exists('numberConvert')) {
    function numberConvert($num = false, $currency = 'shillings'): array|bool|string
    {
        $num = str_replace([',', ''], '', trim($num));
        if (! $num) {
            return false;
        }

        $num = (int) $num;

        $words = [];
        $list1 = [
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen',
        ];

        $list2 = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred'];
        $list3 = [
            '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion',
        ];

        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00'.$num, -$max_length);
        $num_levels = str_split($num, 3);

        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            \App\Services\QuickBooksServiceHelper::logToFile($num_levels[$i]);
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' '.$list1[$hundreds].' hundred'.($hundreds == 1 ? '' : '').' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';

            if ($tens < 20) {
                $tens = ($tens ? ' and '.$list1[$tens].' ' : '');
            } elseif ($tens >= 20) {
                $tens = (int) ($tens / 10);
                $tens = ' and '.$list2[$tens].' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' '.$list1[$singles].' ';
            }

            $words[] = $hundreds.$tens.$singles.(($levels && (int) ($num_levels[$i])) ? ' '.$list3[$levels].' ' : '');
        }

        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }

        $words = implode(' ', $words);
        $words = preg_replace('/^\s\b(and)/', '', $words);
        $words = trim($words);
        $words = ucfirst($words);
        $words = $words.' '.$currency.' only.';

        return str_replace(' and ', ' ', $words);
    }
}

if (! function_exists('currencyConvert')) {
    function currencyConvert($currency)
    {
        if ($currency == 'CNY') {
            return 'yuan';
        }
        if ($currency == 'EUR') {
            return 'euros';
        }
        if ($currency == 'GBP') {
            return 'pounds';
        }
        if ($currency == 'JPY') {
            return 'yen';
        }
        if ($currency == 'KES') {
            return 'shillings';
        }
        if ($currency == 'RWF') {
            return 'francs';
        }
        if ($currency == 'SSP') {
            return 'pounds';
        }
        if ($currency == 'TZS') {
            return 'shillings';
        }
        if ($currency == 'UGX') {
            return 'shillings';
        }
        if ($currency == 'USD') {
            return 'dollars';
        }
        if ($currency == 'ZAR') {
            return 'rands';
        }
    }
}


if (!function_exists('get_tin')) {
    function get_tin(array $custom_fields): string
    {
        foreach ($custom_fields as $custom_field) {
            if ($custom_field->Name == "TIN") {
                if (!property_exists($custom_field, 'StringValue')) {
                    return '';
                } else {
                    return  $custom_field->StringValue;
                }
            }
        }

        return ""; // Return an empty string if "TIN" custom field is not found
    }
}
