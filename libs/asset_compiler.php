<?php
App::import('Lib', 'AssetCompress.AssetScanner');
App::import('Lib', 'AssetCompress.AssetFilterCollection');

/**
 * Compiles a set of assets together, and applies filters.
 * Forms the center of AssetCompress
 *
 * @package asset_compress
 */
class AssetCompiler {

	protected $_Config;

	function __construct(AssetConfig $config) {
		$this->_Config = $config;
	}

/**
 * Generate a compiled asset, with all the configured filters applied.
 *
 * @param string $target The name of the build target to generate.
 * @return The processed result of $target and it dependencies. 
 */
	public function generate($build) {
		$ext = $this->_Config->getExt($build);
		$this->_Scanner = $this->_makeScanner($this->_Config->paths($ext));
		$this->filters = $this->_makeFilters($ext, $build);

		$output = '';
		$files = $this->_Config->files($build);
		foreach ($files as $file) {
			$file = $this->_findFile($file);
			$content = file_get_contents($file);
			$content = $this->filters->input($file, $content);
			$output .= $content;
		}
		if (!$this->_Config->debug) {
			$content = $this->filters->output($build, $content);
		}
		return trim($output);
	}

/**
 * Factory method for AssetScanners
 *
 * @param string $ext The extension you want a scanner for.
 * @return AssetScanner
 */
	protected function _makeScanner($paths) {
		return new AssetScanner($paths);
	}

/**
 * Factory method for AssetFilterCollection
 *
 * @param string $ext The extension An array of filters to put in the collection
 */
	protected function _makeFilters($ext, $target) {
		$config = array(
			'paths' => $this->_Config->paths($ext),
			'target' => $target
		);
		$filters = $this->_Config->filters($ext, $target);
		$filterSettings = $this->_Config->filterConfig($filters);
		return new AssetFilterCollection($filters, $config, $filterSettings);
	}

/**
 * Locate a file using the Scanner.
 *
 * @return string The full filepath to the file.
 * @throws Exception when files can't be found.
 */
	protected function _findFile($object) {
		$filename = $this->_Scanner->find($object);
		if (!$filename) {
			throw new Exception(sprintf('Could not locate file "%s"', $object));
		}
		return $filename;
	}
}
