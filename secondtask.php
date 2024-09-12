<!DOCTYPE html>
<html>

<head>
    <title>Второе задание</title>
    <meta charset="utf-8" />
    <style>
        td
        {
            text-align: center;
        }
    </style>

</head>

<body>
    <table class="table" disabled>
        <thead>
            <tr>
                <th style="color:#66bb6a;" width="250">Номер</th>
                <th style="color:#66bb6a;" width="250">Дата</th>
                <th style="color:#66bb6a;" width="250">Курс доллара</th>
                <th style="color:#66bb6a;" width="250">Курс рубля к доллару</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //массив выпусков, которые нам интересны
            $ids = [1, 3, 102, 1036, 2000, 2846, 2984];
            $response = [];

            for ($i = 0; $i < count($ids); $i++) {

                $id = $ids[$i]; //номер выпуска

                $url = "https://xkcd.com/$id/info.0.json";

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $comicData = curl_exec($ch);
                curl_close($ch);
                if ($comicData) {
                    $response = json_decode($comicData, true);
                }

                $mounth = $response['month'];
                $year = $response['year'];
                $day = $response['day'];

                if (strlen($mounth) == 1) {
                    $mounth = "0" . $mounth;
                }

                if (strlen($day) == 1) {
                    $day = "0" . $day;
                }

                $dataForTable = $day . "." . $mounth . "." . $year; //дата
            
                $url = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=$day/$mounth/$year";

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $moneyData = curl_exec($ch);
                curl_close($ch);

                if ($moneyData) {
                    $xml = simplexml_load_string($moneyData);
                    if ($xml) {
                        $json = json_encode($xml);
                        $data = json_decode($json, true);
                        if (isset($data['Valute'])) {
                            foreach ($data['Valute'] as $item) {
                                if ($item['CharCode'] == "USD") {
                                    $dollarForTable = $item['Value']; //курс доллара
                                    $rubleForTable = 1 / $dollarForTable; //курс рубля к доллару 
                                }
                            }
                        }
                    }
                }

                echo '  
                        <tr>
                            <td>' . $id . '</td>
                            <td>' . $dataForTable . '</td>
                            <td>' . $dollarForTable . '</td>
                            <td>' . $rubleForTable . '</td>
                        </tr>
                    ';
            }
            ?>
        </tbody>
    </table>


</body>

</html>