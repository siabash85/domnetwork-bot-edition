<?php



/**
 * @param $text
 * @param $limit
 * @return string
 */
function truncate($text, $limit = 35): ?string
{
    return \Illuminate\Support\Str::limit($text, $limit, ' ...');
}


function createDatetimeFromFormat($date, $format = 'Y/m/d H:i')
{
    return \Morilog\Jalali\CalendarUtils::createDatetimeFromFormat($format, $date);
}

function formatGregorian($date, $format = 'Y/m/d H:i')
{
    if ($date)  return \Morilog\Jalali\CalendarUtils::strftime($format, strtotime($date));
    return null;
}
