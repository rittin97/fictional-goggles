<?php

/*
    Development Exercise

      The following code is poorly designed and error prone. Refactor the objects below to follow a more SOLID design.
      Keep in mind the fundamentals of MVVM/MVC and Single-responsibility when refactoring.

      Further, the refactored code should be flexible enough to easily allow the addition of different display
        methods, as well as additional read and write methods.

      Feel free to add as many additional classes and interfaces as you see fit.

      Note: Please create a fork of the https://github.com/BrandonLegault/exercise repository and commit your changes
        to your fork. The goal here is not 100% correctness, but instead a glimpse into how you
        approach refactoring/redesigning bad code. Commit often to your fork.

*/


interface IReadWritePlayers {
    function readPlayers($filename = null);
    function writePlayer($player, $filename = null);
}

interface IDisplayPlayers {
    function display($filename = null);
}

class PlayersObjectArray implements IReadWritePlayers {

    private $playersArray;

    public function __construct() {
        $this->playersArray = [];
    }

    function readPlayers($filename = null) {
        $playerData = $this->getPlayerDataArray();

        if (is_string($playerData)) {
            $playerData = json_decode($playerData);
        }

        return $playerData;

    }

    function writePlayer($player, $filename = null) {
        $this->playersArray[] = $player;
    }

    function getPlayerDataArray() {

        $players = [];

        $jonas = new \stdClass();
        $jonas->name = 'Jonas Valenciunas';
        $jonas->age = 26;
        $jonas->job = 'Center';
        $jonas->salary = '4.66m';
        $players[] = $jonas;

        $kyle = new \stdClass();
        $kyle->name = 'Kyle Lowry';
        $kyle->age = 32;
        $kyle->job = 'Point Guard';
        $kyle->salary = '28.7m';
        $players[] = $kyle;

        $demar = new \stdClass();
        $demar->name = 'Demar DeRozan';
        $demar->age = 28;
        $demar->job = 'Shooting Guard';
        $demar->salary = '26.54m';
        $players[] = $demar;

        $jakob = new \stdClass();
        $jakob->name = 'Jakob Poeltl';
        $jakob->age = 22;
        $jakob->job = 'Center';
        $jakob->salary = '2.704m';
        $players[] = $jakob;

        return $players;
    }

}

class PlayersObjectJson implements IReadWritePlayers {

    private $playerJsonString;

    public function __construct() {
        $this->playerJsonString = null;
    }

    function readPlayers($filename = null) {
        $playerData = $this->getPlayerDataJson();

        if (is_string($playerData)) {
            $playerData = json_decode($playerData);
        }

        return $playerData;
    }

    function writePlayer($player, $filename = null) {
        $players = [];
        if ($this->playerJsonString) {
            $players = json_decode($this->playerJsonString);
        }
        $players[] = $player;
        $this->playerJsonString = json_encode($player);
    }

    function getPlayerDataJson() {
        $json = '[{"name":"Jonas Valenciunas","age":26,"job":"Center","salary":"4.66m"},{"name":"Kyle Lowry","age":32,"job":"Point Guard","salary":"28.7m"},{"name":"Demar DeRozan","age":28,"job":"Shooting Guard","salary":"26.54m"},{"name":"Jakob Poeltl","age":22,"job":"Center","salary":"2.704m"}]';
        return $json;
    }

}

class PlayersObjectFile implements IReadWritePlayers {

    function readPlayers($filename = null) {
        $playerData = $this->getPlayerDataFromFile($filename);

        if (is_string($playerData)) {
            $playerData = json_decode($playerData);
        }

        return $playerData;
    }

    function writePlayer($player, $filename = null) {
        $players = json_decode($this->getPlayerDataFromFile($filename));
        if (!$players) {
            $players = [];
        }
        $players[] = $player;
        file_put_contents($filename, json_encode($players));
    }

    function getPlayerDataFromFile($filename) {
        $file = file_get_contents($filename);
        return $file;
    }

}

class DisplayPlayersObjectRenderCli implements IDisplayPlayers {
    
    private $players = null;

    public function __construct($playersData) {
        $this->players = $playersData;
    }

    function display($filename = null) {
        echo "Current Players: \n";
        foreach ($this->players as $player) {

            echo "\tName: $player->name\n";
            echo "\tAge: $player->age\n";
            echo "\tSalary: $player->salary\n";
            echo "\tJob: $player->job\n\n";
        }
    }

}

class DisplayPlayersObjectRenderHtml implements IDisplayPlayers {
    
    private $players = null;

    public function __construct($playersData) {
        $this->players = $playersData;
    }

    function display($filename = null) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                li {
                    list-style-type: none;
                    margin-bottom: 1em;
                }
                span {
                    display: block;
                }
            </style>
        </head>
        <body>
        <div>
            <span class="title">Current Players</span>
            <ul>
                <?php foreach($this->players as $player) { ?>
                    <li>
                        <div>
                            <span class="player-name">Name: <?= $player->name ?></span>
                            <span class="player-age">Age: <?= $player->age ?></span>
                            <span class="player-salary">Salary: <?= $player->salary ?></span>
                            <span class="player-job">Job: <?= $player->job ?></span>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </body>
        </html>
        <?php
    }

}

// Created a controller to use MVC design pattern
class PlayersObjectController {

    // Business logic can be used here to use respective data type: Array, Json, File, etc. (I'm sticking to Array)
    private $playersObject, $displayPlayersObject, $displayType;

    public function __construct($renderType) {
        $this->displayType = $renderType;
    }

    public function getArrayData() {
        $playersObject = new PlayersObjectArray();
        $this->display($this->displayType, $playersObject);
    }

    public function getJsonData() {
        $playersObject = new PlayersObjectJson();
        $this->display($this->displayType, $playersObject);
    }

    public function getFileData($filename = null) {
        $playersObject = new PlayersObjectFile();
        $this->display($this->displayType, $playersObject, $filename);
    }

    private function display($displayType, $playersObject, $filename = null) {
        $playersData = $playersObject->readPlayers($filename);

        if ($displayType == 'cli') {
            $displayPlayersObject = new DisplayPlayersObjectRenderCli($playersData);
        }
        else if ($displayType == 'html') {
            $displayPlayersObject = new DisplayPlayersObjectRenderHtml($playersData);
        }
        else {
            echo "\tNot Supported!\n\n";
            return;
        }
        $displayPlayersObject->display();
    }
}

$playersObjectController = new PlayersObjectController('cli');

$playersObjectController->getFileData('playerdata.json');

$playersObjectController->getArrayData();

$playersObjectController->getJsonData();

?>