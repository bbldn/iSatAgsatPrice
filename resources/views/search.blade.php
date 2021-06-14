<html lang="ru">

<style>
    table, th, td {
        border: 1px solid black;
    }

    table {
        width: 100%;
    }
</style>

<body>
<table>
    <tr>
        <td>Название</td>
        <td>Артикул</td>
        <td>Ссылка</td>
        <td>Розница</td>
        <td>Дилер</td>
        <td>ОПТ</td>
    </tr>
    @foreach ($products as $product)
        <tr>
            <td>{{$product['name']}}</td>
            <td>{{$product['sku']}}</td>
            <td>
                <a href="https://www.agsat.com.ua{{$product['frontend_url']}}" target="_blank">Перейти</a>
            </td>
            @foreach ($product['prices'] as $key => $price)
                @if ($key != 3)
                    <td>{{round($price['price'], 2)}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
</table>
</body>

</html>