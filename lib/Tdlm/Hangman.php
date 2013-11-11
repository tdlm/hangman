<?php
/**
 * Hangman class. This class contains all the logic to make the
 * game run.
 *
 * @date   : 11/9/13 10:18 PM
 * @author scott
 */

namespace Tdlm;

class Hangman
{
	const HANGMAN_STATUS_PLAYING = 'playing';
	const HANGMAN_STATUS_LOST = 'lost';
	const HANGMAN_STATUS_WON = 'won';

	const HANGMAN_TOTAL_CHANCES = 7;

	private $_request = array();

	private $_dictionary = null;

	/**
	 * Hangman constructor
	 *
	 * @param $request
	 * @param $dictionary String location for dictionary file
	 */
	public function __construct($request, $dictionary)
	{
		try {

			// Set the request so we can act accordingly where needed
			$this->_setRequest($request);

			// Set the dictionary file name
			$this->_setDictionary($dictionary);

			$this->init();

		} catch (\GameRequirementException $e) {
			$this->_outputGameError('requirements', $e->getMessage());
		} catch (\Exception $e) {
			$this->_outputGameError('general', $e->getMessage());
		}
	}

	/**
	 * Init Method
	 */
	public function init()
	{
		if ($this->_hasRequest('reset')
			|| $this->_validateGameObject() == false
			|| $this->_checkStatus() != self::HANGMAN_STATUS_PLAYING
		) {
			$this->_resetTheGame();
			$this->_outputGameObject();
			return;
		}

		$this->_updateProgress();

		if ($this->_hasRequest('guess')) {
			$this->_guessLetter($this->_getRequest('guess'));

			$this->_updateProgress();
		}

		$this->_checkStatus();

		$this->_outputGameObject();
	}

	/**
	 * Prints JSON object with game error
	 *
	 * @param string $type
	 * @param string $error
	 */
	protected function _outputGameError($type = 'general', $error)
	{
		$output = array();
		$output['type'] = $type;
		$output['error'] = $error;

		print json_encode($output);

		exit;
	}

	/**
	 * Returns JSON object represent current game state
	 */
	protected function _outputGameObject()
	{
		$hangman = $_SESSION['hangman'];

		unset($_SESSION['hangman']['error']);

		if ($this->_checkStatus() == self::HANGMAN_STATUS_PLAYING) {
			unset($hangman['word']);
		}

		print json_encode($hangman);

		exit;
	}

	/**
	 * Guess Letter
	 *
	 * @param $letter
	 *
	 * @return bool
	 */
	protected function _guessLetter($letter)
	{
		$letter = strtolower($letter);

		if ($this->_checkStatus() != self::HANGMAN_STATUS_PLAYING) {
			// This should be an exception, but to keep it simple I'm going
			// to stay with sessions for now
			$_SESSION['hangman']['error'] = 'Please start another game to guess!';
			return false;
		} elseif (strlen($letter) > 1) {
			$_SESSION['hangman']['error'] = 'One character at a time, bub!';
			return false;
		} elseif (strlen($letter) < 1) {
			$_SESSION['hangman']['error'] = 'Please guess a letter!';
			return false;
		} elseif (preg_match('/[^a-z]/', $letter)) {
			$_SESSION['hangman']['error'] = 'You can only guess with letters from the alphabet!';
			return false;
		} elseif (in_array($letter, $_SESSION['hangman']['guesses'])) {
			$_SESSION['hangman']['error'] = 'You already guessed that letter. Please try another.';
			return false;
		} elseif (in_array($letter, $_SESSION['hangman']['word'])) {
			array_push($_SESSION['hangman']['guesses'], $letter);
			return true;
		} else {
			$_SESSION['hangman']['error'] = 'That letter was not found in the word.';
			array_push($_SESSION['hangman']['guesses'], $letter);
			return false;
		}
	}

	/**
	 * Check Status
	 *
	 * @return string
	 */
	protected function _checkStatus()
	{
		if (count(array_diff($_SESSION['hangman']['guesses'], $_SESSION['hangman']['word']))
			>= self::HANGMAN_TOTAL_CHANCES
		) {
			$_SESSION['hangman']['status'] = self::HANGMAN_STATUS_LOST;
		} elseif (!count(array_diff($_SESSION['hangman']['progress'], $_SESSION['hangman']['word']))) {
			$_SESSION['hangman']['status'] = self::HANGMAN_STATUS_WON;
		} else {
			$_SESSION['hangman']['status'] = self::HANGMAN_STATUS_PLAYING;
		}

		return $_SESSION['hangman']['status'];
	}

	/**
	 * Update the display word based on current set of guesses
	 */
	protected function _updateProgress()
	{
		$correctLetters = array_intersect($_SESSION['hangman']['guesses'], $_SESSION['hangman']['word']);
		$displayWord = $this->_underscoreArray($_SESSION['hangman']['word']);

		foreach ($_SESSION['hangman']['word'] as $index => $letter) {

			if (in_array($letter, $correctLetters)) {
				$displayWord[$index + 1] = $letter;
			}
		}

		$_SESSION['hangman']['progress'] = $displayWord;
	}

	/**
	 * Validate Game Object
	 *
	 * @return bool
	 */
	protected function _validateGameObject()
	{
		if (!isset($_SESSION['hangman'])
			|| !isset($_SESSION['hangman']['word'])
			|| !isset($_SESSION['hangman']['progress'])
			|| !isset($_SESSION['hangman']['chances'])
			|| !isset($_SESSION['hangman']['guesses'])
			|| !isset($_SESSION['hangman']['status'])
		) {
			return false;
		}

		return true;
	}

	/**
	 * Reset the game and store in session
	 *
	 * @param int $chances
	 */
	protected function _resetTheGame($chances = null)
	{
		$_SESSION['hangman'] = array();
		$_SESSION['hangman']['word'] = str_split(strtolower($this->_getRandomWordFromDictionary()));
		$_SESSION['hangman']['progress'] = $this->_underscoreArray($_SESSION['hangman']['word']);
		$_SESSION['hangman']['chances'] = is_null($chances) ? self::HANGMAN_TOTAL_CHANCES : $chances;
		$_SESSION['hangman']['guesses'] = array();
		$_SESSION['hangman']['status'] = self::HANGMAN_STATUS_PLAYING;
	}

	/**
	 * Reads from dictionary file and returns random word from it
	 *
	 * @todo Create alternative to SplFileObject to look up words
	 *
	 * @return bool|string
	 * @throws \GameRequirementException
	 * @throws \Exception
	 */
	protected function _getRandomWordFromDictionary()
	{
		if (!$this->_getDictionary()) {
			throw new \GameRequirementException('Cannot retrieve dictionary word: missing dictionary');
		}

		if (class_exists('SplFileObject')) {
			$dictionary = new \SplFileObject($this->_getDictionary());

			// Fast Line Count
			$i = 0;
			$fp = fopen($this->_getDictionary(), 'r');
			while ($chunk = fread($fp, 1024000)) {
				$i += substr_count($chunk, "\n");
			}

			// Seek random line
			$dictionary->seek(rand(0, $i));

			// Return current word at position
			return trim($dictionary->current());
		} else {
			throw new \GameRequirementException('Required: SplFileObject');
		}
	}

	/**
	 * Create underscored version of array
	 *
	 * @param array $word
	 *
	 * @return array
	 */
	protected function _underscoreArray(array $word)
	{
		return array_fill_keys(range(1, count($word)), '_');
	}

	/**
	 * Set Request from $_REQUEST
	 *
	 * @param array $request
	 *
	 * @return $this
	 */
	protected function _setRequest($request)
	{
		$this->_request = $request;

		return $this;
	}

	/**
	 * Does request have passed value?
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	protected function _hasRequest($key)
	{
		return (bool)isset($this->_request[$key]);
	}

	/**
	 * Get Request Array|String value
	 *
	 * @param string $key
	 * @param null   $default
	 *
	 * @return mixed
	 */
	protected function _getRequest($key = null, $default = null)
	{
		if (!is_null($key) && isset($this->_request[$key])) {
			return $this->_request[$key];
		} elseif (!is_null($key)) {
			return is_null($default) ? false : $default;
		}

		return $this->_request;
	}


	/**
	 * Set Dictionary Filename as String
	 *
	 * @param null $dictionary
	 *
	 *
	 * @return $this Hangman
	 * @throws \Exception
	 */
	protected function _setDictionary($dictionary)
	{
		if (!file_exists($dictionary)) {
			throw new \Exception('Unable to find text file for use as dictionary: ' . $dictionary);
		}

		$this->_dictionary = $dictionary;

		return $this;
	}

	/**
	 * Return Dictionary Filename
	 *
	 * @return null
	 */
	protected function _getDictionary()
	{
		return $this->_dictionary;
	}
}