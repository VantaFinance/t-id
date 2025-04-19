<?php

declare(strict_types=1);

namespace Vanta\Integration\TId;

use Psr\Http\Client\ClientExceptionInterface as ClientException;
use Vanta\Integration\TId\Response\AuthIntrospect;
use Vanta\Integration\TId\Response\PairKey;
use Vanta\Integration\TId\Response\User;

interface UserClient
{
    /**
     * @param non-empty-string $code        - get параметр code, из url-а, на который вернулся пользователь после авторизации в t-банке
     * @param non-empty-string $redirectUri - тот же, что и в url-е авторизации, иначе будет ошибка invalid_grant
     *
     * @throws ClientException
     */
    public function getPairKeyByAuthorizationCode(string $code, string $redirectUri): PairKey;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getUser(string $accessToken): User;

    /**
     * @param non-empty-string $accessToken
     *
     * @throws ClientException
     */
    public function getAuthIntrospect(string $accessToken): AuthIntrospect;
}
