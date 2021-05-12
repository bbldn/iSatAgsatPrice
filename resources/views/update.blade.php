<html lang="ru">

<head>
    <title>

    </title>
</head>

<style>
    .rectangle {
        width: 400px;
        height: 300px;
    }

    .d-none {
        display: none;
    }

    .d-block {
        display: block;
    }

    .d-inline {
        display: inline;
    }

    .d-inline-block {
        display: inline-block;
    }

    .yellow-back {
        background: yellow;
    }

    .red-back {
        background: red;
    }

    .green-back {
        background: green;
    }
</style>

<body>
<div class="rectangle yellow-back" id="rectangle"></div>
<h1 class="d-none d-block" id="text-1">Нажмите на прямоугольник чтобы начать обновление</h1>
<h1 class="d-none" id="text-2">Не закрывайте вкладку пока прямоугольник не станет зелёным!</h1>
<h1 class="d-none" id="text-3">Поздравляем! Обновление завершено</h1>
</body>

<script>
    let flag = false;
    document.getElementById('rectangle').addEventListener('click', () => {
        if (flag) {
            return;
        }
        document.getElementById('text-1').classList.toggle('d-block');
        document.getElementById('text-2').classList.toggle('d-block');
        document.getElementById('rectangle').classList.toggle('red-back');
        flag = true;

        let xHTTP = new XMLHttpRequest();
        xHTTP.onreadystatechange = function () {
            if (this.status === 200) {
                document.getElementById('text-2').classList.toggle('d-block');
                document.getElementById('text-3').classList.toggle('d-block');
                document.getElementById('rectangle').classList.toggle('green-back');
            }
        };
        xHTTP.open('GET', '/api/update', true);
        xHTTP.send();
    });
</script>

</html>