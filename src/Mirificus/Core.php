<?php

/**
 * @package Mirificus
 */
namespace Mirificus;

/**
 * This the the glue beween all Mirificus packages.
 * @package Mirificus\Core
 */
abstract class Core
{
    /**
     * The package version.
     * @var string $strVersion
     * @todo Make this dynamic.
     * @static
     * @access protected
     */
    protected static $strVersion = '0.1';

    /**
     * Holds a globally accessible DB adapter definition.
     * @var array $Database
     * @static
     */
    public static $Database = array();

	/**
	 * Set this in your application.
	 */
	public static $DocumentRoot = '/var/www';

	/**
	 * @var int $intStoredErrorLevel The level of error reporting.
	 */
	private static $intStoredErrorLevel = null;

    /**
     * Get the version.
     * @return string The package version.
     */
    public static function Version()
    {
        return self::$strVersion;
    }

	/**
	 * Temprorarily overrides the default error handling mechanism.  Remember to call
	 * RestoreErrorHandler to restore the error handler back to the default.
	 *
	 * @param string|null $strName The name of the new error handler function, or NULL if none
	 * @param integer $intLevel If an error handler function is defined, then the new error reporting level (if any)
	 * @throws CallerException
	 * @static
	 */
	public static function SetErrorHandler($strName, $intLevel = null)
	{
		if (!is_null(static::$intStoredErrorLevel)) {
			throw new CallerException('Error handler is already overridden. Call RestoreErrorHandler before calling SetErrorHandler again.');
		}
		if (!$strName) {
			// No Error Handling is wanted -- simulate a "On Error, Resume" type of functionality
			set_error_handler('MirificusHandleError', 0);
			static::$intStoredErrorLevel = error_reporting(0);
		} else {
			set_error_handler($strName, $intLevel);
			static::$intStoredErrorLevel = -1;
		}
	}

	/**
	 * Restores the temporarily overridden default error handling mechanism back to the default.
	 * @see SetErrorHandler
	 * @throws CallerException
	 * @static
	 */
	public static function RestoreErrorHandler()
	{
		if (is_null(static::$intStoredErrorLevel))
			throw new CallerException('Error handler is not currently overridden.');
		if (static::$intStoredErrorLevel != -1) {
			error_reporting(static::$intStoredErrorLevel);
		}
		restore_error_handler();
		static::$intStoredErrorLevel = null;
	}

    /**
	 * Same as mkdir() but correctly implements directory recursion.
	 * At its core, it will use mkdir().
	 * This method does no special error handling.  If you want to use special error handlers,
	 * be sure to set that up BEFORE calling MakeDirectory().
	 *
	 * @param string $strPath Actual path of the directoy you want to create.
	 * @param integer $intMode Optional mode (permissions) e.g 644.
	 * @return bool The return flag from mkdir().
	 */
	public static function MakeDirectory($strPath, $intMode = null)
	{
		if (is_dir($strPath)) {
			// Directory Already Exists
			return true;
		}

		// Check to make sure the parent(s) exist, or create if not
		if (!self::MakeDirectory(dirname($strPath), $intMode)) {
			return false;
		}

		if (PHP_OS != "Linux") {
			// Create the current node/directory, and return its result
			$blnReturn = mkdir($strPath);

			if ($blnReturn && !is_null($intMode)) {
				// Manually CHMOD to $intMode (if applicable)
				// mkdir doesn't do it for mac, and this will error on windows
				// Therefore, ignore any errors that creep up
				static::SetErrorHandler(null);
				chmod($strPath, $intMode);
				static::RestoreErrorHandler();
			}
		} else {
			$blnReturn = mkdir($strPath, $intMode);
		}

		return $blnReturn;
	}
}
