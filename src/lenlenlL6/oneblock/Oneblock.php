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
*/

namespace lenlenlL6\oneblock;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use lenlenlL6\oneblock\OneblockManager;
use lenlenlL6\oneblock\event\CreateIslandEvent;
use lenlenlL6\oneblock\event\DeleteIslandEvent;
use lenlenlL6\oneblock\event\HomeEvent;
use lenlenlL6\oneblock\event\AddFriendEvent;
use lenlenlL6\oneblock\event\RemoveFriendEvent;
use lenlenlL6\oneblock\event\TeleportEvent;
use lenlenlL6\oneblock\event\TierChangeEvent;
use lenlenlL6\oneblock\task\CreateIslandTask;
use lenlenlL6\oneblock\task\tier\Tier1Task;

class Oneblock extends PluginBase implements Listener {
  
  public $prefix = "§r§l§a[§b• §eONE BLOCK §b•§a]";
  
  public $maxtier = 10;
  
  private OneblockManager $manager;
  
  const AUTHORS = "lenlenlL6 and DoraOtaku"; //DON'T CHANGE THIS
  const VERSION = "0.0.2"; //DON'T CHANGE THIS
  const API = "4.0.0"; //DON'T CHANGE THIS
  
  public function onEnable(){
    $this->getLogger()->info("
    ___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
  ");
  $this->getLogger()->info("Authors: " . self::AUTHORS);
  $this->getLogger()->info("Plugin Version: " . self::VERSION);
  $this->getLogger()->info("Plugin Api: " . self::API);
  $this->getServer()->getPluginManager()->registerEvents($this, $this);
  $this->saveResources("lang.yml");
  $this->lang = new Config($this->getDataFolder() . "lang.yml", Config::YAML);
  $this->tier = new Config($this->getDataFolder() . "tier.yml", Config::YAML);
  $this->level = new Config($this->getDataFolder() . "level.yml", Config::YAML);
  $this->island = new Config($this->getDataFolder() . "islands.yml", Config::YAML);
  }
  
  public function onDisable(){
    $this->getLogger()->info("
    ___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
  ");
  $this->getLogger()->info("Authors: " . self::AUTHORS);
  $this->getLogger()->info("Plugin Version: " . self::VERSION);
  $this->getLogger()->info("Plugin Api: " . self::API);
  $this->getManager()->saveAll();
  }
  
  public function onCommand(CommandSender $player, Command $cmd, String $label, array $args): bool{
    switch($cmd->getName()){
      case "oneblock":
        if($player instanceof Player){
          $this->MenuForm($player);
        }else{
          $player->sendMessage($this->prefix . "§c This command just work in game !");
        }
        break;
    }
    return true;
  }
  //Main Form
  public function MenuForm(Player $player){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function(Player $player, int $data = null){
      
      if($data === null){
        return true;
      }
      switch($data){
        case 0:
          if(!$this->getManager()->isHaveIsland($player)){
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "mw create oneblock-" . $player->getName() . " 0 VOID");
            $this->getScheduler()->scheduleDelayedTask(new CreateIslandTask($this, $player), 5*20);
            $msg = $this->lang->get("WAITING_CREATE_ISLAND");
            $player->sendMessage($this->prefix . " $msg");
            (new CreateIslandEvent($this, $player))->call();
          }else{
            $msg = $this->lang->get("ALREADY_HAVE_ISLAND");
            $player->sendMessage($this->prefix . " $msg");
          }
          break;
          
          case 1:
            $this->ManageIslandForm($player);
            break;
      }
    });
    $form->setTitle("§l§a【 §bONE BLOCK MENU §a】");
    $form->addButton("§l§a• CREATE ISLAND •", 1, "https://www.vhv.rs/dpng/d/453-4533087_how-to-create-png-images-create-icon-transparent.png");
    $form->addButton("§l§a• MANAGE YOUR ISLAND •", 1, "https://png.pngtree.com/png-clipart/20190619/original/pngtree-file-manager-glyph-black-icon-png-image_4008309.jpg");
    return $form;
  }
  
  public function ManageIslandForm(Player $player){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function(Player $player, int $data = null){
      
      if($data === null){
        $this->MenuForm($player);
        return true;
      }
    });
    
    return $form;
  }
  public function getManager() : OneblockManager{
    return $this->manager;
  }
}
