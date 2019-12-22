<?php
	
/*
	* Debugging class for PHP - Mark Leci 2016
	
	log errors to a database or local file
	send email for certain errors?
	http://php.net/manual/en/book.errorfunc.php
	http://php.net/manual/en/function.trigger-error.php
	http://php.net/manual/en/class.errorexception.php
	http://php.net/manual/en/function.set-error-handler.php
	
*/	

// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}
	
	
	
	
	?>