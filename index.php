<?php

require_once 'vendor/autoload.php';

use Virgil\CryptoImpl\VirgilCrypto;

$inputPassword = "qwerty123456";
$privateKeyPassword = 'passw0rd';

$virgilCrypto = new VirgilCrypto();
$keyPair = $virgilCrypto->generateKeys();

$privateKey = $keyPair->getPrivateKey();
$publicKey = $keyPair->getPublicKey();

$privateKeyExportDER = $virgilCrypto->exportPrivateKey($privateKey, $privateKeyPassword);
$publicKeyExportDER = $virgilCrypto->exportPublicKey($publicKey);

$privateKeyPEM = \VirgilKeyPair::privateKeyToPEM($privateKeyExportDER, $privateKeyPassword);
$publicKeyPEM = \VirgilKeyPair::publicKeyToPEM($publicKeyExportDER);

$privateKeyDER = \VirgilKeyPair::privateKeyToDER($privateKeyPEM, $privateKeyPassword);
$privateKeyImportDER = $virgilCrypto->importPrivateKey($privateKeyDER, $privateKeyPassword);

$publicKeyDER = \VirgilKeyPair::publicKeyToDER($publicKeyPEM);
$publicKeyImportDER = $virgilCrypto->importPublicKey($publicKeyDER);

$encrypted = $virgilCrypto->encrypt($inputPassword, [$publicKeyImportDER]);
$encryptedBase64 = base64_encode($encrypted);

$decrypted = $virgilCrypto->decrypt($encrypted, $privateKeyImportDER);

printf("Recovery status: %s\n%s", $inputPassword==$decrypted ? "Success" : "Failed", "<hr>");

var_dump($privateKeyPEM, $publicKeyPEM, $encrypted, $encryptedBase64, $decrypted);
die;