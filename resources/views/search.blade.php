<html>

<head>
    <title>Привет</title>
</head>

<body>
<table border="1" style="width: 100%">
    <tr>
        <td>ID</td>
        <td>Название</td>
        <td>Ссылка</td>
        <td>Розница</td>
        <td>Дилер</td>
        <td>ОПТ</td>
        <td>Партнер</td>
    </tr>
    @foreach ($products as $product)
        <tr>
            <td>{{$product['id']}}</td>
            <td>{{$product['name']}}</td>
            <td>
                <a href="https://www.agsat.com.ua{{$product['frontend_url']}}">Перейти</a>
            </td>

            @foreach ($product['prices'] as $price)
                @if ($price['category_id'] != 1)
                    <td>{{$price['price'] * $rate}}</td>
                @else
                    <td>{{$price['price']}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
</table>
</body>

</html>
