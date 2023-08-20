# Iiko Transport Client
[![logo](https://api-ru.iiko.services/docs/logo)](https://api-ru.iiko.services)

## Инициализация 
```php
use TeaEagle\IikoTransport\App AS IikoTransport;

$app = new IikoTransport('<API login>');
```

## Установить организацию для запросов 
```php
$app->setOrganization('ce81fbf7-e09b-4fa7-8c61-0e8d4d0e080a');
```
Установленная организация отправляется не во все запросы. 
Отправка в запросах: 
- Клиет 
- Заказ 
- Номенклатура (Меню) 

## Получить новый токен авторизации 
**Время жизни токена 60 минут** 
```php
$token = $app->token->update();
```

## Организации 
### Полный ответ списка организации 
```php
$organizations = $app->organization->result();
```
### ID первой организации из списка. 
Еcли установлена организация функцией `setOrganization`, то у нее приоритет больше. 
```php
$organizations = $app->organization->update();
```
### Список организацй 
```php
$organizations = $app->organization->list();
```
### Список идентификаторов организаций 
```php
$organizations = $app->organization->getOrganizationIds();
```

## Терминалы 
### Полный ответ всех терминалов 
```php
$terminals = $app->terminal->result();
```
### Список терминалов 
```php
$terminals = $app->terminal->list();
```

## Города 
### Полный ответ списка городов 
```php
$cities = $app->city->result();
```
### Список городов 
```php
$cities = $app->city->list();
```

## Улицы 
### Полный список улиц по всем городам 
```php
$streets = $app->street->list();
```

## Клиент 
### Поиск клиента по номеру телефона 
Маска: +79999999999 
```php
$customer = $app->customer->info('<Номер телефона>');
```
### Получение баланса по ID клиента 
```php
$balance = $app->customer->balance('13c2653e-df48-4618-a733-ac6045b635a0');
```
### Создать или обновить клиента 
Имя необязательный параметр. Вернёт ID клиента. 
```php
$customerId = $app->customer->createOrUpdate('+70000000000', 'UserName');
```
### Обновить имя и номер телефона клиента 
Вернёт ID клиента. 
```php
$customerId = $app->customer->update('13c2653e-df48-4618-a733-ac6045b635a0', '+70000000000', 'UserName');
```

## Типы доставки 
### Полный ответ списка типов доставки 
```php
$deliveryTypes = $app->deliveryType->result();
```
### Список типов доставки 
```php
$deliveryTypes = $app->deliveryType->list();
```

## Типы оплаты 
### Полный ответ списка типов оплат 
```php
$paymentTypes = $app->paymentType->result();
```
### Список типов оплат 
```php
$paymentTypes = $app->paymentType->list();
```

## Товары 
### Полный ответ списка товаров 
```php
$products = $app->product->result();
```
### Список товаров 
```php
$products = $app->product->list();
```
### Список групп 
```php
$products = $app->product->groups();
```
### Список категорий 
```php
$products = $app->product->categories();
```
### Список размеров 
```php
$products = $app->product->sizes();
```

## Ограничения 
### Полный ответ списка ограничений 
```php
$restrictions = $app->restrictions->result();
```
### Список ограничений 
```php
$restrictions = $app->restrictions->list();
```
### Список зон доставки 
```php
$restrictions = $app->restrictions->deliveryZones();
```

## Проверка доставки 
```php
$checkDelivery = $app->newCheckDelivery();
// Список товаров
$checkDelivery->setProducts([
	[
		'id' => '8f3c6904-fa9b-4e6a-ba5e-cf02767a4efc',
		'amount' => 1,
		'product' => 'Латте',
		'modifiers' => [
			[
				'id' => '67b3ff12-1284-4410-9cda-ed2e39e18938',
				'amount' => 1,
				'product' => 'Мёд',
			],
		],
	],
	[
		'id' => '66c6bc33-2360-4e49-98bb-baa821820882',
		'amount' => 3,
		'product' => 'Трубочка со сгущенкой',
	],
]);

// Адрес
$checkDelivery->setCity('Хабаровск');
$checkDelivery->setStreet('Волочаевская');
$checkDelivery->setHouse('25');

// Координаты
$checkDelivery->setLatitude('48.470296');
$checkDelivery->setLongitude('135.079202');

// Сумма заказа
$checkDelivery->setSum(1055.55);

// Время заказа
$checkDelivery->setDeliveryDate('2023-01-01 10:00:00.000');

// Вернёт массив готовый к запросу
$array = $checkDelivery->toArray();

// Сделать запрос - можно ли осуществить доставку
$result = $checkDelivery->send();
```
**Переданные координаты имеют больший приоритет, чем адрес** 
**Время заказа передавать в формате: yyyy-MM-dd HH:mm:ss.fff** 

## Заказ 
```php
$order = $app->newOrder();
$order->setRealOrderId('121');
$order->setPhone('+70000000000');
// Для доставки терминал обязательно
$order->setTerminal('19301deb-b7b5-43ce-92ef-1ecd24edab2a');
$order->setCustomer('UserName', '13c2653e-df48-4618-a733-ac6045b635a0');
$order->setComment('Комментарий');

// Количество персон
$order->setCountGuests(2);

// Список товаров
foreach ($items as $key => $item) {
    // Товар
    $product = $app->newProduct();
    $product->setAmount($item->count);
    $product->setId($item->uuid);

    // Модификаторы
    if (!empty($item->modifiers) && is_array($item->modifiers)) {
        foreach ($item->modifiers as $keyChild => $modItem) {
            $modifier = $app->newModifier();
            $modifier->setId($modItem->uuid);
            $modifier->setAmount($modItem->count);
            $modifier->setGroup($modItem->group);
            $product->setModifier($modifier);
        }
    }

    $order->setProduct($product);
}

// Доставка
if ($isPickup === false) {
    // Доставка
    $order->isDelivery(true);

    // Адрес доставки
    $address = $app->newAddress();
    $address->setCity('Хабаровск');
    $address->setStreet('Волочаевская');
    $address->setHouse('25');
    $address->setEntrance('1');
    $address->setFlat('5');
    $address->setFloor('3');
    $address->setDoorphone('Yes');

    $order->setAddress($address);
}

// Оплата бонусами
if ($bonuses) {
    $payment = $app->newPayment();
    $payment->setIsIikoCard();
    $payment->setPaymentTypeId('43259388-c317-4bd5-a81a-d7ccb7c0b892');
    $payment->setSum(100);
    $payment->setPhone('+70000000000');

    $order->setPayment($payment);
}

// Оплата картой
$payment = $app->newPayment();
$payment->setIsCard();
$payment->setPaymentTypeId('faa09787-9403-467e-829a-1bb6de306e6f');
$payment->setSum('330');
$payment->setIsProcessedExternally();
$order->setPayment($payment);

// Оплата наличными
$payment = $app->newPayment();
$payment->setIsCash();
$payment->setPaymentTypeId('3955270a-f681-48db-9b95-dbdbc3e4da5f');
$payment->setSum('330');
$order->setPayment($payment);

// Тип заказа
$order->setOrderType('31feba48-6eaf-43c4-80b3-a34e2f7d393b');

// Вернёт массив готовый к запросу
$array = $order->toArray();

$doOrder = $order->send();
```

## License 

MIT 