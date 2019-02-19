<?php
/*
Задание #1
1. Дан XML файл. Сохраните его под именем data.xml:
2. Написать скрипт, который выведет всю информацию из этого файла в удобно
читаемом виде. Представьте, что результат вашего скрипта будет распечатан и выдан
курьеру для доставки, разберется ли курьер в этой информации?
*/
function task1($filePath)
{
    $xml = simplexml_load_file($filePath);
    $xmlAttr = [];
    foreach ($xml->attributes() as $key => $value) {
        $xmlAttr[$key] = $value;
    }
    $productAttr = [];
    foreach ($xml->Items->Item as $item) {
        foreach ($item->attributes() as $value) {
            $productAttr[] = $value;
        }
    }
    echo "<h1>The order for the supply of products #".$xmlAttr['PurchaseOrderNumber']." from ".$xmlAttr['OrderDate']."</h1>";
    echo "<h2>$xml->DeliveryNotes</h2>";
    $length = count($xml->Address);
    for ($i=0; $i < $length; $i++) {
        echo "<h3>Receiver of product: ".$xml->Address[$i]->Name."</h3>
        <h4>Product: #$productAttr[$i]</h4>
        <ol>
            <li>ProductName: ".$xml->Items->Item[$i]->ProductName."</li>
            <li>Quantity: ".$xml->Items->Item[$i]->Quantity."</li>
            <li>USPrice: ".$xml->Items->Item[$i]->USPrice."</li>
            <li>ShipDate: ".$xml->Items->Item[$i]->ShipDate."</li>
            <li>Comment: ".$xml->Items->Item[$i]->Comment."</li>
        </ol>
        <h4>Delivery address:</h4>
        <ol>
            <li>Country: ".$xml->Address[$i]->Country."</li>
            <li>City: ".$xml->Address[$i]->City."</li>
            <li>State: ".$xml->Address[$i]->State."</li>
            <li>Street: ".$xml->Address[$i]->Street."</li>
            <li>Zip: ".$xml->Address[$i]->Zip."</li>
        </ol>";
    }
}
/*
Задача #2
1. Создайте массив, в котором имеется как минимум 1 уровень вложенности.
Преобразуйте его в JSON. Сохраните как output.json
2. Откройте файл output.json. Случайным образом, используя функцию rand(), решите
изменять данные или нет. Сохраните как output2.json
3. Откройте оба файла. Найдите разницу и выведите информацию об отличающихся
элементах
*/
function task2()
{
    $arr = ["one" => 1, "two" => 2, "three" => ["one" => 123, "two" => 456]];
    $pretty = json_encode($arr, JSON_PRETTY_PRINT);
    $fp = fopen('output.json', "w");
    fputs($fp, $pretty);
    fclose($fp);
    $content = file_get_contents('output.json');
    $result = json_decode($content, true);
    function changeArray($arr, $change)
    {
        if ($change) {
            $arr = array_map(function ($item) {
                if (!is_array($item)) {
                    return ($item*2);
                } else {
                    return $item;
                }
            }, $arr);
        }
        return $arr;
    }
    $pretty = json_encode(changeArray($result, (bool)rand(0, 1)), JSON_PRETTY_PRINT);
    $fp = fopen('output2.json', "w");
    fputs($fp, $pretty);
    fclose($fp);
    $content = file_get_contents('output.json');
    $result1 = json_decode($content, true);
    $content = file_get_contents('output2.json');
    $result2 = json_decode($content, true);
    $keys1 = array_keys($result1);
    $keys2 = array_keys($result2);
    $count = 0;
    for ($i=0; $i < count($result1); $i++) {
        if ($result1[$keys1[$i]] !== $result2[$keys2[$i]]) {
            echo "Значение ключа $keys1[$i] первого массива не равен значению ключа $keys2[$i] второго массива!<br />";
            $count++;
        }
    }
    if ($count == 0) {
        echo "Массивы равны";
    }
}
/*
Задача #3
1. Программно создайте массив, в котором перечислено не менее 50 случайных чисел
от 1 до 100
2. Сохраните данные в файл csv
3. Откройте файл csv и посчитайте сумму четных чисел
*/
function task3()
{
    $array = [];
    for ($i=0; $i<70; $i++) {
        $array[] = rand(1, 100);
    }
    $fp = fopen('file.csv', 'w');
    fputcsv($fp, $array);
    fclose($fp);
    if (($handle = fopen("file.csv", "r")) !== false) {
        while (($data = fgetcsv($handle, 10000, ",")) !== false) {
            $num = count($data);
            $sum = 0;
            for ($i=0; $i < $num; $i++) {
                if ($data[$i]%2 == 0) {
                    $sum+= $data[$i];
                }
            }
            echo "Сумма всех четных чисел в файле .csv = $sum";
        }
        fclose($handle);
    }
}
/*
Задача #4
1. С помощью PHP запросить данные по адресу:
https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json
2. Вывести title и page_id
*/
function task4()
{
    $url = 'https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json';
    $result = json_decode(file_get_contents($url), true);
    function find_node($dataSet, $id)
    {
        foreach ($dataSet as $key => $value) {
            if ($key === $id) {
                return $value;
            } else {
                if (is_array($value)) {
                    $result = find_node($value, $id);
                    if ($result) {
                        return $result; // выход из рекурсии
                    }
                }
            }
        }
    }
    $id = 'title';
    echo "Значение ключа $id = ".find_node($result, $id)."<br />";
    $id = 'pageid';
    echo "Значение ключа $id = ".find_node($result, $id);
}
