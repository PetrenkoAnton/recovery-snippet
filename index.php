<?php

require_once 'vendor/autoload.php';

use Virgil\CryptoImpl\VirgilCrypto;

$inputPassword = "qwerty123456";
$privateKeyPassword = 'passw0rd'; // Can be blank

// Using Virgil SDK, generate a Recovery Keypair
$virgilCrypto = new VirgilCrypto();
$keyPair = $virgilCrypto->generateKeys();

$privateKey = $keyPair->getPrivateKey();
$publicKey = $keyPair->getPublicKey();

// Store the Public Key in your database and save the Private Key securely on another external device. Use PEM-format!
// Note: you won’t be able to recover your Private Key so it is crucial not to lose it

// Your database should contain columns for uid, pure records and user passwords hashes, encrypted with the newly generated public key.
// The last column will be necessary for the recovery process

$privateKeyExportDER = $virgilCrypto->exportPrivateKey($privateKey, $privateKeyPassword);
$publicKeyExportDER = $virgilCrypto->exportPublicKey($publicKey);

$privateKeyPEM = \VirgilKeyPair::privateKeyToPEM($privateKeyExportDER, $privateKeyPassword);
$publicKeyPEM = \VirgilKeyPair::publicKeyToPEM($publicKeyExportDER);

// Encrypt user password hashes with the generated Recovery Key and save them to the column mentioned in the
// previous step

$privateKeyDER = \VirgilKeyPair::privateKeyToDER($privateKeyPEM, $privateKeyPassword);
$privateKeyImportDER = $virgilCrypto->importPrivateKey($privateKeyDER, $privateKeyPassword);

$publicKeyDER = \VirgilKeyPair::publicKeyToDER($publicKeyPEM);
$publicKeyImportDER = $virgilCrypto->importPublicKey($publicKeyDER);

$encrypted = $virgilCrypto->encrypt($inputPassword, [$publicKeyImportDER]);
$encryptedBase64 = base64_encode($encrypted);

// If you want to move away from the Pure technology without losing user passwords, insert the Recovery Key and call
// the following function:
// This will decrypt the encrypted password hashes. After this you can remove the PHE data and continue using user password hashes

$decrypted = $virgilCrypto->decrypt($encrypted, $privateKeyImportDER);

// Check status and the output of the variables

printf("Recovery status: %s\n%s", $inputPassword==$decrypted ? "Success" : "Failed", "<hr>");
var_dump($privateKeyPEM, $publicKeyPEM, $encrypted, $encryptedBase64, $decrypted);
die;