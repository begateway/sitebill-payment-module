# Модуль оплаты для Sitebill

## Установка модуля

  * Создайте резервную копию Sitebill и базы данных
  * Загрузите [архив](https://github.com/beGateway/sitebill-payment-module/raw/master/sitebill-payment-module.zip) и переместите папку `apps` из архива в корень Sitebill

## Настройка модуля

1. В разделе администрирования Sitebill нажмите `Настройки` и выберите закладку `beGateway`

  ![Шаг 1](https://github.com/beGateway/sitebill-payment-module/raw/master/doc/pic1.png)

   Если закладка не видна, то в разделе администрирования Sitebill нажмите `Приложения` и выберите в списке `beGateway`

  ![Шаг 1](https://github.com/beGateway/sitebill-payment-module/raw/master/doc/pic1_1.png)

   После этого закладка `beGateway` появится в разделе `Настройки`

2. Задайте настройки модуля:

  * в `Включить приложение beGateway` введите __1__
  * в `Описание способа оплаты для клиента` введите описание сопособа
    оплаты, который будет показан клиенту. Например, __Оплатить с помощью банковской карты VISA, MasterCard__
  * в `Описание способа оплаты для клиента на английском`. Например, __Pay by credit or debit card__
  * в `Домен платежного шлюза`. Домен платежного шлюза платежной
    системы.
  * в `Домен страницы оплаты`. Домен страницы оплаты платежной системы.
  * в `Id магазина`. Id вашего магазина в платежной системе.
  * в `Секретный ключ магазина`. Секретный ключ вашего магазина.

3. По завершению редактирования нажмите кнопку `Сохранить`
4. Отредактируйте файл `apps/system/lib/system/user/account.php` в вашей
   установленной копии Sitebill, добавив после кода

```php
if ( $this->getConfigValue('apps.paypal.enable') ) {
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/paypal/admin/admin.php');
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/paypal/site/site.php');
  $paypal_site = new paypal_site();

  $rs .= $paypal_site->get_pay_button($bill_id, $bill_sum, $bill_payment_sum);
}
```

следующий код

```php
if ( $this->getConfigValue('apps.begateway.enable') ) {
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/begateway/admin/admin.php');
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/begateway/site/site.php');
  $begateway_site = new begateway_site();

  $rs .= $begateway_site->get_pay_button($bill_id, $bill_sum, $bill_payment_sum);
}
```

Для того, чтобы отключить встроенный способ оплаты через Robokassa,
закоментируйте следующий код, заменив

```php
$rs .= $this->jumpToRobokassa($bill_id);
```

на

```php
# $rs .= $this->jumpToRobokassa($bill_id);
```


## Примечания

Требуется PHP 5.3+

### Тестовые данные

Вы можете использовать следующие данные, чтобы настроить способ оплаты в
тестовом режиме:

  * `Id магазина` __361__
  * `Секретный ключ магазина` __b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d__
  * `Домен платежного шлюза` __demo-gateway.begateway.com__
  * `Домен страницы оплаты` __checkout.begateway.com__

Используйте следующий тестовый набор для тестового платежа:

  * номер карты __4200000000000000__
  * имя на карте __John Doe__
  * месяц срока действия карты __01__, чтобы получить успешный платеж
  * месяц срока действия карты __10__, чтобы получить неуспешный платеж
  * CVC __123__

## Нашли ошибку или у вас есть предложение по улучшению модуля?

Создайте [запрос](https://github.com/beGateway/sitebill-payment-module/issues/new), в котором:

  * укажите наименование CMS и компонента магазина, а также их версии
  * укажите версию платежного модуля (доступна в поле кода обработчика)
  * опишите проблему или предложение
  * приложите снимок экрана (для большей информативности)


# Sitebill payment module

## Module install

  * Make Sitebill and database backups
  * Download [archive](https://github.com/beGateway/sitebill-payment-module/raw/master/sitebill-payment-module.zip) and upload the `apps` folder from the achive to your Sitebill installation folder.

## Module setup

1. In admin area of Sitebill click `Settings` and select the tab `beGateway`

  ![Шаг 1](https://github.com/beGateway/sitebill-payment-module/raw/master/doc/pic1_en.png)

   If the tab is not available, then select `Applications` in the Sitebill admin area and select `beGateway` in the appeared drop-down list.

  ![Шаг 1](https://github.com/beGateway/sitebill-payment-module/raw/master/doc/pic1_1_en.png)

   The tab `beGateway` will appear in `Settings`.

2. Module settings:

  * в `Enable beGateway application` type __1__
  * в `Payment method description` enter the payment method description. E.g. __Pay by credit or debit card__
  * в `Payment method description. Russian version`. E.g. __Оплатить с помощью банковской карты VISA, MasterCard__
  * в `Payment gateway domain`. Payment gateway domain of your payment processor.
  * в `Payment page domain`. Payment page domain of your payment processor.
  * в `Shop Id`. Your shop id in your payment processor.
  * в `Shop secret key`. Your shop secret key.

3. When you complete to edit the settings click `Save`
4. Edit the file `apps/system/lib/system/user/account.php` in your
   Sitebill installation to add after the code

```php
if ( $this->getConfigValue('apps.paypal.enable') ) {
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/paypal/admin/admin.php');
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/paypal/site/site.php');
  $paypal_site = new paypal_site();

  $rs .= $paypal_site->get_pay_button($bill_id, $bill_sum, $bill_payment_sum);
}
```

the code as follows

```php
if ( $this->getConfigValue('apps.begateway.enable') ) {
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/begateway/admin/admin.php');
  require_once (SITEBILL_DOCUMENT_ROOT.'/apps/begateway/site/site.php');
  $begateway_site = new begateway_site();

  $rs .= $begateway_site->get_pay_button($bill_id, $bill_sum, $bill_payment_sum);
}
```

To disable the Robokassa payment method installed in Sitebill by
default, change the code

```php
$rs .= $this->jumpToRobokassa($bill_id);
```

to

```php
# $rs .= $this->jumpToRobokassa($bill_id);
```

## Notes

Required PHP 5.3+

### Test data

You are free to use the settings to configure the module to process
payments with a demo gateway.

  * `Shop Id` __361__
  * `Shop secret key` __b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d__
  * `Payment gateway domain` __demo-gateway.begateway.com__
  * `Payment page domain` __checkout.begateway.com__

Use the test data to make a test payment:

  * card number __4200000000000000__
  * card name __John Doe__
  * card expiry month __01__ to get a success payment
  * card expiry month __10__ to get a failed payment
  * CVC __123__

### Contributing

Issue pull requests or send feature requests.
