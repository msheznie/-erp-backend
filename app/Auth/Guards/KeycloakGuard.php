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
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\Clock\SystemClock;

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
                Log::warning('Keycloak token missing principal attribute: ' . $principalAttribute);
                return null;
            }

            // Load user from database if configured
            if (config('keycloak.load_user_from_database', true)) {
                $credential = config('keycloak.user_provider_credential', 'username');
                
                // Try to find user by the configured credential
                // Common fields: username, email, empID
                $this->user = $this->provider->retrieveByCredentials([
                    $credential => $userIdentifier
                ]);
                
                // If not found and credential is 'username', try email or empID
                if (!$this->user && $credential === 'username') {
                    $this->user = $this->provider->retrieveByCredentials([
                        'email' => $userIdentifier
                    ]);
                    
                    if (!$this->user) {
                        $this->user = $this->provider->retrieveByCredentials([
                            'empID' => $userIdentifier
                        ]);
                    }
                }
            } else {
                // Create a temporary user from token (not recommended for production)
                $this->user = $this->provider->retrieveByCredentials([
                    'email' => $userIdentifier
                ]);
            }

            // Append decoded token to user if configured
            if (config('keycloak.append_decoded_token', false) && $this->user) {
                $this->user->keycloak_token = $decodedToken;
            }

            return $this->user;

        } catch (\Exception $e) {
            Log::error('Keycloak authentication error: ' . $e->getMessage());
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
                Log::error('Keycloak realm public key not configured');
                return null;
            }

            // Format the public key (add headers if needed)
            $publicKey = $this->formatPublicKey($realmPublicKey);

            // Create JWT configuration
            $configuration = Configuration::forAsymmetricSigner(
                new Sha256(),
                InMemory::plainText(''), // Private key not needed for validation
                InMemory::plainText($publicKey)
            );

            // Set validation constraints
            $configuration->setValidationConstraints(
                new SignedWith($configuration->signer(), $configuration->verificationKey()),
                new StrictValidAt(SystemClock::fromSystemTimezone())
            );

            // Parse and validate token
            $parsedToken = $configuration->parser()->parse($token);

            $constraints = $configuration->validationConstraints();

            if (!$configuration->validator()->validate($parsedToken, ...$constraints)) {
                Log::warning('Keycloak token validation failed');
                return null;
            }

            // Check if token is expired
            if ($parsedToken->isExpired(new \DateTimeImmutable())) {
                Log::warning('Keycloak token is expired');
                return null;
            }

            return $parsedToken;

        } catch (RequiredConstraintsViolated $e) {
            Log::warning('Keycloak token constraints violated: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Keycloak token validation error: ' . $e->getMessage());
            return null;
        }
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
