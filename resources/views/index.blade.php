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

    <div>
        <iframe src="/search" id="iframe">Hello</iframe>
    </div>

</body>

<script>
    const input = document.getElementById('query');
    const iframe = document.getElementById('iframe');

    const searchAction = () => {
        let value = input.value.trim();
        if (value.length === 0) {
            iframe.src = `/search`;
            return;
        }

        iframe.src = `/search?q=${value}`;
    };

    document.getElementById('searchButton').addEventListener('click', searchAction);
    input.addEventListener('keydown', (e) => {
        if (e.keyCode === 13) {
            searchAction();
        }
    });
</script>

</html>
