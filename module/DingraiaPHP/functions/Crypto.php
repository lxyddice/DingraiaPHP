<?php
/**
 * DingTalkCrypto
 *
 * User: longfei.he <hlf513@gmail.com>
 * Date: 2018/1/12
 */

//修改前第三方库地址为https://github.com/hlf513/dingtalk-crypto
namespace Hlf\DingTalkCrypto;

class Exception extends \Exception {}

class Crypto
{
    private string $m_token;
    private string $m_encodingAesKey;
    private string $m_suiteKey;
    public function __construct($token, $encodingAesKey, $suiteKey)
    {
        $this->m_token = $token;
        $this->m_suiteKey = $suiteKey;

        if (strlen($encodingAesKey) != 43) {
            throw new Exception('Illegal Aes key');
        }
        $this->m_encodingAesKey = base64_decode($encodingAesKey.'=');
    }

    public function encryptMsg($text, $timeStamp=null, $nonce=null): string
    {
        try {
            $nonce = $nonce ?: $this->getRandomStr();
            $text = $nonce . pack("N", strlen($text)) . $text . $this->m_suiteKey;
            $iv = substr($this->m_encodingAesKey, 0, 16);
            $text = $this->pkcs7Pad($text);
            $encrypt = openssl_encrypt($text, 'AES-256-CBC', substr($this->m_encodingAesKey, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        } catch (\Exception $e) {
            throw new Exception('Encrypt AES error');
        }
        $timeStamp = $timeStamp ?: time();
        $timeStamp = strval($timeStamp);
        $signature = $this->getSignature($timeStamp, $nonce, $encrypt);

        return json_encode(array(
            "msg_signature" => $signature,
            "encrypt"       => $encrypt,
            "timeStamp"     => $timeStamp,
            "nonce"         => $nonce
        ));
    }

    public function decryptMsg($signature, $timeStamp, $nonce, $encrypt)
    {
        $timeStamp = $timeStamp ?: time();
        $verifySignature = $this->getSignature($timeStamp, $nonce, $encrypt);
        if ($verifySignature != $signature) {
            throw new Exception('Validate signature');
        }
        try {
            $iv = substr($this->m_encodingAesKey, 0, 16);
            $decrypted = openssl_decrypt($encrypt, 'AES-256-CBC', substr($this->m_encodingAesKey, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        } catch (\Exception $e) {
            throw new Exception('decrypt AES error');
        }
        try {
            $result = $this->pkcs7UnPad($decrypted);
            if (strlen($result) < 16) return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_corpid = substr($content, $xml_len + 4);
        } catch (\Exception $e) {
            throw new Exception('decrypt AES error');
        }
        if ($from_corpid != $this->m_suiteKey) {
            throw new Exception('Validate SuiteKey error: '.$from_corpid.' Verify: '.$this->m_suiteKey);
        }
        return $xml_content;
    }

    public function pkcs7Pad($data, $blockSize=16): string
    {
        $padding = $blockSize - (strlen($data) % $blockSize);
        return $data . str_repeat(chr($padding), $padding);
    }

    public function pkcs7UnPad($data): string
    {
        $padding = ord($data[strlen($data) - 1]);
        return substr($data, 0, -$padding);
    }

    public function getRandomStr(): string
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }

    protected function getSignature($timestamp, $nonce, $encrypt_msg): string
    {
        $array = array($encrypt_msg, $this->m_token, $timestamp, $nonce);
        sort($array, SORT_STRING);
        $str = implode($array);

        return sha1($str);
    }
}