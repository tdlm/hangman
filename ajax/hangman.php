<?php
/**
 * This is the AJAX interface for front-end game interaction. This is the
 * gateway to communicate with the class.
 *
 * @date   : 11/9/13 10:18 PM
 * @author scott
 */

// Initiate session
session_start();

// Require classes and such
require_once '../lib/Tdlm/Hangman.php';
require_once '../lib/Tdlm/Hangman/Exception/GameRequirementException.php';

// Start new game or pick up where we left off
$hangman = new Tdlm\Hangman($_REQUEST, '../words.txt');