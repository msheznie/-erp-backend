<?php

namespace App\Auth\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\Clock\SystemClock;
use Illuminate\Auth\GenericUser;

class KeycloakGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $provider;
    protected $config;

    public function __construct(UserProvider $provider, Request $request, array $config = [])
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenFromRequest();

        if (!$token) {
            return null;
        }

        try {
            $decodedToken = $this->validateToken($token);

            if (!$decodedToken) {
                return null;
            }

            // Get user identifier from token
            $principalAttribute = config('keycloak.token_principal_attribute', 'preferred_username');
            $userIdentifier = $decodedToken->claims()->get($principalAttribute);

            if (!$userIdentifier) {
                Log::channel('keycloak')->warning('Keycloak token missing principal attribute: ' . $principalAttribute);
                return null;
            }

            // Load user from database if configured
            if (config('keycloak.load_user_from_database', true)) {
                $credential = config('keycloak.user_provider_credential', 'email');

                // Try configured credential first, then username, email, empID (preferred_username may match any)
                $this->user = $this->provider->retrieveByCredentials([$credential => $userIdentifier]);
                if (!$this->user) {
                    $this->user = $this->provider->retrieveByCredentials(['username' => $userIdentifier]);
                }
                if (!$this->user) {
                    $this->user = $this->provider->retrieveByCredentials(['email' => $userIdentifier]);
                }
                if (!$this->user) {
                    $this->user = $this->provider->retrieveByCredentials(['empID' => $userIdentifier]);
                }
            } else {
                $this->user = $this->provider->retrieveByCredentials(['username' => $userIdentifier]);
                if (!$this->user) {
                    $this->user = $this->provider->retrieveByCredentials(['email' => $userIdentifier]);
                }
                if (!$this->user) {
                    $this->user = $this->provider->retrieveByCredentials(['empID' => $userIdentifier]);
                }
            }

            // Token valid but no user in DB: optionally accept via GenericUser so request is authenticated
            if (!$this->user && config('keycloak.accept_token_without_user', false)) {
                Log::channel('keycloak')->warning('Keycloak token valid but no matching user in DB; using token-only user for: ' . $userIdentifier);
                $this->user = $this->createUserFromToken($decodedToken, $userIdentifier);
            } elseif (!$this->user) {
                Log::channel('keycloak')->warning('Keycloak token valid but no matching user in database (principal: ' . $userIdentifier . '). Add user or set KEYCLOAK_ACCEPT_TOKEN_WITHOUT_USER=true.');
            }

            // Append decoded token to user if configured
            if (config('keycloak.append_decoded_token', false) && $this->user) {
                $this->user->keycloak_token = $decodedToken;
            }

            return $this->user;

        } catch (\Exception $e) {
            Log::channel('keycloak')->error('Keycloak authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return !is_null($this->user());
    }

    /**
     * Get the token from the request.
     *
     * @return string|null
     */
    protected function getTokenFromRequest()
    {
        $token = $this->request->bearerToken();

        if (!$token) {
            $token = $this->request->header('Authorization');
            if ($token && str_starts_with($token, 'Bearer ')) {
                $token = substr($token, 7);
            } else {
                $token = null;
            }
        }

        return $token;
    }

    /**
     * Validate and decode the JWT token.
     *
     * @param string $token
     * @return \Lcobucci\JWT\UnencryptedToken|null
     */
    protected function validateToken($token)
    {
        try {
            $realmPublicKey = config('keycloak.realm_public_key');
            
            if (empty($realmPublicKey)) {
                Log::channel('keycloak')->error('Keycloak realm public key not configured');
                return null;
            }

            // Format the public key (add headers if needed)
            $publicKey = $this->formatPublicKey($realmPublicKey);

            $keyContent = trim(preg_replace('/-----BEGIN PUBLIC KEY-----|-----END PUBLIC KEY-----|\s+/', '', $publicKey));
            if ($keyContent === '') {
                Log::channel('keycloak')->error('Keycloak realm public key is empty or invalid after formatting');
                return null;
            }

            // Create JWT configuration (verification only; signing key is unused but must be non-empty per library)
            $configuration = Configuration::forAsymmetricSigner(
                new Sha256(),
                InMemory::plainText($publicKey),
                InMemory::plainText($publicKey)
            );

            // Set validation constraints (LooseValidAt allows optional nbf/iat; Keycloak often omits nbf)
            $configuration->setValidationConstraints(
                new SignedWith($configuration->signer(), $configuration->verificationKey()),
                new LooseValidAt(SystemClock::fromSystemTimezone())
            );

            // Parse and validate token (use assert to get detailed violation messages)
            $parsedToken = $configuration->parser()->parse($token);

            $constraints = $configuration->validationConstraints();
            $configuration->validator()->assert($parsedToken, ...$constraints);

            // Check if token is expired
            if ($parsedToken->isExpired(new \DateTimeImmutable())) {
                Log::channel('keycloak')->warning('Keycloak token is expired');
                return null;
            }

            return $parsedToken;

        } catch (RequiredConstraintsViolated $e) {
            Log::channel('keycloak')->warning('Keycloak token validation failed: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::channel('keycloak')->error('Keycloak token validation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build a minimal authenticatable user from the token when no DB user exists.
     *
     * @param \Lcobucci\JWT\UnencryptedToken $decodedToken
     * @param string $userIdentifier
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function createUserFromToken($decodedToken, $userIdentifier)
    {
        $sub = $decodedToken->claims()->get('sub', $userIdentifier);

        return new GenericUser([
            'id' => $sub,
            'email' => $decodedToken->claims()->get('email', $userIdentifier),
            'name' => $userIdentifier,
            'password' => '',
            'remember_token' => '',
        ]);
    }

    /**
     * Format the public key for JWT validation.
     *
     * @param string $key
     * @return string
     */
    protected function formatPublicKey($key)
    {
        // Remove any existing headers
        $key = preg_replace('/-----BEGIN (.*) KEY-----/', '', $key);
        $key = preg_replace('/-----END (.*) KEY-----/', '', $key);
        $key = preg_replace('/\s+/', '', $key);

        // Add proper headers
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split($key, 64, "\n") . "-----END PUBLIC KEY-----";
    }
}
