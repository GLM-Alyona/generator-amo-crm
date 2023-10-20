<?php

    const HOST_AMO = 'https://olgismay.amocrm.ru';

    //Подключаем функции
    include 'functions.php';

    //Инициируем переменные с формы
    $phone = $email = "";
    $phone = post_input($_POST["phone"]);
    $email = post_input($_POST["email"]);


    //Временный код доступа
    $code = 'def50200418278c2c0c4f22f11f2045e8d881571ce40aefb727129f9943d9d150f1cf13c04d021bbce63406bc0f744bf0cf9c0e86db3e9abb7553249e99615c51b0658a0130f7f7bb8fcb29e6a98a247d34ad5dbe70ceaec5a6b4061eac4e801b82fc01c5836b7536adaada0f1e3f1467be6c838a242e1b27e128ca597cdfb696a936e1d270ce1b2cc8ca07a369f45060acb4baf47cbdf67e8948c0b840ba5ffd8b87e6bc0a48d89d5425e7b49dc3ae7587f2be7ba7efd39ff8ed53602a74aff002c235793d6d46b723e2da8fb8ed60ea3a9385d3cca4121f526dc48f84ee4b2f96a08b8e33d00f2140842d100d488b54a4d96d9828b7200f9046dfa4b08b057983c16de428234972a6da83c2b397961b8ced2119f2277f0444cc46c426a1d6ed8a507d09d7a43210c80e801028d0cde402277fc2934b3b8646f06d2f72385f1f2c2f5124d65f28e99591ff3794b7cde16a0c2ce85681d344ed4b5f9c07f461ec3550e70375157ff5b542fec763e0b2a9971a217896098d88bef479647c2e528a260af6e10c05345d1f42f6e67e9c71f071e432db213b87f3dee6a37b5c06885e4ab016d6db610b000cac350f87a70522df7518e0cc79f854be2d05fff6da694d019bfcbd98b922fa03165f0167dbebfa71333fbd01e426abeeae935183fa3b8ffa708da'; // код авторизации нашей интеграции

    //получаем с токеном
    $response = amoGetToken($code);

    //Если что-то есть, то продолжаем выполнение иначе завершаем скрипт(в дальнейшем можно будет указать причину оставновки)
    if(isset($response) && $response) {

        $access_token = $response['access_token']; //Access токен
        $refresh_token = $response['refresh_token']; //Refresh токен
        $token_type = $response['token_type']; //Тип токена
        $expires_in = $response['expires_in']; //Через сколько действие токена истекает
    }
    else{
        die();
    }

    $lead_id = amoAddTask($access_token);

    if(isset($lead_id) && $lead_id) {

        amoAddContact($access_token, [
            "namePerson"	=> "Гюлумян Алёна",
            "phonePerson"	=> $phone,
            "emailPerson"	=> $email,
            "lead_id" => $lead_id
        ]);
    }


/* ОТПРАВКА ПИСЬМА НА ПОЧТУ*/

$to ='order@salesgenerator.pro';
$email = clear_data($_POST['email']);
$phone = clear_data($_POST['phone']);
$subject ='Заявка с сайта - Гюлумян Алёна';

$headers = 'From: webmaster@example.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$message = 'Email: ' .$email."\n" . 'Телефон: ' .$phone . "\n" .$headers;

function clear_data($val){
    $val = trim($val);
    $val = stripslashes($val);
    $val = htmlspecialchars($val);
    return $val;
}

mail($to, $subject, $message);
