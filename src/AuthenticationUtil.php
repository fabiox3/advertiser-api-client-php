<?php
namespace Zanox\AdvertiserApi;

class AuthenticationUtil
{
    /**
     * zanox connect ID
     *
     * @var string $connectId zanox connect id
     */
    private $connectId = '';

    /**
     * zanox shared secret key
     *
     * @var string $secretKey secret key to sign messages
     */
    private $secretKey = '';

    /**
     * Returns the connectId
     *
     * @return string zanox connect id
     */
    final public function getConnectId()
    {
        return $this->connectId;
    }

    /**
     * Set the connectId
     *
     * @param string $connectId zanox connectId
     */
    final public function setConnectId($connectId)
    {
        $this->connectId = $connectId;
    }

    /**
     * Set SecretKey
     *
     * @param string $secretKey zanox secret key
     *
     * @access public
     */
    final public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Returns hash based nonce.
     *
     * @see http://en.wikipedia.org/wiki/Cryptographic_nonce
     *
     * @return string md5 hash-based nonce
     */
    final public function getNonce()
    {
        $mt = microtime();
        $rand = mt_rand();
        return md5($mt . $rand);
    }

    /**
     * Returns the crypted hash signature for the message.
     *
     * Builds the signed string consisting of the rest action verb, the uri used
     * and the timestamp of the message. Be aware of the 15 minutes timeframe
     * when setting the time manually.
     *
     * @param string $service service name or restful action
     * @param string $method method or uri
     * @param string $nonce nonce of request
     * @param string $timestamp for authentication
     *
     * @return string encoded string
     */
    final public function getSignature($service, $method, $nonce, $timestamp)
    {
        $sign = $service . strtolower($method) . $timestamp;
        $sign .= $nonce;
        $hmac = $this->hmac($sign);
        if ($hmac) {
            return $hmac;
        }
        return false;
    }

    /**
     * Creates secured HMAC signature of the message parameters.
     * Uses the hash_hmac function to sign and crypt the message.
     *
     * @param string $mesgparams message to sign
     * @return string signed sha1 message hash
     */
    private function hmac($mesgparams)
    {
        $hmac = hash_hmac('sha1', utf8_encode($mesgparams), $this->secretKey);
        return $this->encodeBase64($hmac);
    }

    /**
     * Encodes the given message parameters with Base64.
     *
     * @param string $str string to encode
     * @return base 64 encoded string
     */
    private function encodeBase64($str)
    {
        $encode = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $encode .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($encode);
    }
}
