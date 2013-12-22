function SendMail(f) {
    if (f.day.value == 1 && f.month.value == "01" && ( f.year.value == "1925" || f.year.value == '' )) {
        f.day.focus();
        alert('Заполните, пожалуйста, дату рождения');
        return false;
    }
    if (f.name.value == '') {
        f.name.focus();
        alert('Заполните, пожалуйста, ФИО');
        return false;
    }
    if (f.code.value == '') {
        f.code.focus();
        alert('Заполните, пожалуйста, идентификационный код');
        return false;
    }
    if (f.address.value == '') {
        f.address.focus();
        alert('Заполните, пожалуйста, адрес');
        return false;
    }
    if (f.phone.value == '') {
        f.phone.focus();
        alert('Заполните, пожалуйста, номер телефона');
        return false;
    }
    if (f.email.value == '') {
        f.email.focus();
        alert('Заполните, пожалуйста, электронный адрес');
        return false;
    }
    f.but.disabled = true;

    $.ajax({
        url: '/calculator/send/',
        type: "POST",
        data: ({
            price: price,
            term: term,
            name: f.name.value,
            code: f.code.value,
            address: f.address.value,
            phone: f.phone.value,
            email: f.email.value,
            day: f.day.value,
            month: f.month.value,
            year: f.year.value
        }),
        error: function () {
            alert('Возникла ошибка при отправке!');
        },
        success: function (data) {
            f.reset();
            alert('Данные отправлены!');
            document.getElementById('sendForm').style.display = "none";
        },
        dataType: 'html'
    });


    return false;
}


var rate = 0.01;
var fee = 2.00;
var belay = 1.25;
var term = 3;
var price = '';

function Pay() {
    document.getElementById('rate').innerHTML = rate + "%";
    document.getElementById('fee').innerHTML = fee + "%";
    document.getElementById('belay').innerHTML = belay + "%";


    term = document.getElementById('time').value;

    if (term <= 12) {
        belay = 1.25;
    }
    else {
        belay = 1.00;
    }
    document.getElementById('belay').innerHTML = belay + "%";

    price = document.getElementById('price').value;
    if (price == '') return false;
    price = parseFloat(price);
    if (price == NaN) return false;

    pay = ( price / term ) + ( price + (price * belay / 100 * term) ) * rate / 100 / 12 + ( price + (price * belay / 100 * term) ) * fee / 100 + (price * belay / 100);
    if (pay == NaN) return false;
    document.getElementById('pay').innerHTML = pay.toFixed(2);
    document.getElementById('cash').style.display = "block";

    return true;
}

function SendForm() {
    if (Pay() == true) {
        document.getElementById('sendForm').style.display = "block";
        var years = document.getElementById('year');
        var dt = new Date();
        var lastYear = dt.getFullYear() - 18;
        for (var i = 1925; i <= lastYear; i++)
            years.options[years.options.length] = new Option(i, i);
        return true;
    }
    else return false;
}

$(document).ready(function () {
    var goButton = $('#goButton').prop('disabled', 'disabled');
    $('#goButton').addClass('opacity');

    $('#agree').change(function () {
        var checkBox = $(this).prop('checked');

        if (checkBox) {
            $('#goButton').removeClass('opacity');
        }
        else {
            $('#goButton').addClass('opacity');
        }
    });

    $('#agree').change(function () {
        $('#goButton').prop('disabled', function (i, val) {
            return !val;
        })
    });
});
