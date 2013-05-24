del-bitcoin
===========

Zend Framework 2 Bitcoin module using the Blockchain API

So whats in v0.1?
Service for connecting to Blockchain API
View Helper to display balances

Example use - Wallet Balance View Helper
	$this->walletBalance('1Cu6X3c716CCKU3Bi2jfHv8kZ2QCor8uXm');

Service commands:
public function getWalletBalance($address);
public function sendBitcoin($from,$to,$amount,$fee = 50000, $note = null);
public function sendMultipleTransactions($from,$recipients,$fee = 50000, $note = null);
public function listWalletAddresses();
public function generateNewAddress($label = null);
public function archiveAddress($address);
public function unarchiveAddress($address);
public function autoConsolidateAddresses($days);