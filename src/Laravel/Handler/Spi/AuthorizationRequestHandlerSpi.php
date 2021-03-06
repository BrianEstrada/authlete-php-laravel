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
 * File containing the definition of AuthorizationRequestHandlerSpi interface.
 */


namespace Authlete\Laravel\Handler\Spi;


use Authlete\Dto\Property;
use Authlete\Laravel\Handler\Spi\UserClaimProvider;


/**
 * The base interface for Service Provider Interfaces for authorization request
 * handlers.
 *
 * This interface defines common methods inherited by `NoInteractionHandlerSpi`
 * and `AuthorizationRequestDecisionHandlerSpi` interfaces.
 */
interface AuthorizationRequestHandlerSpi extends UserClaimProvider
{
    /**
     * Get the time when the end-user was authenticated.
     *
     * @return integer
     *     The time when the current end-user was authenticated. The number of
     *     seconds since the Unix epoch (1970-Jan-1). 0 means that the time is
     *     unknown.
     */
    public function getUserAuthenticatedAt();


    /**
     * Get the subject (= unique identifier) of the end-user.
     *
     * It must consist of only ASCII letters and its length must not exceed 100.
     *
     * @return string
     *     The subject of the end-user.
     */
    public function getUserSubject();


    /**
     * Get the value of the "sub" claim that will be embedded in an ID token.
     *
     * If this method returns `null`, the value returned from `getUserSubject()`
     * will be used.
     *
     * The main purpose of this method is to hide the actual value of the
     * subject from client applications.
     *
     * @return string
     *     The value of the `"sub"` claim.
     */
    public function getSub();


    /**
     * Get the authentication context class reference (ACR) that was satisfied
     * when the end-user was authenticated.
     *
     * The value returned from this method has an important meaning only when
     * the `"acr"` claim is requested as an essential claim. See
     * [5.5.1.1. Requesting the acr Claim](https://openid.net/specs/openid-connect-core-1_0.html#acrSemantics)
     * of [OpenID Connect Core 1.0](https://openid.net/specs/openid-connect-core-1_0.html)
     * for details.
     *
     * @return string
     *     The ACR that was satisfied when the end-user was authenticated. If
     *     your system does not recognize ACR, `null` should be returned.
     */
    public function getAcr();


    /**
     * Get arbitrary key-value pairs to be associated with an access token
     * and/or an authorization code.
     *
     * Properties returned from this method will appear as top-level entries
     * (unless they are marked as hidden) in a JSON response from the
     * authorization server as shown in
     * [5.1. Successful Response](https://tools.ietf.org/html/rfc6749#section-5.1)
     * of [RFC 6749](https://tools.ietf.org/html/rfc6749).
     *
     * Keys listed below should not be used and they would be ignored on
     * Authlete side even if they were used. It is because they are reserved
     * in [RFC 6749](https://tools.ietf.org/html/rfc6749) and
     * [OpenID Connect Core 1.0](https://openid.net/specs/openid-connect-core-1_0.html).
     *
     * * `access_token`
     * * `token_type`
     * * `expires_in`
     * * `refresh_token`
     * * `scope`
     * * `error`
     * * `error_description`
     * * `error_uri`
     * * `id_token`
     *
     * Note that there is an upper limit on the total size of properties.
     * On Authlete side, the properties will be (1) converted to a
     * multidimensional string array, (2) converted to JSON, (3) encrypted
     * by AES/CBC/PKCS5Padding, (4) encoded by base64url, and then stored
     * into the database. The length of the resultant string must not
     * exceed 65,535 in bytes. This is the upper limit, but we think it is
     * big enough.
     *
     * @return Property[]
     *     Arbitrary key-value pairs to be associated with an access token.
     */
    public function getProperties();


    /**
     * Get the scopes to be associated with an access token and/or an
     * authorization code.
     *
     * If `null` is returned, the scopes specified in the original
     * authorization request from the client application are used. In other
     * cases, the specified scopes by this method will replace the original
     * scopes.
     *
     * Even scopes that are not included in the original authorization request
     * can be specified. However, as an exception, the `openid` scope is
     * ignored on Authlete server side if it is not included in the original
     * request. It is because the existence of the `openid` scope considerably
     * changes the validation steps and because adding `openid` triggers
     * generation of an ID token (although the client application has not
     * requested it) and the behavior is a major violation against the
     * specification.
     *
     * If you add the `offline_access` scope although it is not included in
     * the original request, keep in mind that the specification requires
     * explicit consent from the end-user for the scope
     * ([11. Offline Access](https://openid.net/specs/openid-connect-core-1_0.html#OfflineAccess)
     * of [OpenID Connect Core 1.0](https://openid.net/specs/openid-connect-core-1_0.html)).
     *
     * When `offline_access` is included in the original authorization request,
     * the current implementation of Authlete's `/api/auth/authorization` API
     * checks whether the authorization request has come along with the
     * `prompt` request parameter and its value includes `consent`. However,
     * note that the implementation of Authlete's `/api/auth/authorization/issue`
     * API does not perform the same validation even if the `offline_access`
     * scope is newly added via this `scopes` parameter.
     *
     * @return string[]
     *     Scopes which replace the scopes of the original authorization
     *     request. If `null` is returned, the scopes will not be replaced.
     */
    public function getScopes();
}
?>
