hangman
=======

This is a very simple version of hangman. The back-end logic has been separated completely from the front-end logic. Currently, the game uses a normal PHP session to remember each person and their game.

Installation
=======

This requires PHP 5.4+ to run properly.

To install:

    git clone git@github.com:tdlm/hangman.git

Once you've copied the directory to your webroot, you'll have to set up a local hostname in your web server of choice.

To run using the built-in PHP web server:

    cd ~/Sites/hangman/
    php -S localhost:9000

You can then point your browser to http://localhost:9000/ and the game should work immediately.

About
=======

This was written by [Scott Weaver](http://scottmw.com)