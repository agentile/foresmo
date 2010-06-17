<?php
/**
 * Foresmo_Captcha_Word
 * Word Captcha Adapter
 * TODO: implement TTL
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class Foresmo_Captcha_Word extends Foresmo_Captcha {

    protected $_Foresmo_Captcha_Word = array(
        'word_length' => 8,
        'vowels' => array('a', 'e', 'i', 'o', 'u', 'y'),
        'consonants' => array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm' ,'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z'),
        'numbers' => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
        'use_numbers' => false,
    );

    protected $_word = null;

    protected $_session_key = null;

    /**
     * _postConstruct
     *
     */
    protected function _postConstruct()
    {
        parent::_postConstruct();
    }

    /**
     * getWordLength
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function getWordLength()
    {
        return $this->_config['word_length'];
    }

    /**
     * setWordLength
     * Insert description here
     *
     * @param $length
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function setWordLength($length)
    {
        if (!is_int($length) || $length != (int) $length) {
            return false;
        }

        $this->_config['word_length'] = (int) $length;
    }

    /**
     * getVowels
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function getVowels()
    {
        return $this->_config['vowels'];
    }

    /**
     * setVowels
     * Insert description here
     *
     * @param $vowels
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function setVowels($vowels)
    {
        $this->_config['vowels'] = (array) $vowels;
    }

    /**
     * getConsonants
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function getConsonants()
    {
        return $this->_config['consonants'];
    }

    /**
     * setConsonants
     * Insert description here
     *
     * @param $consonants
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function setConsonants($consonants)
    {
        $this->_config['consonants'] = (array) $consonants;
    }

    /**
     * getNumbers
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function getNumbers()
    {
        return $this->_config['numbers'];
    }

    /**
     * setNumbers
     * Insert description here
     *
     * @param $numbers
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function setNumbers($numbers)
    {
        $this->_config['numbers'] = (array) $numbers;
    }

    /**
     * getUseNumbers
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function getUseNumbers()
    {
        return $this->_config['use_numbers'];
    }

    /**
     * setUseNumbers
     * Insert description here
     *
     * @param $bool
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function setUseNumbers($bool)
    {
        if (!is_bool($bool)) {
            return false;
        }

        $this->_config['use_numbers'] = $bool;
    }

    /**
     * generate
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function generate()
    {
        $this->_setSessionKey();
        $this->_generateWord();
        $this->_session->set($this->_getSessionKey(), $this->_getWord());

        return array('key' => $this->_getSessionKey(), 'word' => $this->_getWord());
    }

    /**
     * isValid
     * Insert description here
     *
     * @param $info
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function isValid($info)
    {
        if (!is_array($info) || !isset($info['key']) || !isset($info['word'])) {
            return false;
        }

        if ($this->_session->get($info['key'], false) !== false
            && $this->_session->get($info['key']) == $info['word']) {
            return true;
        }

        return false;
    }

    /**
     * _setSessionKey
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _setSessionKey()
    {
        $this->_session_key = md5(uniqid(mt_rand(), TRUE));
    }

    /**
     * _getSessionKey
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _getSessionKey()
    {
        return $this->_session_key;
    }

    /**
     * _getWord
     * Insert description here
     *
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    protected function _getWord()
    {
        return $this->_word;
    }

    /**
     * Generate new random word
     *
     * @return string
     */
    protected function _generateWord()
    {
        $word = '';
        $word_length  = $this->getWordLength();
        $step = 2;
        $use_numbers = $this->getUseNumbers();
        $vowels = $this->getVowels();
        $consonants = $this->getConsonants();

        if ($use_numbers) {
            $numbers = $this->getNumbers();
        }

        for ($i = 0; $i < $word_length; $i = $i + 2) {
            $consonant = $consonants[array_rand($consonants)];
            $vowel = $vowels[array_rand($vowels)];

            if ($use_numbers) {
                $number = $numbers[array_rand($numbers)];
                if (rand(0,1)) {
                    $vowel = $number;
                }
            }

            $word .= $consonant . $vowel;
        }

        if (strlen($word) > $word_length) {
            $word = substr($word, 0, $word_length);
        }

        $this->_word = $word;
    }
}