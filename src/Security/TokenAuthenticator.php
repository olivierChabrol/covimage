<?php
// src/Security/TokenAuthenticator.php

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    // ...

    public function getCredentials(Request $request)
    {
        // ...

        if ($token == 'Anon') {
            throw new CustomUserMessageAuthenticationException(
                'Echec'
            );
        }

        // ...
    }

    // ...
}