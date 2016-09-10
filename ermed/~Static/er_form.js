var D3ER = new function () {
    this.eyes = function () {
        if (D3Api.hasClass(document.body, 'er-eyes')) {
            D3Api.removeClass(document.body, 'er-eyes');
            D3Api.globalClientData.set('erEyes', 0);
        } else {
            D3Api.addClass(document.body, 'er-eyes');
            D3Api.globalClientData.set('erEyes', 1);
        }
    };

    this.startLoad = function () {
        document.getElementById('D3LoadContainer').style.display = 'block';

        clearTimeout(D3ER._timerInactivitySystem);
    };
    this.endLoad = function () {
        document.getElementById('D3LoadContainer').style.display = '';

        if (!D3ER.isInfomate()) {
            return;
        }

        var minute = D3Api.getOption('inactivitySystem', 0) * 60 * 1000;

        if (minute > 0 && minute < Infinity && D3Api.globalClientData.get('erLogin')) {
            clearTimeout(D3ER._timerInactivitySystem);
            D3ER._timerInactivitySystem = setTimeout(function () {
                D3ER.erExit();
            }, minute);
        }
    };
    var performanceStart = Date.now();
    this.getPerformanceNow = function()
    {
        if(window.performance && window.performance.now)
        {
            return window.performance.now();
        }else
            return Date.now()-performanceStart;
    }
    this.setCurrentDate = function (str) {
        D3ER._currentDate = {
            date: new Date(str),
            now: D3ER.getPerformanceNow()
        };
    };
    this.getCurrentDate = function () {
        return new Date(+D3ER._currentDate.date + D3ER.getPerformanceNow() - D3ER._currentDate.now);
    };

    this.getStrMonth = function (date) {
        return ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'][date.getMonth()];
    };
    this.getStrMonthNormal = function (date) {
        return ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'][date.getMonth()];
    };
    this.getStrDay = function (date) {
        return ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'][date.getDay()];
    };
    this.strDate = function (s) {
        return (s < 10) ? '0' + s : '' + s;
    };
    this.formatDate = function (date, time) {
        var s = D3ER.strDate(date.getDate()) + '.' + D3ER.strDate(date.getMonth() + 1) + '.' + date.getFullYear();
        if (time) {
            s += ' ' + D3ER.strDate(date.getHours()) + ':' + D3ER.strDate(date.getMinutes());
        }
        return s;
    };
    this.getDay = function (date) {
        return date.getDay() || 7;
    };

    this.regexString = function (value) {
        return String(value || '').replace(/([)(\\.?\[\]\{\}^$+-])/g, '\\$1');
    };

    this.printTicket = function (response) {
        D3ER.printHTML(
            '<div>' + 'Талон на прием к врачу' + '</div><br />' +
            'Дата приема: <b>' + response.rec_date_cap + '</b>' +
            (response.cablab && '<div>' + 'Кабинет: <b>' + response.cablab + '</b></div>' || '') +
            (response.doctor_fio && '<div>' + 'ФИО врача: <b>' + response.doctor_fio + '</b></div>' || '') +
            (response.service && '<div>' + 'Услуга: ' + response.service + '</div>' || '') +
            (response.payment && '<div>' + 'Тип оплаты: ' + response.payment + '</div>' || '') +
            (response.cost && '<div>' + 'Стоимость приема: ' + response.cost + 'руб' + '</div>' || '') +
            (response.lpu && '<div>' + 'МО: ' + response.lpu + '</div>' || '') +
            ((response.division || response.address) ? '<div>' + 'Место приема: ' + (response.division || '') + ((response.division && response.address) ? ', ' : '') + (response.address || '') + '</div>' : '') +
            (response.phones && '<div>' + 'Тел. регистратуры: ' + response.phones + '</div>' || '') +
            (response.patient_fio && '<div>' + 'Пациент: ' + response.patient_fio + '</div>' || '') +
            (response.rec_comment && '<div>' + 'Комментарий: ' + response.rec_comment + '</div>' || '')
        );
    };
    this.printHTML = function (html) {
        var windowPrint = window.frames.D3PrintContainer,
            documentPrint = windowPrint.document,
            bodyPrint = documentPrint.body;

        documentPrint.title = 'Электронная регистратура';

        bodyPrint.innerHTML = (html || '') +
                                '<style>' +
                                '@media print{ @page{ margin: 5mm; }} ' +
                                'html, body{ padding: 0; margin: 0; } ' +
                                'body{ font-size: ' + (D3ER.isInfomate() ? '6mm' : '3mm') + '; } ' +
                                '</style>';

        windowPrint.print();
    };

    this.isInfomate = function () {
        return /[?&]infomate=true(&|$)/.test(window.location.search);
    };

    this.getDataUrl = function () {
        var arrSearch = window.location.search.split(/[?&]/);
        var urlData = {};

        for (var i = 0; i < arrSearch.length; i++) {
            var arrItem = arrSearch[i].split('=');

            if (!arrItem[0]) {
                continue;
            }
            urlData[arrItem[0]] = arrItem[1] || '';
        }

        return urlData;
    };

    this.locationSearch = function () {
        var path = [];
        var data = D3ER.getDataUrl();

        delete data.D3ErUsername;

        for (var key in data) {
            path.push(key + ((data[key]) ? '=' + data[key] : ''));
        }

        return (path.length > 0) ? '?' + path.join('&') : '';
    };
    this.erHome = function () {
        location.search = D3ER.locationSearch();
    };
    this.erExit = function () {
        var erLogin = JSON.parse(D3Api.globalClientData.get('erLogin', 'null'));

        D3Api.globalClientData.set('erLogin');

        if (erLogin) {
            window.open('webservice/er/logout' + D3ER.locationSearch(), '_self');
        } else {
            D3ER.erHome();
        }
    };
};