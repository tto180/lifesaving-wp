<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Encryption;

interface StringEncrypter
{
    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \StellarWP\Learndash\Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString($value);

    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \StellarWP\Learndash\Illuminate\Contracts\Encryption\DecryptException
     */
    public function decryptString($payload);
}
