<html>

<head>

</head>

<body>
<table border="1" style="width: 100%">
    <tr>
        <td>ID</td>
        <td>Название</td>
        <td>Артикул</td>
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
            <td>{{$product['sku']}}</td>
            <td>
                <a href="https://www.agsat.com.ua{{$product['frontend_url']}}" target="_blank">Перейти</a>
            </td>
            @foreach ($product['prices'] as $price)
                <td>{{$price['price']}}</td>
            @endforeach
        </tr>
    @endforeach
</table>
</body>

</html>
