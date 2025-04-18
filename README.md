# T id клиент

Клиент для общения с [API t id](https://developer.tbank.ru/docs/products/TID/w2w).

## Установка

Минимальная версия PHP: 8.3

1. Выполнить `composer require vanta/t-id-client`
2. Обязательно установить PSR-совместимый клиент

## Использование

Создание инстанса клиента:

```php
use GuzzleHttp\Client;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\RestClientBuilder;

$restClientBuilder = RestClientBuilder::create(new ConfigurationClient('ваш ClientId', 'ваш ClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'), new Client());

$restUserClient = $restClientBuilder->createUserClient();

$restDocumentClient = $restClientBuilder->createDocumentClient();

$restUserStatusClient = $restClientBuilder->createUserStatusClient();
```

Генерация URL для авторизации пользователя:

```php
use GuzzleHttp\Client;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\RestClientBuilder;

RestClientBuilder::create(new ConfigurationClient('ваш ClientId', 'ваш ClientSecret', 'https://id.tbank.ru', 'https://business.tbank.ru'), new Client());
    ->createAuthUrlBuilder('https://id.tbank.ru', 'ваш redirectUrl')
    ->build()
;
```

Получение данных о пользователе:

```php
$pairKey = $restUserClient->getPairKeyByAuthorizationCode('get параметр code, из url-а, на который вернулся пользователь', 'ваш redirectUrl');

$restUserClient->getUser($pairKey->accessToken);
```

Тестовая среда:

```php
use GuzzleHttp\Client;
use Vanta\Integration\TId\Infrastructure\HttpClient\ConfigurationClient;
use Vanta\Integration\TId\RestClientBuilder;

$restDocumentClient = $restClientBuilder
    ->addMiddleware(new SandboxBusinessClientMiddleware())
    ->createDocumentClient()
;

$restUserStatusClient = $restClientBuilder
    ->addMiddleware(new SandboxBusinessClientMiddleware())
    ->createUserStatusClient()
;

$pairKey = PairKey::createSandboxPairKey();

$restDocumentClient->getAddress($pairKey->accessToken, AddressType::RESIDENCE_ADDRESS);
```

Тестовая среда имеет следующие ограничения:
```text
1. Не работает url авторизации пользователя
2. Не работает запрос $restUserClient->getPairKeyByAuthorizationCode('get параметр code, из url-а, на который вернулся пользователь', 'ваш redirectUrl');
3. Не работает запрос данных пользователя $restUserClient->getUser($pairKey->accessToken);
4. API т-банка в запросе $restUserClient->getAuthIntrospect($pairKey->accessToken);,
на тесте возвращает только 1 поле: active, на prod-е возвращает все поля, решили сделать все поля ответа обязательными 
```
