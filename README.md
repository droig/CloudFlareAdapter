CloudFlareAdapter
=================

A PHP tool to chage a DNS record for CloudFlare.


How to use:

$adapter = new CloudFlareAdapter('44cbd278d1333059b874ad54741e782b', 'foo@bar.com');

if ( $adapter->editRecord('bar.com', 'A', 'foo.bar.com', '1.2.3.4') ) {
	//success actions
} else {
	//error actions
}
