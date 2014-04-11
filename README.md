CloudFlareAdapter
=================

A PHP class to manage a CloudFlare DNS record.


How to use:

$adapter = new CloudFlareAdapter('44cbd278d1333059b874ad54741e782b', 'foo@bar.com');

if ( $adapter->editRecord('bar.com', 'A', 'foo.bar.com', '1.2.3.4') ) {
	//success actions
} else {
	echo $adapter->getError();
	//more error actions
}
