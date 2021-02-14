<html lang="ru">

<head>
    <title>Agsat Parser</title>
</head>

<style>
    #iframe {
        width: 100%;
        height: 90vh;
    }

    .top-content {
        height: 5vh;
    }

    #query {
        width: 88%;
    }
</style>

<body>
<div class="top-content">
    <label>
        <input type="text" id="query">
    </label>

    <button id="searchButton">Найти</button>
    <select id="currencySelect">
        <option value="1" @if (1 === $currencyId)selected="selected"@endif>грн</option>
        <option value="2" @if (2 === $currencyId)selected="selected"@endif>$</option>
    </select>
    <b>Курс:</b>
    <span>{{ $rate }}</span>
</div>

<button id="refreshButton">Обновить кеш</button>

<div>
    <iframe src="/search?currency_id={{ $currencyId }}" id="iframe">Hello</iframe>
</div>

</body>

<script>
    const bootTime = new Date().getTime();
    const input = document.getElementById('query');
    const iFrame = document.getElementById('iframe');
    const currencySelect = document.getElementById('currencySelect');
    let updateEnable = false;

    const searchAction = () => {
        let value = input.value.trim();

        if (value.length === 0) {
            iFrame.src = `/search?currency_id=${currencySelect.value}`;
            return;
        }

        iFrame.src = `/search?q=${value}&currency_id=${currencySelect.value}`;
    };

    const refresh = (bootTime) => {
        console.log(new Date().getTime() - bootTime);
        if (new Date().getTime() - bootTime >= 3600000) {
            window.location.reload(true);
        }

        setTimeout(refresh, 10000, bootTime);
    };
    setTimeout(refresh, 10000, bootTime);

    document.getElementById('searchButton').addEventListener('click', searchAction);
    input.addEventListener('keydown', (e) => {
        if (e.keyCode === 13) {
            searchAction();
        }
    });

    let defaultColor = null;
    let defaultText = null;

    document.getElementById('refreshButton').addEventListener('click', (e) => {
        if (updateEnable) {
            alert('Кеш уже обновляется');
            return;
        }
        updateEnable = true;

        let target = e.target;
        defaultText = target.innerHTML;
        defaultColor = target.style.backgroundColor;
        target.innerHTML = 'Не перезагружайте/не закрывайте страницу пока кнопка не станет серой';
        target.style.backgroundColor = 'red';

        let request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (this.status === 200) {
                target.innerHTML = defaultText;
                target.style.backgroundColor = defaultColor;
                window.location.reload();
                updateEnable = false;
            }
        };
        request.open('GET', '/api/update', true);
        request.send();
    });

    currencySelect.addEventListener("change", () => {
        window.location = `/?currency_id=${currencySelect.value}`;
    });
</script>

</html>
