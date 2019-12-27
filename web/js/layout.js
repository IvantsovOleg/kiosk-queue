function checkTime(i) {
    return i < 10 ? ("0" + i) : i;
}

function getWeekDay(date) {
    var days = ['ВОСКРЕСЕНЬЕ', 'ПОНЕДЕЛЬНИК', 'ВТОРНИК', 'СРЕДА', 'ЧЕТВЕРГ', 'ПЯТНИЦА', 'СУББОТА'];
    return days[date.getDay()];
}

function getMonthLabel(date) {
    var days = ['ЯНВАРЯ', 'ФЕВРАЛЯ', 'МАРТА', 'АПРЕЛЯ', 'МАЯ', 'ИЮНЯ', 'ИЮЛЯ', 'АВГУСТА', 'СЕНТЯБРЯ', 'ОКТЯБРЯ', 'НОЯБРЯ', 'ДЕКАБРЯ'];
    return days[date.getMonth()];
}

function startTime() {
    var currentDate = new Date();

    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1;
    var year = currentDate.getFullYear();

    var hour = currentDate.getHours();
    var minute = currentDate.getMinutes();

    minute = checkTime(minute);
    month = checkTime(month);
    day = checkTime(day);

    // document.getElementById('date').innerHTML = day + " " + getMonthLabel(currentDate) + " " + year + " Г. (" + getWeekDay(currentDate) + ")";
    document.getElementById('date').innerHTML = day + " " + getMonthLabel(currentDate);
    document.getElementById('time').innerHTML = hour + ":" + minute;

    // setTimeout('startTime()', 10000);
}

// $(document).ready(function () {
//     setInterval(function(){
        startTime()
//     }, 500);
// });