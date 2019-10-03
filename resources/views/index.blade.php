<html>

<head>

</head>

<body>
    <input type="text" id="query">
    <button id="searchButton"></button>
    <iframe src="/search" id="iframe">Hello</iframe>
</body>

<script>
    const input = document.getElementById('query');
    const iframe = document.getElementById('iframe');

    document.getElementById('searchButton').addEventListener('click', () => {
        let value = input.value.trim();
        if (value.length === 0) {
            return;
        }

        iframe.src = `/search?q=${value}`;
    });
</script>

</html>
