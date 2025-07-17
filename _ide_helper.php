<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 11.44.7.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */
namespace Laravel\Socialite\Facades {
    /**
     * 
     *
     * @method array getScopes()
     * @method \Laravel\Socialite\Contracts\Provider scopes(array|string $scopes)
     * @method \Laravel\Socialite\Contracts\Provider setScopes(array|string $scopes)
     * @method \Laravel\Socialite\Contracts\Provider redirectUrl(string $url)
     * @see \Laravel\Socialite\SocialiteManager
     */
    class Socialite {
        /**
         * Get a driver instance.
         *
         * @param string $driver
         * @return mixed 
         * @static 
         */
        public static function with($driver)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->with($driver);
        }

        /**
         * Build an OAuth 2 provider instance.
         *
         * @param string $provider
         * @param array $config
         * @return \Laravel\Socialite\Two\AbstractProvider 
         * @static 
         */
        public static function buildProvider($provider, $config)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->buildProvider($provider, $config);
        }

        /**
         * Format the server configuration.
         *
         * @param array $config
         * @return array 
         * @static 
         */
        public static function formatConfig($config)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->formatConfig($config);
        }

        /**
         * Forget all of the resolved driver instances.
         *
         * @return \Laravel\Socialite\SocialiteManager 
         * @static 
         */
        public static function forgetDrivers()
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->forgetDrivers();
        }

        /**
         * Set the container instance used by the manager.
         *
         * @param \Illuminate\Contracts\Container\Container $container
         * @return \Laravel\Socialite\SocialiteManager 
         * @static 
         */
        public static function setContainer($container)
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->setContainer($container);
        }

        /**
         * Get the default driver name.
         *
         * @return string 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function getDefaultDriver()
        {
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->getDefaultDriver();
        }

        /**
         * Get a driver instance.
         *
         * @param string|null $driver
         * @return mixed 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function driver($driver = null)
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->driver($driver);
        }

        /**
         * Register a custom driver creator Closure.
         *
         * @param string $driver
         * @param \Closure $callback
         * @return \Laravel\Socialite\SocialiteManager 
         * @static 
         */
        public static function extend($driver, $callback)
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->extend($driver, $callback);
        }

        /**
         * Get all of the created "drivers".
         *
         * @return array 
         * @static 
         */
        public static function getDrivers()
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->getDrivers();
        }

        /**
         * Get the container instance used by the manager.
         *
         * @return \Illuminate\Contracts\Container\Container 
         * @static 
         */
        public static function getContainer()
        {
            //Method inherited from \Illuminate\Support\Manager 
            /** @var \Laravel\Socialite\SocialiteManager $instance */
            return $instance->getContainer();
        }

            }
    }

namespace PragmaRX\Google2FALaravel {
    /**
     * 
     *
     */
    class Facade {
        /**
         * Set the QRCode Backend.
         *
         * @param string $qrCodeBackend
         * @return self 
         * @static 
         */
        public static function setQrCodeBackend($qrCodeBackend)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setQrCodeBackend($qrCodeBackend);
        }

        /**
         * Authenticator boot.
         *
         * @param $request
         * @return \Google2FA 
         * @static 
         */
        public static function boot($request)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->boot($request);
        }

        /**
         * The QRCode Backend.
         *
         * @return mixed 
         * @static 
         */
        public static function getQRCodeBackend()
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getQRCodeBackend();
        }

        /**
         * Check if the 2FA is activated for the user.
         *
         * @return bool 
         * @static 
         */
        public static function isActivated()
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->isActivated();
        }

        /**
         * Set current auth as valid.
         *
         * @static 
         */
        public static function login()
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->login();
        }

        /**
         * OTP logout.
         *
         * @static 
         */
        public static function logout()
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->logout();
        }

        /**
         * Verify the OTP.
         *
         * @param $secret
         * @param $one_time_password
         * @return mixed 
         * @static 
         */
        public static function verifyGoogle2FA($secret, $one_time_password)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->verifyGoogle2FA($secret, $one_time_password);
        }

        /**
         * Generates a QR code data url to display inline.
         *
         * @param string $company
         * @param string $holder
         * @param string $secret
         * @param int $size
         * @param string $encoding Default to UTF-8
         * @return string 
         * @static 
         */
        public static function getQRCodeInline($company, $holder, $secret, $size = 200, $encoding = 'utf-8')
        {
            //Method inherited from \PragmaRX\Google2FAQRCode\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getQRCodeInline($company, $holder, $secret, $size, $encoding);
        }

        /**
         * Service setter
         *
         * @return \PragmaRX\Google2FAQRCode\QRCode\QRCodeServiceContract 
         * @static 
         */
        public static function getQrCodeService()
        {
            //Method inherited from \PragmaRX\Google2FAQRCode\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getQrCodeService();
        }

        /**
         * Service setter
         *
         * @return self 
         * @static 
         */
        public static function setQrCodeService($service)
        {
            //Method inherited from \PragmaRX\Google2FAQRCode\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setQrCodeService($service);
        }

        /**
         * Create the QR Code service instance
         *
         * @return \PragmaRX\Google2FAQRCode\QRCode\QRCodeServiceContract 
         * @static 
         */
        public static function qrCodeServiceFactory($imageBackEnd = null)
        {
            //Method inherited from \PragmaRX\Google2FAQRCode\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->qrCodeServiceFactory($imageBackEnd);
        }

        /**
         * Find a valid One Time Password.
         *
         * @param string $secret
         * @param string $key
         * @param int|null $window
         * @param int $startingTimestamp
         * @param int $timestamp
         * @param int|null $oldTimestamp
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return bool|int 
         * @static 
         */
        public static function findValidOTP($secret, $key, $window, $startingTimestamp, $timestamp, $oldTimestamp = null)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->findValidOTP($secret, $key, $window, $startingTimestamp, $timestamp, $oldTimestamp);
        }

        /**
         * Generate a digit secret key in base32 format.
         *
         * @param int $length
         * @param string $prefix
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @return string 
         * @static 
         */
        public static function generateSecretKey($length = 16, $prefix = '')
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->generateSecretKey($length, $prefix);
        }

        /**
         * Get the current one time password for a key.
         *
         * @param string $secret
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return string 
         * @static 
         */
        public static function getCurrentOtp($secret)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getCurrentOtp($secret);
        }

        /**
         * Get the HMAC algorithm.
         *
         * @return string 
         * @static 
         */
        public static function getAlgorithm()
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getAlgorithm();
        }

        /**
         * Get key regeneration.
         *
         * @return int 
         * @static 
         */
        public static function getKeyRegeneration()
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getKeyRegeneration();
        }

        /**
         * Get OTP length.
         *
         * @return int 
         * @static 
         */
        public static function getOneTimePasswordLength()
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getOneTimePasswordLength();
        }

        /**
         * Get secret.
         *
         * @param string|null $secret
         * @return string 
         * @static 
         */
        public static function getSecret($secret = null)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getSecret($secret);
        }

        /**
         * Returns the current Unix Timestamp divided by the $keyRegeneration
         * period.
         *
         * @return int 
         * @static 
         */
        public static function getTimestamp()
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getTimestamp();
        }

        /**
         * Get the OTP window.
         *
         * @param null|int $window
         * @return int 
         * @static 
         */
        public static function getWindow($window = null)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getWindow($window);
        }

        /**
         * Takes the secret key and the timestamp and returns the one time
         * password.
         *
         * @param string $secret Secret key in binary form.
         * @param int $counter Timestamp as returned by getTimestamp.
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return string 
         * @static 
         */
        public static function oathTotp($secret, $counter)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->oathTotp($secret, $counter);
        }

        /**
         * Extracts the OTP from the SHA1 hash.
         *
         * @param string $hash
         * @return string 
         * @static 
         */
        public static function oathTruncate($hash)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->oathTruncate($hash);
        }

        /**
         * Remove invalid chars from a base 32 string.
         *
         * @param string $string
         * @return string|null 
         * @static 
         */
        public static function removeInvalidChars($string)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->removeInvalidChars($string);
        }

        /**
         * Setter for the enforce Google Authenticator compatibility property.
         *
         * @param mixed $enforceGoogleAuthenticatorCompatibility
         * @return \PragmaRX\Google2FALaravel\Google2FA 
         * @static 
         */
        public static function setEnforceGoogleAuthenticatorCompatibility($enforceGoogleAuthenticatorCompatibility)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setEnforceGoogleAuthenticatorCompatibility($enforceGoogleAuthenticatorCompatibility);
        }

        /**
         * Set the HMAC hashing algorithm.
         *
         * @param mixed $algorithm
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidAlgorithmException
         * @return \PragmaRX\Google2FA\Google2FA 
         * @static 
         */
        public static function setAlgorithm($algorithm)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setAlgorithm($algorithm);
        }

        /**
         * Set key regeneration.
         *
         * @param mixed $keyRegeneration
         * @static 
         */
        public static function setKeyRegeneration($keyRegeneration)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setKeyRegeneration($keyRegeneration);
        }

        /**
         * Set OTP length.
         *
         * @param mixed $oneTimePasswordLength
         * @static 
         */
        public static function setOneTimePasswordLength($oneTimePasswordLength)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setOneTimePasswordLength($oneTimePasswordLength);
        }

        /**
         * Set secret.
         *
         * @param mixed $secret
         * @static 
         */
        public static function setSecret($secret)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setSecret($secret);
        }

        /**
         * Set the OTP window.
         *
         * @param mixed $window
         * @static 
         */
        public static function setWindow($window)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setWindow($window);
        }

        /**
         * Verifies a user inputted key against the current timestamp. Checks $window
         * keys either side of the timestamp.
         *
         * @param string $key User specified key
         * @param string $secret
         * @param null|int $window
         * @param null|int $timestamp
         * @param null|int $oldTimestamp
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return bool|int 
         * @static 
         */
        public static function verify($key, $secret, $window = null, $timestamp = null, $oldTimestamp = null)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->verify($key, $secret, $window, $timestamp, $oldTimestamp);
        }

        /**
         * Verifies a user inputted key against the current timestamp. Checks $window
         * keys either side of the timestamp.
         *
         * @param string $secret
         * @param string $key User specified key
         * @param int|null $window
         * @param null|int $timestamp
         * @param null|int $oldTimestamp
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return bool|int 
         * @static 
         */
        public static function verifyKey($secret, $key, $window = null, $timestamp = null, $oldTimestamp = null)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->verifyKey($secret, $key, $window, $timestamp, $oldTimestamp);
        }

        /**
         * Verifies a user inputted key against the current timestamp. Checks $window
         * keys either side of the timestamp, but ensures that the given key is newer than
         * the given oldTimestamp. Useful if you need to ensure that a single key cannot
         * be used twice.
         *
         * @param string $secret
         * @param string $key User specified key
         * @param int|null $oldTimestamp The timestamp from the last verified key
         * @param int|null $window
         * @param int|null $timestamp
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return bool|int 
         * @static 
         */
        public static function verifyKeyNewer($secret, $key, $oldTimestamp, $window = null, $timestamp = null)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->verifyKeyNewer($secret, $key, $oldTimestamp, $window, $timestamp);
        }

        /**
         * Creates a QR code url.
         *
         * @param string $company
         * @param string $holder
         * @param string $secret
         * @return string 
         * @static 
         */
        public static function getQRCodeUrl($company, $holder, $secret)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getQRCodeUrl($company, $holder, $secret);
        }

        /**
         * Generate a digit secret key in base32 format.
         *
         * @param int $length
         * @param string $prefix
         * @throws \Exception
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return string 
         * @static 
         */
        public static function generateBase32RandomKey($length = 16, $prefix = '')
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->generateBase32RandomKey($length, $prefix);
        }

        /**
         * Decodes a base32 string into a binary string.
         *
         * @param string $b32
         * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
         * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
         * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
         * @return string 
         * @static 
         */
        public static function base32Decode($b32)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->base32Decode($b32);
        }

        /**
         * Encode a string to Base32.
         *
         * @param string $string
         * @return string 
         * @static 
         */
        public static function toBase32($string)
        {
            //Method inherited from \PragmaRX\Google2FA\Google2FA 
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->toBase32($string);
        }

        /**
         * Get a config value.
         *
         * @param $string
         * @throws \Exception
         * @return mixed 
         * @static 
         */
        public static function config($string, $default = null)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->config($string, $default);
        }

        /**
         * Get the request property.
         *
         * @return mixed 
         * @static 
         */
        public static function getRequest()
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->getRequest();
        }

        /**
         * Set the request property.
         *
         * @param mixed $request
         * @return \PragmaRX\Google2FALaravel\Google2FA 
         * @static 
         */
        public static function setRequest($request)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setRequest($request);
        }

        /**
         * Get a session var value.
         *
         * @param null $var
         * @return mixed 
         * @static 
         */
        public static function sessionGet($var = null, $default = null)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->sessionGet($var, $default);
        }

        /**
         * 
         *
         * @param mixed $stateless
         * @return \PragmaRX\Google2FALaravel\Authenticator 
         * @static 
         */
        public static function setStateless($stateless = true)
        {
            /** @var \PragmaRX\Google2FALaravel\Google2FA $instance */
            return $instance->setStateless($stateless);
        }

            }
    }

namespace Illuminate\Http {
    /**
     * 
     *
     */
    class Request {
        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param string $errorBag
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @static 
         */
        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @static 
         */
        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

            }
    }


namespace  {
    class Helper extends \App\Helpers\Helpers {}
    class Socialite extends \Laravel\Socialite\Facades\Socialite {}
    class Google2FA extends \PragmaRX\Google2FALaravel\Facade {}
}





