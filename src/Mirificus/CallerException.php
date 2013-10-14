<?php
/**
 * Mirificus PHP Framework
 * @package Mirificus
 */

namespace Mirificus;

/**
 * This is the main exception to be thrown by any
 * method to indicate that the CALLER is responsible for
 * causing the exception.  This works in conjunction with Mirificus
 * error handling/reporting, so that the correct file/line-number is
 * displayed to the user.
 *
 * So for example, for a class that contains the method GetItemAtIndex($intIndex),
 * it is conceivable that the caller could call GetItemAtIndex(15), where 15 does not exist.
 * GetItemAtIndex would then thrown an IndexOutOfRangeException (which extends CallerException).
 * If the CallerException is not caught, then the Exception will be reported to the user.  The CALLER
 * (the script who CALLED GetItemAtIndex) would have that line highlighted as being responsible
 * for calling the error.
 *
 * The PHP default for exception reporting would normally say that the "throw Exception" line in GetItemAtIndex
 * is responsible for throwing the exception.  While this is technically true, in reality, it was the line that
 * CALLED GetItemAtIndex which is responsible.  In short, this allows for much cleaner exception reporting.
 *
 * On a more in-depth note, in general, suppose a method OuterMethod takes in parameters, and ends up passing those
 * parameters into ANOTHER method InnerMethod which could throw a CallerException.  OuterMethod is responsible
 * for catching and re-throwing the caller exception.  And when this is done, IncrementOffset() MUST be called on
 * the exception object, to indicate that OuterMethod's CALLER is responsible for the exception.
 *
 * So the code snippet to call InnerMethod by OuterMethod should look like:
 * 	function OuterMethod($mixValue) {
 * 		try {
 * 			InnerMethod($mixValue);
 * 		} catch (CallerException $objExc) {
 * 			$objExc->IncrementOffset();
 * 			throw $objExc;
 * 		}
 * 		// Do Other Stuff
 * 	}
 * Again, this will assure the user that the line of code responsible for the excpetion is properly being reported
 * by the Mirificus error reporting/handler.
 * @package Mirificus\CallerException
 */
class CallerException extends \Exception
{
    /** @var int $intOffset How many calls up the stack is the error? */
    private $intOffset;

    /** @var string $strTraceArray The stack trace. */
    private $strTraceArray;

    /**
     * Sets the Message for the exception.
     * @param string $strMessage The message to set for this exception.
     */
    public function setMessage($strMessage)
    {
        $this->message = $strMessage;
    }

    /**
     * The constructor of CallerExceptions.  Takes in a message string
     * as well as an optional Offset parameter (defaults to 1).
     * The Offset specificies how many calls up the call stack is responsible
     * for the exception.  By definition, when a CallerException is called,
     * at the very least the Caller of the most immediate function, which is
     * 1 up the call stack, is responsible.  So therefore, by default, intOffset
     * is set to 1.
     *
     * It is rare for intOffset to be set to an integer other than 1.
     *
     * Normally, the Offset would be altered by calls to IncrementOffset
     * at every step the CallerException is caught/rethrown up the call stack.
     * @param string $strMessage The Message of the exception.
     * @param int $intOffset The optional offset value (currently defaulted to 1).
     * @return CallerException the new exception
     */
    public function __construct($strMessage, $intOffset = 1)
    {
        parent::__construct($strMessage);
        $this->intOffset = $intOffset;
        $this->strTraceArray = debug_backtrace();

        $this->file = $this->strTraceArray[$this->intOffset]['file'];
        $this->line = $this->strTraceArray[$this->intOffset]['line'];
    }

    /**
     * Increments the offset and sets file/line accordingly.
     */
    public function IncrementOffset()
    {
        $this->intOffset++;
        $this->setFileAndLine();
    }

    /**
     * Decrements the offset and sets file/line accordingly.
     */
    public function DecrementOffset()
    {
        $this->intOffset--;
        $this->setFileAndLine();
    }

    /**
     * Sets the file and line based on offset.
     */
    protected function setFileAndLine()
    {
        $this->file = (array_key_exists('file', $this->strTraceArray[$this->intOffset])) ?
                $this->strTraceArray[$this->intOffset]['file'] : '';

        $this->line = (array_key_exists('line', $this->strTraceArray[$this->intOffset])) ?
                $this->strTraceArray[$this->intOffset]['line'] : '';
    }

    /**
     * Magic method: get
     * @param string $strName The name of the property to get.
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "Offset":
                return $this->intOffset;
            case "BackTrace":
                $objTraceArray = debug_backtrace();
                return (var_export($objTraceArray, true));
            case "TraceArray":
                return $this->strTraceArray;
        }
    }

}
