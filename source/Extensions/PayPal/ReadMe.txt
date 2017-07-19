Для работы модуля требуется:

1. Зарегестрировать юредический аккаунт https://www.paypal.com/ua/signup/account
1.1 Выбрать пункт Bussines account и продолжить регистрацию.
1.2 Пройти все пункты до завершения регистрации.
2. Войти в аккаунт https://www.paypal.com/signin/?country.x=UA

2.1 Справа в верхнем меню выбрать пункт Profile / Profile and settings
2.2 В открывшейся странице выбрать закладку My selling напротив пункта Selling online / API access нажать ссылку update
2.3 На странице API Access выбрать Option2 - Request API credentials
2.3 Под ссылкой View API Signature переписань в файл конфигурации значения API Username,  API Password, Signature в соответствующие поля.

Создание платежа
$paypal = new \PayPal\Payment(
    \PayPal\Payment::$PAYPAL_USER, 
    \PayPal\Payment::$PAYPAL_PWD, 
    \PayPal\Payment::$PAYPAL_SIGNATURE, 
    false, 
    \PayPal\Payment::$PAYPAL_RETURN_URL, 
    \PayPal\Payment::$PAYPAL_CANCEL_URL,
    \PayPal\Payment::$PAYPAL_VERSION
);
$request = $paypal->CreateInvoice(0.05, 'rus', 'USD');
Отправка на оплату пользователю
$response = $paypal->ExpressPayment($request['response']);
Подтверждение оплаты получателем
$paypal->FinalizeInvoice($response['TOKEN']);
