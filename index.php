<?php

require_once 'vendor/autoload.php';

use Virgil\CryptoImpl\VirgilCrypto;

$inputPassword = "qwerty123456";
$privateKeyPassword = 'passw0rd'; // Can be blank

// Generate keypair:
$virgilCrypto = new VirgilCrypto();
$keyPair = $virgilCrypto->generateKeys();

$privateKey = $keyPair->getPrivateKey();
$publicKey = $keyPair->getPublicKey();

// Store exported keys:
$privateKeyExported = $virgilCrypto->exportPrivateKey($privateKey, $privateKeyPassword);
$publicKeyExported = $virgilCrypto->exportPublicKey($publicKey);

// Import keys:
$privateKeyImported = $virgilCrypto->importPrivateKey($privateKeyExported, $privateKeyPassword);
$publicKeyImported = $virgilCrypto->importPublicKey($publicKeyExported);

// Encrypt:
$encrypted = $virgilCrypto->encrypt($inputPassword, [$publicKeyImported]);
$encryptedBase64 = base64_encode($encrypted);

// Decrypt:
$decrypted = $virgilCrypto->decrypt($encrypted, $privateKeyImported);

// For dev:
// Check status and the output of the variables
printf("Recovery status: %s\n%s", $inputPassword==$decrypted ? "Success" : "Failed", "<hr>");
var_dump($encrypted, $encryptedBase64, $decrypted);
die;