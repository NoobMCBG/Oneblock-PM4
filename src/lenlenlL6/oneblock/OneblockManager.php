<?php
/*
___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
 
 ((/)) An upgrade of oneblock pm3 made by lenlenlL6 and Dora.
 ((/)) If you have problems with the plugin, contact me.
 ---> Facebook: https://www.facebook.com/profile.php?id=100071316150096
 ---> Github: https://github.com/lenlenlL6
 ((/)) Copyright by lenlenlL6 and Dora.
 
 
 
 
 Manager
*/

namespace lenlenlL6\oneblock;

use pocketmine\player\Player;
use pocketmine\Server;
use lenlenlL6\oneblock\Oneblock;

class OneblockManager{
  
  private Oneblock $oneblock;
  
  public function isHaveIsland(Player $player) : bool{
    return $this->getServer()->getWorldManager()->isWorldGenerated("oneblock-" . $player->getName());
  }
  
  public function getTier(Player $player) : int{
    return $this->oneblock->tier->get($player->getName());
  }
  
  public function getLevelTier(Player $player) : int{
    return $this->oneblock->level->get($player->getName());
  }
  
  public function getMaxTier() : int{
    return $this->oneblock->maxtier;
  }
  
  public function saveAll() : void{
$this->oneblock->tier->save();
$this->oneblock->level->save();
$this->oneblock->island->save();
}