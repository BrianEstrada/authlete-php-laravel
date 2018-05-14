<?php
//
// Copyright (C) 2018 Authlete, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing,
// software distributed under the License is distributed on an
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
// either express or implied. See the License for the specific
// language governing permissions and limitations under the
// License.
//


/**
 * File containing the definition of DefaultTokenRequestHandlerSpi class.
 */


namespace Authlete\Laravel\Handler\Spi;


use App\User;
use Illuminate\Support\Facades\Auth;


/**
 * An implementation of TokenRequestHandlerSpi that uses Laravel's
 * standard authentication mechanism.
 */
class DefaultTokenRequestHandlerSpi extends TokenRequestHandlerSpiAdapter
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     *
     * @param string $username
     *     {@inheritdoc}
     *
     * @param string $password
     *     {@inheritdoc}
     */
    public function authenticateUser($username, $password)
    {
        // The database column for unique user identifiers.
        $field = 'email';

        // Credentials used for user authentication.
        $credentials = array(
            $field     => $username,
            'password' => $password
        );

        // If the credentials are not valid.
        if (Auth::validate($credentials) === false)
        {
            // No user has the credentials.
            return null;
        }

        // The user who has the $username as the identifier.
        $user = User::where($field, $username)->first();

        // Return the subject (unique identifier) of the user.
        return $user->getAuthIdentifier();
    }
}
?>
