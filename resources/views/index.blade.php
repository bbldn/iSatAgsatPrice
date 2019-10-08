<html>

<head>

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
        width: 90%;
    }
</style>

<body>
<div class="top-content">
    <input type="text" id="query">
    <button id="searchButton">Найти</button>
    <b>Курс:</b>
    <span>{{$rate}}</span>
</div>

<button id="refreshButton">Обновить кеш</button>

<div>
    <iframe src="/search" id="iframe">Hello</iframe>
</div>

</body>

<script>
    const bootTime = new Date().getTime();
    const input = document.getElementById('query');
    const iFrame = document.getElementById('iframe');
    let updateEnable = false;

    const searchAction = () => {
        let value = input.value.trim();
        if (value.length === 0) {
            iFrame.src = `/search`;
            return;
        }

        iFrame.src = `/search?q=${value}`;
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

        let xHTTP = new XMLHttpRequest();
        xHTTP.onreadystatechange = function () {
            if (this.status === 200) {
                target.innerHTML = defaultText;
                target.style.backgroundColor = defaultColor;
                window.location.reload(true);
                updateEnable = false;
            }
        };
        xHTTP.open('GET', '/api/update', true);
        xHTTP.send();
    });

</script>

</html>
