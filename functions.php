<?php
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function post_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function amoAddTask($access_token, $contactId = false) {

    $lead['add'] = array(
        array(
            'name' => "Форма с сайта " . date('Y-m-d H:i:s'),
        )
    );

    $link = HOST_AMO . "/api/v2/leads";

    $headers = [
        "Accept: application/json",
        'Authorization: Bearer ' . $access_token
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-undefined/2.0");
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($lead));
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl);
    $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);

    $response = json_decode($out,TRUE);

    if($code === 200){
        echo 'Проверяем responce</br>';
        if(isset($response['_embedded']['items'][0]['id']) && $response['_embedded']['items'][0]['id']){
            echo 'Возвращаем ID заказа : '. $response['_embedded']['items'][0]['id'] .'</br>';
            return $response['_embedded']['items'][0]['id'];
        }
    }

    return false;

}

function amoAddContact($access_token, $contact) {

    $data_set['request']['contacts']['add'] = array(
        array(
            'name' => $contact["namePerson"],
            'linked_leads_id' => array($contact['lead_id']),
            'tags' => 'авто отправка',
            'custom_fields'	=> array(
                [
                    "id" => 521575,
                    "values" => [
                        [
                            "value" => $contact["emailPerson"],
                            "enum" => "WORK"
                        ]
                    ]
                ],
                [
                    "id" => 521573,
                    "values" => [
                        [
                            "value" => $contact["phonePerson"],
                            "enum" => "MOB"
                        ]
                    ]
                ]
            )
        )
    );

    /* Формируем заголовки */
    $headers = [
        "Accept: application/json",
        'Authorization: Bearer ' . $access_token
    ];

    $link = HOST_AMO . '/private/api/v2/json/contacts/set';

    $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    /** Устанавливаем необходимые опции для сеанса cURL  */
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client-undefined/2.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data_set));
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);

    $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($out,true);

    //$contact_id = $response["response"]["contacts"]["add"]["0"]["id"];

}


/* в эту функцию мы передаём текущий refresh_token */
function returnNewToken($token) {

    $link = 'https://124svetik12345.amocrm.ru/oauth2/access_token';

    /** Соберем данные для запроса */
    $data = [
        'client_id' => 'bac25e77-11f7-431f-bb64-94acde23e8da',
        'client_secret' => 'nksUk7ZWLhB6YgjLfg1hlg1JFNaPCM2yVOdeyshLG2eILZYz19nR69z8TPv0SP0f',
        'grant_type' => 'refresh_token',
        'refresh_token' => $token,
        'redirect_uri' => 'https://example.com',
    ];

    /**
     * Нам необходимо инициировать запрос к серверу.
     * Воспользуемся библиотекой cURL (поставляется в составе PHP).
     * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
     */
    $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    /** Устанавливаем необходимые опции для сеанса cURL  */
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int)$code;
    $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    try
    {
        /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
        if ($code < 200 || $code > 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
        }
    }
    catch(\Exception $e)
    {
        die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
    }

    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */

    $response = json_decode($out, true);

    if($response) {

        /* записываем конечное время жизни токена */
        $response["endTokenTime"] = time() + $response["expires_in"];

        $responseJSON = json_encode($response);

        /* передаём значения наших токенов в файл */
        $filename = "token.json";
        $f = fopen($filename,'w');
        fwrite($f, $responseJSON);
        fclose($f);

        $response = json_decode($responseJSON, true);

        return $response;
    }
    else {
        return false;
    }

}

function amoGetToken($code) {

    $subdomain = 'olgismay'; //Поддомен нужного аккаунта
    $link = HOST_AMO . '/oauth2/access_token'; //Формируем URL для запроса

    /** Соберем данные для запроса */
    $data = [
        'client_id' => 'f815f784-bfef-4c6e-a9d6-d973aeca3c8b', // id нашей интеграции
        'client_secret' => '0fJPnPIr0wTmCOnN4TAL9fssDfOnFHfukutLwBrNTUx2a65GUGDjuDfwkL98WRGt', // секретный ключ нашей интеграции
        'grant_type' => 'authorization_code',
        'code' => $code, // код авторизации нашей интеграции
        'redirect_uri' => 'https://example.com',// домен сайта нашей интеграции

    ];

    $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    /** Устанавливаем необходимые опции для сеанса cURL  */
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    print_r($out);
    curl_close($curl);

    /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int)$code;

    // коды возможных ошибок
    $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    try {
        /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
        if ($code < 200 || $code > 204) {
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);

        }
    }
    catch(\Exception $e)
    {
        die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
    }

    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */

    if($code == 200) {
        $response = json_decode($out, true);
        return $response;
    }

    return false;

}

/**
 * @param $auth_code
 */
function amoRefreshToken($auth_code) {

}

/**
 * @param $data
 */
function amoGetAuthCode($data) {

}