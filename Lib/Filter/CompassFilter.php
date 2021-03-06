<?php
App::uses('AssetFilter', 'AssetCompress.Lib');

/**
 * Pre-processing filter that adds support for SCSS files.
 *
 * Requires ruby and sass rubygem to be installed
 *
 * @see http://sass-lang.com/
 */
class CompassFilter extends AssetFilter {

	protected $_settings = array(
		'ext' => '.scss',
		'compass' => '/usr/bin/compass'
	);

/**
 * Runs Compass compiler against any files that match the configured extension.
 *
 * @param string $filename The name of the input file.
 * @param string $input The content of the file.
 * @return string
 */
	public function input($filename, $input) {
		if (substr($filename, strlen($this->_settings['ext']) * -1) !== $this->_settings['ext']) {
			return $input;
		}
		$bin = $this->_settings['compass'] . ' ' . $filename;
		$return = $this->_runCmd($bin, '');
		return $return;
	}

}
